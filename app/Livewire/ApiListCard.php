<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Api;

class ApiListCard extends Component
{
    public $apis;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->apis = Api::visibleToUser(auth()->user())
                            ->with('latestStatusCheck','tags')
                            ->orderByDesc('id')
                            ->get();
    }

    public function render()
    {
        return view('livewire.api-list-card');
    }
}
