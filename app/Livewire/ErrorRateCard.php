<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Api;
use App\Models\ApiStatusCheck;
use Carbon\Carbon;

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

    public function loadData()
    {
        $this->errorData = [
            'last_24h' => [
                'rate' => $this->calculateErrorRate(now()->subDay()),
                'apis' => $this->getErrorDetailsByApi(now()->subDay())
            ],
            'last_week' => [
                'rate' => $this->calculateErrorRate(now()->subWeek()),
                'apis' => $this->getErrorDetailsByApi(now()->subWeek())
            ],
            'last_month' => [
                'rate' => $this->calculateErrorRate(now()->subMonth()),
                'apis' => $this->getErrorDetailsByApi(now()->subMonth())
            ]
        ];
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    protected function calculateErrorRate($since)
    {
        $checks = ApiStatusCheck::whereIn('api_id', auth()->user()->apis->pluck('id'))
            ->where('created_at', '>=', $since)
            ->get();

        if ($checks->isEmpty()) return 0;

        $errorRate = ($checks->where('success', false)->count() / $checks->count()) * 100;
        
        return $errorRate < 0.01 && $errorRate > 0 ? 0.01 : round($errorRate, 2);
    }

    protected function getErrorDetailsByApi($since)
    {
        return Api::with(['statusChecks' => function($query) use ($since) {
                $query->where('created_at', '>=', $since);
            }])
            ->get()
            ->map(function($api) {
                $totalChecks = $api->statusChecks->count();
                $errorChecks = $api->statusChecks->where('success', false)->count();
                
                return [
                    'id' => $api->id,
                    'name' => $api->name,
                    'error_rate' => $totalChecks > 0 
                        ? round(($errorChecks / $totalChecks) * 100, 1)
                        : 0,
                    'error_count' => $errorChecks,
                    'total_checks' => $totalChecks
                ];
            })
            ->filter(fn($api) => $api['total_checks'] > 0)
            ->sortByDesc('error_count')
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.error-rate-card');
    }
}