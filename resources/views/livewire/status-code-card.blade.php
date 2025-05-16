<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="p-6">
        <div class="flex justify-between items-start">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Códigos de Status
            </h2>

            <div class="relative w-24">
                <select wire:model.live="selectedPeriod"
                    class="appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-1 px-2 pr-6 rounded-md text-xs focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @foreach($availablePeriods as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="h-64 mt-2">
            @if(count($statusCodes) > 0)
                <canvas id="statusCodeChart"></canvas>
            @else
                <div class="flex items-center justify-center h-full text-gray-500">
                    Nenhum código de status registrado
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        (function () {
            let chart = null;

            function initChart(statusData) {
                const canvasId = 'statusCodeChart';
                const ctx = document.getElementById(canvasId);

                if (!ctx) return;

                const filteredCodes = Object.keys(statusData)
                    .filter(code => statusData[code].count > 0)
                    .sort((a, b) => a - b);

                if (filteredCodes.length === 0) return;

                const labels = filteredCodes.map(code => `HTTP ${code}`);
                const data = filteredCodes.map(code => statusData[code].count);
                const backgroundColors = filteredCodes.map(code => {
                    const firstDigit = code.toString()[0];
                    switch (firstDigit) {
                        case '2': return '#22C55E';
                        case '3': return '#3B82F6';
                        case '4': return '#F59E0B';
                        case '5': return '#EF4444';
                        default: return '#9CA3AF';
                    }
                });

                const fullData = filteredCodes.reduce((obj, code) => {
                    obj[code] = statusData[code];
                    return obj;
                }, {});

                if (chart) {
                    chart.destroy();
                }

                chart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: backgroundColors,
                            borderWidth: 1,
                            borderColor: '#FFF'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 12,
                                    padding: 16,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    },
                                    afterLabel: function (context) {
                                        const statusCode = filteredCodes[context.dataIndex];
                                        const statusInfo = fullData[statusCode];

                                        if (!statusInfo || !statusInfo.apis) return '';

                                        let tooltipText = '';
                                        Object.entries(statusInfo.apis).forEach(([apiId, apiData]) => {
                                            const lastOccurrence = new Date(apiData.last_occurrence).toLocaleString();
                                            tooltipText += `\n• ${apiData.name}: ${apiData.count} vezes\n  (último: ${lastOccurrence})\n`;
                                        });

                                        return tooltipText;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }


            const statusData = @js($statusCodes);
            initChart(statusData);

            Livewire.on('chart-updated', ({ statusData }) => {
                initChart(statusData);
            });

            window.addEventListener('resize', function () {
                if (chart) {
                    chart.resize();
                }
            });
        })();
    </script>
    @endscript
</div>