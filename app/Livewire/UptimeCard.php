<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; 
use App\Models\Api;
use Carbon\Carbon;

class UptimeCard extends Component
{
    public $uptimeData = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->uptimeData = Api::query()
            ->with(['statusChecks' => function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            }])
            ->where('is_active', true)
            ->get()
            ->map(function($api) {
                $totalChecks = $api->statusChecks->count();
                $successfulChecks = $api->statusChecks->where('success', true)->count();
                $uptime = $totalChecks > 0 ? round(($successfulChecks / $totalChecks) * 100, 2) : 100;

                return [
                    'id' => $api->id,
                    'name' => $api->name,
                    'uptime' => $uptime,
                    'last_status' => $api->statusChecks->last()?->success,
                    'checks_count' => $totalChecks,
                    'color' => $this->getUptimeColor($uptime)
                ];
            })
            ->sortBy('uptime')
            ->values() 
            ->toArray();
    }

    protected function getUptimeColor($percentage)
    {
        return $percentage >= 99 ? '#22C55E' : 
            ($percentage >= 95 ? '#F59E0B' : '#EF4444');
    }

    public function render()
    {
        return view('livewire.uptime-card');
    }
}