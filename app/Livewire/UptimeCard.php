<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Api;
use Carbon\Carbon;

class UptimeCard extends Component
{
    public $uptimeData = [];
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
        
        $this->uptimeData = Api::query()
            ->where('is_active', true)
            ->with(['latestStatusCheck'])
            ->withCount([
                'statusChecks as total_checks' => function($query) use ($dateRange) {
                    $query->where('created_at', '>=', $dateRange['start']);
                    if (isset($dateRange['end'])) {
                        $query->where('created_at', '<=', $dateRange['end']);
                    }
                },
                'statusChecks as successful_checks' => function($query) use ($dateRange) {
                    $query->where('success', true)
                        ->where('created_at', '>=', $dateRange['start']);
                    if (isset($dateRange['end'])) {
                        $query->where('created_at', '<=', $dateRange['end']);
                    }
                }
            ])
            ->get()
            ->map(function($api) {
                $uptime = $api->total_checks > 0 
                    ? round(($api->successful_checks / $api->total_checks) * 100, 2) 
                    : 100;

                return [
                    'id' => $api->id,
                    'name' => $api->name,
                    'uptime' => $uptime,
                    'last_status' => $api->latestStatusCheck?->success,
                    'checks_count' => $api->total_checks,
                    'color' => $this->getUptimeColor($uptime),
                    'last_check' => $api->latestStatusCheck?->created_at
                ];
            })
            ->sortBy('uptime')
            ->values()
            ->toArray();
    }

    protected function getDateRange()
    {
        $now = Carbon::now();
        
        return match ($this->selectedPeriod) {
            '1h' => ['start' => $now->copy()->subHour()],
            '3h' => ['start' => $now->copy()->subHours(3)],
            '12h' => ['start' => $now->copy()->subHours(12)],
            '24h' => ['start' => $now->copy()->subHours(24)],
            '3d' => ['start' => $now->copy()->subDays(3)],
            '7d' => ['start' => $now->copy()->subDays(7)],
            '15d' => ['start' => $now->copy()->subDays(15)],
            '30d' => ['start' => $now->copy()->subDays(30)],
            default => ['start' => $now->copy()->subHours(24)],
        };
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