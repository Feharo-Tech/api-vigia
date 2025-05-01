<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Api;
use App\Models\Tag;

class ApiListCard extends Component
{
    public $apis;
    public $allTags;
    public string $search = '';
    public string $statusFilter = '';

    public string $notifyFilter = '';
    public string $monitoringStatus = '';
    public string $tagFilter = '';

    public function mount()
    {
        $this->loadData();
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    public function updatedStatusFilter()
    {
        $this->loadData();
    }

    public function updatedNotifyFilter()
    {
        $this->loadData();
    }


    public function updatedMonitoringStatus()
    {
        $this->loadData();
    }

    public function updatedTagFilter()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $query = Api::visibleToUser(auth()->user())
                    ->with('latestStatusCheck', 'tags')
                    ->orderByDesc('id');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter === 'ativo') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inativo') {
            $query->where('is_active', false);
        }

        if ($this->notifyFilter === 'ativo') {
            $query->where('should_notify', true);
        } elseif ($this->notifyFilter === 'inativo') {
            $query->where('should_notify', false);
        }

        if ($this->monitoringStatus === 'ativo') {
            $query->whereHas('latestStatusCheck', fn ($q) => $q->where('success', true));
        } elseif ($this->monitoringStatus === 'inativo') {
            $query->whereHas('latestStatusCheck', fn ($q) => $q->where('success', false));
        } elseif ($this->monitoringStatus === 'nunca') {
            $query->whereDoesntHave('latestStatusCheck');
        }

        if ($this->tagFilter) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $this->tagFilter));
        }

        $this->apis = $query->get();
        $this->allTags = Tag::all();
    }

    public function isAnyFilterActive()
    {
        return !empty($this->search) ||
               !empty($this->statusFilter) ||
               !empty($this->notifyFilter) ||
               !empty($this->monitoringStatus) ||
               !empty($this->tagFilter);
    }

    public function render()
    {
        return view('livewire.api-list-card', [
            'isAnyFilterActive' => $this->isAnyFilterActive(),
        ]);
    }
}

