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
        $this->apis = Api::where(function($query) {
                if (auth()->user()->is_admin) {
                    return;
                }
                $query->where('is_active', true);
            })
            ->with('latestStatusCheck','tags')
            ->orderByDesc('id')
            ->get();
    }

    public function render()
    {
        return view('livewire.api-list-card');
    }
}
