document.addEventListener('DOMContentLoaded', function () {
    const chartContainer = document.getElementById('forecast-chart');
    if (!chartContainer) { return; }

    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts is not loaded.');
        return;
    }

    const loader           = document.getElementById('chart-loader');
    const tabs             = document.querySelectorAll('#crop-tabs .tab');
    const breakEvenInput   = document.getElementById('break-even-input');
    const legendBreakEven  = document.getElementById('legend-break-even');

    let currentChart       = null;
    let lastData           = null;  // Cache latest API response
    let activeCropKey      = null;  // Used for localStorage persistence



    // -------------------------------------------------------------------------
    // Break-even annotation helpers
    // -------------------------------------------------------------------------
    function getBreakEvenValue() {
        const val = parseFloat(breakEvenInput.value);
        return isNaN(val) || val <= 0 ? null : val;
    }

    function buildAnnotations(breakEven) {
        if (breakEven === null) {
            return {};
        }
        return {
            yaxis: [{
                y: breakEven,
                borderColor: '#f43f5e',
                borderWidth: 2,
                strokeDashArray: 6,
                label: {
                    borderColor: '#f43f5e',
                    style: { color: '#fff', background: '#f43f5e', fontSize: '11px', fontWeight: 600 },
                    text: `Break-Even ₱${breakEven.toFixed(2)}/kg`,
                    position: 'left',
                    offsetX: 8,
                },
            }],
        };
    }

    function updateBreakEvenAnnotation() {
        if (!currentChart) { return; }
        const breakEven = getBreakEvenValue();

        legendBreakEven.classList.toggle('hidden', breakEven === null);

        currentChart.updateOptions({ annotations: buildAnnotations(breakEven) });
    }

    // -------------------------------------------------------------------------
    // Main chart render / update
    // -------------------------------------------------------------------------
    function renderChart(cropId, cropName, spec) {
        chartContainer.classList.add('opacity-50');
        loader.classList.remove('hidden');

        activeCropKey = `forecast_be_${cropId}_${spec}`;
        const storedBreakEven = localStorage.getItem(activeCropKey);
        if (storedBreakEven !== null) {
            breakEvenInput.value = storedBreakEven;
        } else {
            breakEvenInput.value = '';
        }
        legendBreakEven.classList.add('hidden');

        const baseUrl = window.AppUrl || '';
        fetch(`${baseUrl}/api/v2/forecast/${cropId}?spec=${spec}&_t=${Date.now()}`)
            .then(res => res.json())
            .then(data => {
                loader.classList.add('hidden');
                chartContainer.classList.remove('opacity-50');

                lastData = data;

                const breakEven = getBreakEvenValue();

                const options = {
                    series: [
                        { name: 'Actual Price',       data: data.actual },
                        { name: 'Forecast Trend',     data: data.forecast },
                    ],
                    chart: {
                        height: 420,
                        type: 'line',
                        fontFamily: 'inherit',
                        toolbar: { show: false },
                        animations: { enabled: true, easing: 'easeinout', speed: 800 },
                    },
                    colors: ['#10b981', '#6366f1'],
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: 'smooth',
                        width: [3, 2.5],
                        dashArray: [0, 5],
                    },
                    fill: {
                        type: ['solid', 'solid'],
                        opacity: [1, 1],
                    },
                    xaxis: {
                        categories: data.dates,
                        type: 'datetime',
                        labels: {
                            style: { colors: '#6b7280' },
                            datetimeFormatter: { year: 'yyyy', month: "MMM 'yy", day: 'dd MMM', hour: 'HH:mm' },
                        },
                    },
                    yaxis: {
                        title: {
                            text: 'Price (₱ / kg)',
                            style: { fontWeight: 600, color: '#6b7280' },
                        },
                        labels: { formatter: (v) => v !== null ? `₱${v.toFixed(2)}` : '' },
                    },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    legend: { show: false },
                    annotations: buildAnnotations(breakEven),
                    tooltip: {
                        theme: document.documentElement.getAttribute('data-theme') === 'forest' ? 'dark' : 'light',
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function (val, { seriesIndex }) {
                                if (val === null || val === undefined) { return null; }
                                const be = getBreakEvenValue();
                                if (be !== null) {
                                    const profit = val - be;
                                    const sign   = profit >= 0 ? '+' : '';
                                    const color  = profit >= 0 ? '🟢' : '🔴';
                                    return `₱${val.toFixed(2)} ${color} (${sign}₱${profit.toFixed(2)}/kg)`;
                                }
                                return `₱${val.toFixed(2)}`;
                            },
                        },
                    },
                };

                if (currentChart) {
                    currentChart.updateOptions(options);
                } else {
                    currentChart = new ApexCharts(chartContainer, options);
                    currentChart.render();
                }

                if (breakEven !== null) {
                    legendBreakEven.classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error('Error fetching forecast:', err);
                loader.classList.add('hidden');
                chartContainer.innerHTML = '<div class="text-center text-error p-8">Failed to load forecast data. Please try again.</div>';
            });
    }

    // -------------------------------------------------------------------------
    // Initialise
    // -------------------------------------------------------------------------
    if (tabs.length > 0) {
        const firstTab = tabs[0];
        renderChart(firstTab.dataset.cropId, firstTab.dataset.cropName, firstTab.dataset.spec);
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('tab-active', 'font-bold'));
            tab.classList.add('tab-active', 'font-bold');
            renderChart(tab.dataset.cropId, tab.dataset.cropName, tab.dataset.spec);
        });
    });

    // Break-even input — live update annotation & persist per crop
    breakEvenInput.addEventListener('input', () => {
        updateBreakEvenAnnotation();
        if (activeCropKey) {
            const val = breakEvenInput.value;
            if (val) {
                localStorage.setItem(activeCropKey, val);
            } else {
                localStorage.removeItem(activeCropKey);
            }
        }
    });
});

