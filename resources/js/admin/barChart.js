/**
 * Earnings Bar Chart Component
 * Handles chart initialization, updates, and Livewire integration
 */

// Function to get chart title based on filter
function getChartTitle(filter) {
    switch(filter) {
        case 'daily': return 'Daily Earnings (Last 30 Days)';
        case 'weekly': return 'Weekly Earnings (Last 12 Weeks)';
        case 'yearly': return 'Yearly Earnings (Last 5 Years)';
        case 'monthly':
        default: return 'Monthly Earnings (Last 12 Months)';
    }
}

// Main bar chart function for Alpine.js
export function barChart(labels, totals, filter) {
    return {
        labels: labels,
        totals: totals,
        filter: filter,
        chart: null,
        
        init() {
            // Validate initial data
            if (!Array.isArray(this.labels) || !Array.isArray(this.totals)) {
                console.error('Invalid initial chart data.');
                return;
            }
            
            this.initializeChart();
            this.setupLivewireListeners();
        },

        initializeChart() {
            this.$nextTick(() => {
                if (!this.$refs.canvas) {
                    console.error('Canvas reference not found');
                    return;
                }
                this.chart = new Chart(this.$refs.canvas, {
                    type: 'bar',
                    data: {
                        labels: this.labels,
                        datasets: [{
                            label: 'Earnings',
                            data: this.totals,
                            backgroundColor: 'rgba(45, 136, 212, 0.9)',
                            borderColor: 'rgba(45, 136, 212, 1)',
                            borderWidth: 1,
                            barThickness: this.filter === 'daily' ? 30 : 50
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true },
                            title: { 
                                display: true, 
                                text: this.totals.length > 0 ? getChartTitle(this.filter) : 'No Data Available',
                                font: { size: 16, weight: 'bold' },
                                padding: { bottom: 20 }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let value = context.parsed.y;
                                        return '$' + value.toLocaleString('en-US', {
                                            minimumFractionDigits: 2, 
                                            maximumFractionDigits: 2
                                        });
                                    }
                                }
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'end',
                                formatter: function(value, context) {
                                    return '$' + Number(value).toLocaleString('en-US', {
                                        minimumFractionDigits: 2, 
                                        maximumFractionDigits: 2
                                    });
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        scales: {
                            x: { 
                                offset: true,
                                min: 0,
                                max: this.labels.length - 1,
                                categoryPercentage: 0.6,
                                barPercentage: 1.0,
                                grid: {
                                    display: false
                                } 
                            },
                            y: { 
                                beginAtZero: true,
                                grid: {
                                    display: false
                                } 
                            }
                        },
                    },
                    plugins: [ChartDataLabels]
                });
            });
        },

        setupLivewireListeners() {
            // Listen for chart updates from Livewire
            Livewire.on('chart-updated', (data) => {
                // Extract data from the event object
                this.labels = data[0]?.labels || data.labels;
                this.totals = data[0]?.totals || data.totals;
                this.filter = data[0]?.filter || data.filter;
                
                // Validate data before updating
                if (this.chart && Array.isArray(this.labels) && Array.isArray(this.totals) && this.filter) {
                    this.destroy();
                    this.initializeChart();
                } else {
                    console.error('Invalid chart data received:');
                }
            });
        },

        destroy() {
            if (this.chart) {
                this.chart.destroy();
            }
        }
    }
}

window.initEarningsBarChart = function(labels, totals, filter) {
    return barChart(labels, totals, filter);
};
