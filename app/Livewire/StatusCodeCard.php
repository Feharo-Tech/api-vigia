<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ApiStatusCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StatusCodeCard extends Component
{
    public $statusCodes = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $apiIds = Auth::user()->visibleApis()->pluck('id');

        $this->statusCodes = ApiStatusCheck::whereIn('api_id', $apiIds)
            ->select('status_code', DB::raw('count(*) as count'))
            ->groupBy('status_code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status_code => $item->count];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.status-code-card');
    }
}