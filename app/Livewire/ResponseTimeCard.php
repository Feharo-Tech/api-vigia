<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Api;
use Carbon\Carbon;

class ResponseTimeCard extends Component
{
    public $responseTimeData = [];
    public $selectedPeriod = '30d';
    public $availablePeriods = [
        '1h' => '1 hora',
        '3h' => '3 horas',
        '12h' => '12 horas',
        '24h' => '24 horas',
        '3d' => '3 dias',
        '7d' => '7 dias',
        '15d' => '15 dias',
        '30d' => '30 dias',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function updatedSelectedPeriod($period)
    {
        $this->selectedPeriod = $period;
        $this->loadData();
    }

    public function loadData()
    {
        $dateRange = $this->getDateRange();
        
        $this->responseTimeData = Api::query()
            ->with(['statusChecks' => function($query) use ($dateRange) {
                $query->where('created_at', '>=', $dateRange['start'])
                      ->where('success', true);
                if (isset($dateRange['end'])) {
                    $query->where('created_at', '<=', $dateRange['end']);
                }
            }])
            ->where('is_active', true)
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
                    'color' => $this->getResponseTimeColor($avgResponseTime),
                    'last_check' => $lastCheck?->created_at
                ];
            })
            ->filter(fn($api) => !is_null($api['avg_response_time']))
            ->sortByDesc('avg_response_time')
            ->values()
            ->toArray();
    }

    protected function getDateRange()
    {
        $now = Carbon::now();
        
        return match ($this->selectedPeriod) {
            '1h' => ['start' => $now->subHour()],
            '3h' => ['start' => $now->subHours(3)],
            '12h' => ['start' => $now->subHours(12)],
            '24h' => ['start' => $now->subHours(24)],
            '1d' => ['start' => $now->subDay()->startOfDay(), 'end' => $now->endOfDay()],
            '3d' => ['start' => $now->subDays(3)],
            '7d' => ['start' => $now->subDays(7)],
            '15d' => ['start' => $now->subDays(15)],
            '30d' => ['start' => $now->subDays(30)],
            default => ['start' => $now->subDays(30)],
        };
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