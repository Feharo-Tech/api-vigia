<?php

namespace App\Livewire;

use App\Models\Api;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ApiStatusChecksTable extends Component
{
    use WithPagination;

    public $api;
    public $statusFilter = 'all';
    public $periodFilter = '24h';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
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

    public function mount(Api $api)
    {
        $this->api = $api;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    protected function getDateRange()
    {
        $now = Carbon::now();
        
        return match ($this->periodFilter) {
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

    public function updating($name, $value)
    {
        if (in_array($name, ['statusFilter', 'periodFilter', 'sortField', 'sortDirection'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = $this->api->statusChecks()
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('success', $this->statusFilter === 'success');
            })
            ->when($this->periodFilter, function ($query) {
                $dateRange = $this->getDateRange();
                $query->where('created_at', '>=', $dateRange['start']);
                
                if (isset($dateRange['end'])) {
                    $query->where('created_at', '<=', $dateRange['end']);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $statusChecks = $query->paginate(10);

        return view('livewire.api-status-checks-table', [
            'statusChecks' => $statusChecks,
        ]);
    }
}
