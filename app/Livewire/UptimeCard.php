<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Api;
use Carbon\Carbon;
use Livewire\Attributes\On;

class UptimeCard extends Component
{
    public $uptimeData = [];
    public $selectedPeriod = '24h';
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
                'statusChecks as successful_checks_count' => function($query) use ($dateRange) {
                    $query->where('success', true)
                        ->where('created_at', '>=', $dateRange['start']);
                    if (isset($dateRange['end'])) {
                        $query->where('created_at', '<=', $dateRange['end']);
                    }
                },
                'statusChecks as total_checks_count' => function($query) use ($dateRange) {
                    $query->where('created_at', '>=', $dateRange['start']);
                    if (isset($dateRange['end'])) {
                        $query->where('created_at', '<=', $dateRange['end']);
                    }
                }
            ])
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
                    'color' => $this->getUptimeColor($uptime),
                    'last_check' => $api->statusChecks->last()?->created_at
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
            '1h' => ['start' => $now->subHour()],
            '3h' => ['start' => $now->subHours(3)],
            '12h' => ['start' => $now->subHours(12)],
            '24h' => ['start' => $now->subHours(24)],
            '1d' => ['start' => $now->subDay()->startOfDay(), 'end' => $now->endOfDay()],
            '3d' => ['start' => $now->subDays(3)],
            '7d' => ['start' => $now->subDays(7)],
            '15d' => ['start' => $now->subDays(15)],
            '30d' => ['start' => $now->subDays(30)],
            default => ['start' => $now->subHours(24)],
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