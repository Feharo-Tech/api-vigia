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

        $statusTotals = ApiStatusCheck::query()
            ->select([
                'status_code',
                DB::raw('COUNT(*) as total_count')
            ])
            ->whereIn('api_id', $apiIds)
            ->where('created_at', '>=', $dateRange['start'])
            ->when(isset($dateRange['end']), fn($q) => $q->where('created_at', '<=', $dateRange['end']))
            ->groupBy('status_code')
            ->get()
            ->keyBy('status_code');

        $statusDetails = ApiStatusCheck::query()
            ->select([
                'status_code',
                'api_id',
                DB::raw('COUNT(*) as count'),
                DB::raw('MAX(created_at) as last_occurrence')
            ])
            ->whereIn('api_id', $apiIds)
            ->where('created_at', '>=', $dateRange['start'])
            ->when(isset($dateRange['end']), fn($q) => $q->where('created_at', '<=', $dateRange['end']))
            ->groupBy('status_code', 'api_id')
            ->with('api')
            ->get()
            ->groupBy('status_code');

        $this->statusCodes = $statusTotals->map(function ($total, $statusCode) use ($statusDetails) {
            return [
                'count' => $total->total_count,
                'apis' => isset($statusDetails[$statusCode]) 
                    ? $statusDetails[$statusCode]->map(function ($item) {
                        return [
                            'name' => $item->api->name,
                            'count' => $item->count,
                            'last_occurrence' => $item->last_occurrence
                        ];
                    })->toArray()
                    : []
            ];
        })->toArray();
    }

    protected function getDateRange()
    {
        $now = Carbon::now();
        
        return match ($this->selectedPeriod) {
            '1h' => ['start' => $now->subHour()],
            '3h' => ['start' => $now->subHours(3)],
            '12h' => ['start' => $now->subHours(12)],
            '24h' => ['start' => $now->subHours(24)],
            '3d' => ['start' => $now->subDays(3)],
            '7d' => ['start' => $now->subDays(7)],
            '15d' => ['start' => $now->subDays(15)],
            '30d' => ['start' => $now->subDays(30)],
            default => ['start' => $now->subHours(24)],
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