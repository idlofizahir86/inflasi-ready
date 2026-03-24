<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    chartData: {
        type: Array,
        default: () => []
    },
    commodities: {
        type: Array,
        default: () => []
    },
    dateRange: {
        type: Object,
        default: () => ({ start: null, end: null })
    },
    title: {
        type: String,
        default: 'Price Trends'
    }
});

const canvasRef = ref(null);
let chartInstance = null;
const timeFormat = ref('daily');
const filteredData = ref([]);

// Warna untuk setiap komoditas
const commodityColors = {
    'Cabai Merah': '#004532',
    'Beras Medium': '#2170e4',
    'Minyak Goreng': '#f59e0b',
    'Bawang Merah': '#ef4444',
    'Daging Ayam': '#8b5cf6',
    'Telur Ayam': '#ec4899'
};

const generateChartData = () => {
    if (!props.chartData || props.chartData.length === 0) {
        return generateMockData();
    }

    // Group data by commodity
    const groupedData = {};
    
    props.chartData.forEach(point => {
        if (!groupedData[point.name]) {
            groupedData[point.name] = [];
        }
        groupedData[point.name].push(point);
    });

    // Get labels (dates)
    let labels = props.chartData
        .map(p => p.date)
        .filter((v, i, a) => a.indexOf(v) === i)
        .sort();

    // If weekly view, aggregate data
    if (timeFormat.value === 'weekly') {
        labels = aggregateToWeekly(labels);
    }

    // Create datasets
    const datasets = Object.keys(groupedData).map(name => {
        let data = labels.map(label => {
            const found = groupedData[name].find(p => p.date === label);
            return found ? found.price : null;
        });

        // If weekly, average the prices
        if (timeFormat.value === 'weekly') {
            data = data.map((price, idx) => {
                if (price === null) return null;
                // Average with surrounding days
                const surrounding = [];
                for (let i = Math.max(0, idx - 3); i <= Math.min(labels.length - 1, idx + 3); i++) {
                    const p = groupedData[name][i];
                    if (p && p.price) surrounding.push(p.price);
                }
                return surrounding.length > 0 ? surrounding.reduce((a, b) => a + b) / surrounding.length : price;
            });
        }

        return {
            label: name,
            data: data,
            borderColor: commodityColors[name] || '#004532',
            backgroundColor: (commodityColors[name] || '#004532') + '20',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: commodityColors[name] || '#004532',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 6
        };
    });

    return { labels, datasets };
};

const aggregateToWeekly = (dateLabels) => {
    // Group dates into weeks
    const weeks = [];
    let weekDates = [];
    
    dateLabels.forEach((date, idx) => {
        weekDates.push(date);
        if (weekDates.length === 7 || idx === dateLabels.length - 1) {
            weeks.push(`Week ${Math.ceil((idx + 1) / 7)}`);
            weekDates = [];
        }
    });
    
    return weeks;
};

const generateMockData = () => {
    const labels = [];
    const today = new Date();
    for (let i = 29; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' }));
    }

    return {
        labels,
        datasets: [
            {
                label: 'Cabai Merah',
                data: Array.from({ length: 30 }, () => Math.floor(Math.random() * 15000 + 35000)),
                borderColor: '#004532',
                backgroundColor: '#00453220',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#004532',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            },
            {
                label: 'Beras Medium',
                data: Array.from({ length: 30 }, () => Math.floor(Math.random() * 3000 + 12000)),
                borderColor: '#2170e4',
                backgroundColor: '#2170e420',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#2170e4',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            },
            {
                label: 'Minyak Goreng',
                data: Array.from({ length: 30 }, () => Math.floor(Math.random() * 5000 + 12000)),
                borderColor: '#f59e0b',
                backgroundColor: '#f59e0b20',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }
        ]
    };
};

const initChart = () => {
    if (!canvasRef.value) return;

    if (chartInstance) {
        chartInstance.destroy();
    }

    const data = generateChartData();
    
    chartInstance = new Chart(canvasRef.value, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            family: "'Lexend', sans-serif",
                            size: 12,
                            weight: 600
                        },
                        color: '#191c1e',
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: '#191c1e',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#004532',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + 
                                new Intl.NumberFormat('id-ID').format(Math.round(context.parsed.y));
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        },
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11
                        },
                        color: '#3f4944'
                    },
                    grid: {
                        color: '#f0f0f0',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11
                        },
                        color: '#3f4944'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
};

onMounted(() => {
    initChart();
});

watch([() => props.chartData, () => props.dateRange, timeFormat], () => {
    initChart();
}, { deep: true });
</script>

<template>
    <section class="bg-surface-container-lowest rounded-lg p-8 shadow-[0_20px_50px_rgba(0,33,22,0.02)]">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h3 class="text-xl font-bold font-headline text-on-surface">{{ title }}</h3>
                <p class="text-sm text-on-surface-variant">30-day historical analysis (IDR)</p>
            </div>
            <div class="flex gap-2">
                <button 
                    @click="timeFormat = 'daily'"
                    :class="timeFormat === 'daily' ? 'bg-secondary-fixed text-on-secondary-fixed' : 'hover:bg-surface-container'"
                    class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-tight transition-colors">
                    Daily
                </button>
                <button 
                    @click="timeFormat = 'weekly'"
                    :class="timeFormat === 'weekly' ? 'bg-secondary-fixed text-on-secondary-fixed' : 'hover:bg-surface-container'"
                    class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-tight transition-colors">
                    Weekly
                </button>
            </div>
        </div>

        <div class="relative h-80 w-full">
            <canvas ref="canvasRef"></canvas>
        </div>
    </section>
</template>

<style scoped>
.font-headline { font-family: 'Lexend', sans-serif; }
.font-body { font-family: 'Inter', sans-serif; }
</style>
