<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Api;
use Carbon\Carbon;

class ResponseTimeCard extends Component
{
    public $responseTimeData = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->responseTimeData = Api::query()
            ->with(['statusChecks' => function($query) {
                $query->where('created_at', '>=', now()->subDays(30))
                      ->where('success', true);
            }])
            ->get()
            ->map(function($api) {
                $successfulChecks = $api->statusChecks;
                $avgResponseTime = $successfulChecks->avg('response_time') * 1000;
                $lastCheck = $api->statusChecks->last();
                
                return [
                    'id' => $api->id,
                    'name' => $api->name,
                    'avg_response_time' => $avgResponseTime ? round($avgResponseTime, 2) : null,
                    'last_response_time' => $lastCheck ? round($lastCheck->response_time * 1000, 2) : null,
                    'last_status' => $lastCheck ? $lastCheck->success : null,
                    'checks_count' => $successfulChecks->count(),
                    'color' => $this->getResponseTimeColor($avgResponseTime)
                ];
            })
            ->filter(fn($api) => !is_null($api['avg_response_time']))
            ->sortByDesc('avg_response_time')
            ->values()
            ->toArray();
    }

    protected function getResponseTimeColor($responseTime)
    {
        if (is_null($responseTime)) return '#9CA3AF';
        
        return $responseTime <= 200 ? '#22C55E' : 
              ($responseTime <= 500 ? '#F59E0B' : 
              '#EF4444');                         
    }

    public function render()
    {
        return view('livewire.response-time-card');
    }
}