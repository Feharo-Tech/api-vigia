<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Api;
use App\Models\ApiStatusCheck;
use Illuminate\Support\Facades\DB;

class ErrorRateCard extends Component
{
    public $errorData = [
        'last_24h' => ['rate' => 0, 'apis' => []],
        'last_week' => ['rate' => 0, 'apis' => []],
        'last_month' => ['rate' => 0, 'apis' => []]
    ];

    public $activeTab = 'last_24h';

    public function mount()
    {
        $this->loadData();
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function loadData()
    {
        $this->errorData = [
            'last_24h' => $this->getErrorStats(now()->subDay()),
            'last_week' => $this->getErrorStats(now()->subWeek()),
            'last_month' => $this->getErrorStats(now()->subMonth())
        ];
    }

    protected function getErrorStats($since)
    {
        $totalStats = ApiStatusCheck::whereIn('api_id', auth()->user()->apis->pluck('id'))
            ->where('created_at', '>=', $since)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN success = false THEN 1 ELSE 0 END) as errors')
            )
            ->first();
        
        $rate = $totalStats->total > 0 
            ? round(($totalStats->errors / $totalStats->total) * 100, 2)
            : 0;

        $apiDetails = Api::query()
            ->where('is_active', true)
            ->whereHas('statusChecks', fn($q) => $q->where('created_at', '>=', $since))
            ->withCount([
                'statusChecks as total_checks' => fn($q) => $q->where('created_at', '>=', $since),
                'statusChecks as error_checks' => fn($q) => $q->where('created_at', '>=', $since)
                                                             ->where('success', false)
            ])
            ->get()
            ->map(function($api) {
                return [
                    'id' => $api->id,
                    'name' => $api->name,
                    'error_rate' => $api->total_checks > 0 
                        ? round(($api->error_checks / $api->total_checks) * 100, 1)
                        : 0,
                    'error_count' => $api->error_checks,
                    'total_checks' => $api->total_checks
                ];
            })
            ->sortByDesc('error_count')
            ->values()
            ->toArray();

        return [
            'rate' => $rate < 0.01 && $rate > 0 ? 0.01 : $rate,
            'apis' => $apiDetails
        ];
    }

    public function render()
    {
        return view('livewire.error-rate-card');
    }
}