<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ApiStatusCheck;
use Illuminate\Support\Facades\DB;

class StatusHistoryCard extends Component
{
    public $historyData = [];
    public $filteredData = [];
    public $apis = [];
    public $selectedApi = 'all';
    
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
        $this->apis = auth()->user()->visibleApis()->where('is_active', true)->get();
        $this->historyData = $this->getHistoryData($this->getHoursFromPeriod());
        $this->filteredData = $this->historyData;
    }

    public function updatedSelectedApi()
    {
        $this->applyFilter();
    }
    
    public function updatedSelectedPeriod()
    {
        $this->loadData();
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
        $startTime = now()->subHours($hours);
        $groupByFormat = $this->getGroupByFormat();
        $dateFormat = $this->convertToDateFormat($groupByFormat);
        $user = auth()->user();
        $visibleApiIds = $user->visibleApis()->pluck('id');
        
        if ($visibleApiIds->isEmpty()) {
            return [
                'labels' => [],
                'datasets' => []
            ];
        }

        $results = DB::table('api_status_checks')
            ->select([
                DB::raw("to_char(created_at, '{$dateFormat}') as time_group"),
                'api_id',
                DB::raw('COUNT(*) as total_checks'),
                DB::raw('SUM(CASE WHEN success THEN 1 ELSE 0 END) as successful_checks')
            ])
            ->whereIn('api_id', $visibleApiIds)
            ->where('created_at', '>=', $startTime)
            ->groupBy('time_group', 'api_id')
            ->orderBy('time_group')
            ->get();

        $timeLabels = $results->pluck('time_group')->unique()->sort()->values();
        
        $apis = $user->visibleApis()->get(['id', 'name']);
        
        $history = [
            'labels' => $timeLabels->toArray(),
            'datasets' => []
        ];

        foreach ($apis as $api) {
            $apiData = [
                'label' => $api->name,
                'data' => array_fill(0, $timeLabels->count(), null),
                'borderColor' => $this->getRandomColor($api->id),
                'apiId' => $api->id,
                'meta' => array_fill(0, $timeLabels->count(), ['checks' => 0, 'success' => 0])
            ];

            $apiResults = $results->where('api_id', $api->id);
            
            foreach ($apiResults as $result) {
                $index = array_search($result->time_group, $history['labels']);
                if ($index !== false) {
                    $availability = $result->total_checks > 0 
                        ? round(($result->successful_checks / $result->total_checks) * 100, 2)
                        : 0;
                    
                    $apiData['data'][$index] = $availability;
                    $apiData['meta'][$index] = [
                        'checks' => $result->total_checks,
                        'success' => $result->successful_checks
                    ];
                }
            }

            $history['datasets'][] = $apiData;
        }

        return $history;
    }
    
    protected function getHoursFromPeriod()
    {
        $period = $this->selectedPeriod;
        
        if (str_ends_with($period, 'h')) {
            return (int) substr($period, 0, -1);
        } elseif (str_ends_with($period, 'd')) {
            return (int) substr($period, 0, -1) * 24;
        }
        
        return 24;
    }
    
    protected function getGroupByFormat()
    {
        $hours = $this->getHoursFromPeriod();
        
        if ($hours <= 24) {
            return 'Y-m-d H:00';
        } elseif ($hours <= 24 * 7) {
            return 'Y-m-d 12:00';
        } else {
            return 'Y-m-d';
        }
    }

    protected function convertToDateFormat($groupByFormat)
    {
        return match($groupByFormat) {
            'Y-m-d H:00' => 'YYYY-MM-DD HH24:00',
            'Y-m-d 12:00' => 'YYYY-MM-DD HH12:00',
            'Y-m-d' => 'YYYY-MM-DD',
            default => 'YYYY-MM-DD HH24:00'
        };
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