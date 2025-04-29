<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Api;
use App\Models\ApiStatusCheck;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatusHistoryCard extends Component
{
    public $historyData = [];
    public $filteredData = [];
    public $apis = [];
    public $selectedApi = 'all';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->apis = auth()->user()->visibleApis()->get();
        $this->historyData = $this->getHistoryData(24);
        $this->filteredData = $this->historyData;
    }

    public function updatedSelectedApi()
    {
        $this->applyFilter();
    }

    protected function applyFilter()
    {
        if ($this->selectedApi === 'all') {
            $this->filteredData = $this->historyData;
        } else {
            $filteredDatasets = array_values(array_filter($this->historyData['datasets'], function($dataset) {
                return $dataset['apiId'] == $this->selectedApi;
            }));
            
            $this->filteredData = [
                'labels' => $this->historyData['labels'],
                'datasets' => $filteredDatasets
            ];
        }
        
        $this->dispatch('chart-updated', data: $this->filteredData);
    }

    protected function getHistoryData($hours = 24)
    {
        $user = auth()->user();
        $history = [
            'labels' => [],
            'datasets' => []
        ];
        
        $now = now();
        $startTime = $now->copy()->subHours($hours);
        
        $allChecks = ApiStatusCheck::whereIn(
                            'api_id',
                            $user->visibleApis()->pluck('id')
                        )
                        ->where('created_at', '>=', $startTime)
                        ->orderBy('created_at')
                        ->get()
                        ->groupBy(function($item) {
                            return $item->created_at->format('Y-m-d H:00');
                        });


        $history['labels'] = array_keys($allChecks->toArray());
        sort($history['labels']);

        foreach ($this->apis as $api) {
            $apiData = [
                'label' => $api->name,
                'data' => [],
                'borderColor' => $this->getRandomColor($api->id),
                'backgroundColor' => 'rgba(0, 0, 0, 0.05)',
                'tension' => 0.3,
                'borderWidth' => 2,
                'pointRadius' => 3,
                'pointHoverRadius' => 5,
                'apiId' => $api->id,
                'meta' => []
            ];

            $apiChecks = ApiStatusCheck::where('api_id', $api->id)
                ->where('created_at', '>=', $startTime)
                ->orderBy('created_at')
                ->get()
                ->groupBy(function($item) {
                    return $item->created_at->format('Y-m-d H:00');
                });

            foreach ($history['labels'] as $timeLabel) {
                $hourKey = $timeLabel;
                
                if (isset($apiChecks[$hourKey])) {
                    $hourChecks = $apiChecks[$hourKey];
                    $total = $hourChecks->count();
                    $success = $hourChecks->where('success', true)->count();
                    $availability = $total > 0 ? round(($success / $total) * 100, 2) : 0;
                    
                    $apiData['data'][] = $availability;
                    $apiData['meta'][] = [
                        'checks' => $total,
                        'success' => $success,
                        'time' => $hourKey
                    ];
                } else {
                    $apiData['data'][] = null;
                    $apiData['meta'][] = [
                        'checks' => 0,
                        'success' => 0,
                        'time' => $hourKey
                    ];
                }
            }

            $history['datasets'][] = $apiData;
        }

        return $history;
    }

    private function getRandomColor($seed) {
        $goldenRatio = 0.618033988749895;
        $h = fmod($seed * $goldenRatio, 1);
        
        $hsvToRgb = function($h, $s, $v) {
            $i = floor($h * 6);
            $f = $h * 6 - $i;
            $p = $v * (1 - $s);
            $q = $v * (1 - $f * $s);
            $t = $v * (1 - (1 - $f) * $s);
            
            $rgb = match ($i % 6) {
                0 => [$v, $t, $p],
                1 => [$q, $v, $p],
                2 => [$p, $v, $t],
                3 => [$p, $q, $v],
                4 => [$t, $p, $v],
                default => [$v, $p, $q],
            };
            
            return sprintf(
                "rgb(%d, %d, %d)",
                round($rgb[0] * 255),
                round($rgb[1] * 255),
                round($rgb[2] * 255)
            );
        };
        
        return $hsvToRgb($h, 0.8, 0.9);
    }

    public function render()
    {
        return view('livewire.status-history-card', [
            'initialData' => $this->filteredData
        ]);
    }
}