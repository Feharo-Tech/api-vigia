<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ApiStatusCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\On;

class StatusCodeCard extends Component
{
    public $statusCodes = [];
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

    public function loadData()
    {
        $dateRange = $this->getDateRange();
        $apiIds = Auth::user()->visibleApis()->where('is_active', true)->pluck('id');

        $query = ApiStatusCheck::whereIn('api_id', $apiIds)
            ->where('created_at', '>=', $dateRange['start'])
            ->with('api');

        if (isset($dateRange['end'])) {
            $query->where('created_at', '<=', $dateRange['end']);
        }

        $checks = $query->get();

        $this->statusCodes = $checks->groupBy('status_code')
            ->map(function ($groupedChecks, $statusCode) {
                return [
                    'count' => $groupedChecks->count(),
                    'apis' => $groupedChecks->groupBy('api_id')
                        ->map(function ($apiChecks) {
                            $api = $apiChecks->first()->api;
                            return [
                                'name' => $api->name,
                                'count' => $apiChecks->count(),
                                'last_occurrence' => $apiChecks->max('created_at')
                            ];
                        })
                        ->sortByDesc('count')
                        ->toArray()
                ];
            })
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

    public function updatedSelectedPeriod($period)
    {
        $this->selectedPeriod = $period;
        $this->loadData();
        $this->dispatch('chart-updated', statusData: $this->statusCodes);
    }

    public function render()
    {
        return view('livewire.status-code-card');
    }
}