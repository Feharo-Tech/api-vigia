<div class="bg-white rounded-xl shadow-md overflow-hidden col-span-1 md:col-span-4">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Histórico de Status (24h)
            </h2>
            
            <select wire:model.live="selectedApi" class="mt-1 block text-xs rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 w-40">
                <option value="all">Todas as APIs</option>
                @foreach($apis as $api)
                    <option value="{{ $api->id }}">{{ $api->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="h-[300px] relative">
            @if(count($initialData['datasets'] ?? []) > 0)
                <canvas id="statusHistoryChart-{{ $this->getId() }}"></canvas>
            @else
                <div class="flex items-center justify-center h-full text-gray-500">
                    Dados históricos não disponíveis
                </div>
            @endif
        </div>
    </div>


    @script
    <script>
        (function() {            
            let chartInstance = null;
            
                const canvasId = 'statusHistoryChart-{{ $this->getId() }}';
                const ctx = document.getElementById(canvasId);
                
                if (!ctx) return;
                
                const chartConfig = {
                type: 'line',
                data: @js($initialData),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 20,
                            left: 10,
                            right: 10
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        intersect: false,
                        axis: 'x'
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: 12
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                                    enabled: true,
                                    usePointStyle: false,
                                    position: 'average',
                                    caretPadding: 20,
                                    backgroundColor: '#1F2937',
                                    titleColor: '#F3F4F6',
                                    bodyColor: '#E5E7EB',
                                    footerColor: '#F3F4F6',
                                    padding: 16,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    bodyFont: {
                                        size: 12,
                                        lineHeight: 1.5
                                    },
                                    callbacks: {
                                        title: function(context) {
                                             return context[0].label;
                                        },
                                        label: function(context) {
                                            const meta = context.dataset.meta[context.dataIndex];
                                            const errorRate = meta.checks > 0 ? 
                                                ((meta.checks - meta.success) / meta.checks * 100).toFixed(2) : 
                                                '0.00';

                                            return [
                                                `Disponibilidade: ${context.parsed.y}%`,
                                                `Verificações: ${meta.checks}`,
                                                `Sucessos: ${meta.success}`,
                                                `Taxa de erro: ${errorRate}%`
                                            ];
                                        },
                                        footer: function(context) {
                                            const tooltipItem = Array.isArray(context) ? context[0] : context;
                                            const meta = tooltipItem.dataset.meta[tooltipItem.dataIndex];

                                            if (meta.checks === 0) return '⚠️ Sem verificações';
                                            const availability = context[0].parsed.y;
                                            if (availability < 90) return '⚠️ Status: Crítico';
                                            if (availability < 95) return '⚠️ Status: Atenção';
                                            return '✅ Status: Normal';
                                        }
                                    },
                                    itemSort: function(a, b) {
                                        return b.datasetIndex - a.datasetIndex;
                                    },
                                    filter: function(tooltipItem) {
                                        return !tooltipItem.hidden;
                                    }
                                },
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            };

            let chart = new Chart(ctx, chartConfig);

            Livewire.on('chart-updated', ({data}) => {
                if (chart) {
                    if (data.datasets && !Array.isArray(data.datasets)) {
                        data.datasets = Object.values(data.datasets);
                    }

                    chart.data = data;
                    chart.update('none');

                    setTimeout(() => {
                        chart.resize();
                    }, 50);
                }
            });
            
            window.addEventListener('resize', function() {
                chart.resize();
            });
                 
        })();
    </script>
    @endscript
</div>