import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import * as XLSX from 'xlsx';

Chart.register(ChartDataLabels);
window.Chart = Chart;

let genderChartInstance = null;
let totalStudentsChartInstance = null;
let previousStats = null;
let moduleChartInstance = null;
let pretestChartInstance = null;
let posttestChartInstance = null;
let prePostRawData = { pretest: {}, posttest: {} };
let moduleRawData = {};


function readStatsFromDOM() {
    return {
        total: Number(document.getElementById('stat-total')?.innerText ?? 0),
        male: Number(document.getElementById('stat-male')?.innerText ?? 0),
        female: Number(document.getElementById('stat-female')?.innerText ?? 0),
        other: Number(document.getElementById('stat-other')?.innerText ?? 0),
        none: Number(document.getElementById('stat-none')?.innerText ?? 0),
    };
}

function statsChanged(oldStats, newStats) {
    if (!oldStats) return true;

    return Object.keys(newStats).some(
        key => oldStats[key] !== newStats[key]
    );
}

function updateGenderChart(data) {
    if (!genderChartInstance) {
        initGenderChart(data);
        return;
    }

    genderChartInstance.data.datasets[0].data = [
        data.male,
        data.female,
        data.other,
        data.none
    ];

    genderChartInstance.update();
}

function updateTotalStudentsChart(data) {
    if (!totalStudentsChartInstance) {
        initTotalStudentsChart(data);
        return;
    }

    totalStudentsChartInstance.data.datasets[0].data = [
        data.male,
        data.female,
        data.other,
        data.none
    ];

    totalStudentsChartInstance.update();
}

function updateModuleChart(moduleData) {
    if (!moduleChartInstance) {
        initModuleAttemptsChart(moduleData);
        return;
    }

    const levelIds = ['1','2','3'];
    const newData = levelIds.map(id => moduleData[id]?.totalAttempts ?? 0);

    moduleChartInstance.data.datasets[0].data = newData;
    moduleChartInstance.update();
}


const delayedAnimation = {
    animation: {
        duration: 800,
        easing: 'easeOutQuart',
        delay(context) {
            let delay = 0;

            // Animate elements one by one
            if (context.type === 'data' && context.mode === 'default') {
                delay = context.dataIndex * 200;
            }

            return delay;
        }
    }
};

// function downloadChartPNG(chartInstance, filename) {
//     if (!chartInstance) {
//         console.warn('Chart instance not ready');
//         return;
//     }

//     const canvas = chartInstance.canvas;

//     // Force white background (important for PNG exports)
//     const ctx = canvas.getContext('2d');
//     ctx.save();
//     ctx.globalCompositeOperation = 'destination-over';
//     ctx.fillStyle = '#ffffff';
//     ctx.fillRect(0, 0, canvas.width, canvas.height);
//     ctx.restore();

//     const image = canvas.toDataURL('image/png');

//     const link = document.createElement('a');
//     link.href = image;
//     link.download = filename;
//     link.click();
// }

function downloadChartPNG(chartInstance, title, filename, padding = 20) {
    if (!chartInstance) {
        console.warn('Chart instance not ready');
        return;
    }

    const chartCanvas = chartInstance.canvas;
    const titleHeight = 50;

    // Create a new canvas for export
    const exportCanvas = document.createElement('canvas');
    const ctx = exportCanvas.getContext('2d');

    // Set canvas size: chart + title + padding top/bottom/left/right
    exportCanvas.width = chartCanvas.width + padding * 2;
    exportCanvas.height = chartCanvas.height + titleHeight + padding * 2;

    // Background
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, exportCanvas.width, exportCanvas.height);

    // Title
    ctx.fillStyle = '#111827'; // gray-900
    ctx.font = 'bold 18px sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(title, exportCanvas.width / 2, padding + titleHeight / 2);

    // Draw chart with padding offset
    ctx.drawImage(
        chartCanvas,
        padding,          // x position
        padding + titleHeight, // y position
        chartCanvas.width,
        chartCanvas.height
    );

    // Export
    const link = document.createElement('a');
    link.href = exportCanvas.toDataURL('image/png');
    link.download = filename;
    link.click();
}


function initGenderChart(data) {
    const canvas = document.getElementById('genderChart');
    if (!canvas) return;

    if (genderChartInstance) genderChartInstance.destroy();

    genderChartInstance = new Chart(canvas, {
        type: 'pie',
        data: {
            labels: ['Male', 'Female', 'Other', 'None'],
            datasets: [{
                data: [data.male, data.female, data.other, data.none],
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#A9A9A9'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                datalabels: {
                    color: '#fff',
                    formatter: (value, ctx) => {
                        const sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / sum) * 100).toFixed(1);
                        return `${percentage}%`;
                    },
                    font: {
                        weight: 'bold',
                        size: 12
                    }
                }
            },
            ...delayedAnimation
        },
        plugins: [ChartDataLabels]
    });
}


function initTotalStudentsChart(data) {
    const canvas = document.getElementById('totalStudentsChart');
    if (!canvas) return;

    if (totalStudentsChartInstance) totalStudentsChartInstance.destroy();

    totalStudentsChartInstance = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: ['Male', 'Female', 'Other', 'None'],
            datasets: [{
                data: [data.male, data.female, data.other, data.none],
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#A9A9A9'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: { display: false }
            },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
            ...delayedAnimation
        }
    });
}

function initModuleAttemptsChart(moduleData) {
    const canvas = document.getElementById('moduleChart');
    if (!canvas) return;

    if (moduleChartInstance) moduleChartInstance.destroy();

    // Labels and colors
    const levelLabels = ['Easy', 'Average', 'Hard'];
    const levelColors = ['#36A2EB', '#FFCE56', '#FF6384'];

    // Access string keys
    const levelIds = ['1','2','3'];
    const dataValues = levelIds.map(id => moduleData[id]?.totalAttempts ?? 0);

    moduleChartInstance = new Chart(canvas, {
        type: 'pie',
        data: {
            labels: levelLabels,
            datasets: [{
                data: dataValues,
                backgroundColor: levelColors,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        filter: function(legendItem, chartData) {
                            // Hide legend if corresponding data value is 0
                            return chartData.datasets[0].data[legendItem.index] > 0;
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    formatter: (value, ctx) => {
                        const sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const percentage = sum > 0 ? ((value / sum) * 100).toFixed(1) : 0;
                        return `${percentage}%`;
                    },
                    font: {
                        weight: 'bold',
                        size: 12
                    }
                }
            },
            ...delayedAnimation
        },
        plugins: [ChartDataLabels]
    });
}

function initPrePostChart(prePostScores, startAttempt, endAttempt) {

    const buildChartData = (data) => {
        const labels = [];
        const values = [];

        let overall = 0;

        for (let i = startAttempt; i <= endAttempt; i++) {
            labels.push(i.toString());
            const score = data[i]?.totalScore ?? 0;
            values.push(score);
            overall += score;
        }

        labels.push('Overall');
        values.push(overall);

        return { labels, values };
    };

    // ---------- PRETEST ----------
    const pretestCanvas = document.getElementById('pretestChart');
    if (pretestCanvas) {
        const pretestData = buildChartData(prePostScores.pretest);

        if (pretestChartInstance) pretestChartInstance.destroy();

        pretestChartInstance = new Chart(pretestCanvas, {
            type: 'bar',
            data: {
                labels: pretestData.labels,
                datasets: [{
                    label: 'Pretest',
                    data: pretestData.values,
                    backgroundColor: '#36A2EB',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animations: {
                    y: {
                        from: (ctx) => {
                            if (ctx.type === 'data') {
                                return ctx.chart.scales.y.getPixelForValue(0);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 14,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#000',
                        font: { weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                },
                ...delayedAnimation
            },
            plugins: [ChartDataLabels]
        });
    }

    // ---------- POSTTEST ----------
    const posttestCanvas = document.getElementById('posttestChart');
    if (posttestCanvas) {
        const posttestData = buildChartData(prePostScores.posttest);

        if (posttestChartInstance) posttestChartInstance.destroy();

        posttestChartInstance = new Chart(posttestCanvas, {
            type: 'bar',
            data: {
                labels: posttestData.labels,
                datasets: [{
                    label: 'Post-test',
                    data: posttestData.values,
                    backgroundColor: '#FF6384',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animations: {
                    y: {
                        from: (ctx) => {
                            if (ctx.type === 'data') {
                                return ctx.chart.scales.y.getPixelForValue(0);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 14,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#000',
                        font: { weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                },
                ...delayedAnimation
            },
            plugins: [ChartDataLabels]
        });
    }
}


window.addEventListener('update-charts', event => {
    const payload = Array.isArray(event.detail) ? event.detail[0] : event.detail;

    // Gender chart
    if (payload.genderData && payload.totalData) {
        initGenderChart(payload.genderData);
        initTotalStudentsChart(payload.totalData);
    }

    // Module attempts chart
    if (payload.moduleAttempts) {
        console.log('Module chart payload:', payload.moduleAttempts);
        moduleRawData = payload.moduleAttempts;
        updateModuleChart(payload.moduleAttempts);
    }

    // Pre/Post test chart
    if (payload.prePostScores) {
        console.log('📊 Pre/Post test chart payload:', payload.prePostScores);
        prePostRawData = payload.prePostScores;
        initPrePostChart(payload.prePostScores, payload.startAttempt, payload.endAttempt);
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const container = document.body; // or a more specific wrapper

    const observer = new MutationObserver(() => {
        const newStats = readStatsFromDOM();

        if (statsChanged(previousStats, newStats)) {
            console.log('📊 Stats changed:', newStats);

            updateGenderChart(newStats);
            updateTotalStudentsChart(newStats);

            previousStats = { ...newStats };
        }
    });

    observer.observe(container, {
        childList: true,
        subtree: true,
        characterData: true,
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const initialStats = readStatsFromDOM();

    // Gender & Total students charts
    initGenderChart({ male: 0, female: 0, other: 0, none: 0 });
    initTotalStudentsChart({ male: 0, female: 0, other: 0, none: 0 });

    // Initialize Module chart with 0 values
    initModuleAttemptsChart({
        '1': { totalAttempts: 0, users: [] }, // Easy
        '2': { totalAttempts: 0, users: [] }, // Average
        '3': { totalAttempts: 0, users: [] }  // Hard
    });

    // Animate to actual stats after short delay
    setTimeout(() => {
        updateGenderChart(initialStats);
        updateTotalStudentsChart(initialStats);
        previousStats = { ...initialStats };
    }, 100);
});


document.addEventListener('DOMContentLoaded', () => {
    const downloadTotalBtn = document.getElementById('downloadTotalChart');
    const downloadGenderBtn = document.getElementById('downloadGenderChart');

    if (downloadTotalBtn) {
        downloadTotalBtn.addEventListener('click', () => {
            downloadChartPNG(
                totalStudentsChartInstance,
                'Total Students Overview',
                'total-students-chart.png'
            );
        });
    }

    if (downloadGenderBtn) {
        downloadGenderBtn.addEventListener('click', () => {
            downloadChartPNG(
                genderChartInstance,
                'Student Gender Distribution',
                'gender-distribution-chart.png'
            );
        });
    }
});

document.getElementById('exportExcelBtn')?.addEventListener('click', () => {
    const stats = readStatsFromDOM();
    const total = stats.total || Object.values(stats).reduce((a,b) => a+b, 0);

    const rows = [
        ['Total Students Overview'],
        [],
        ['Gender', 'Number of Students', 'Percentage']
    ];

    ['male','female','other','none'].forEach(gender => {
        const count = stats[gender] || 0;
        const percent = total ? ((count / total) * 100).toFixed(1) + '%' : '0%';
        rows.push([gender.charAt(0).toUpperCase() + gender.slice(1), count, percent]);
    });

    rows.push(['Total', total, '100%']);

    const ws = XLSX.utils.aoa_to_sheet(rows);
    ws['!merges'] = [{ s: { r:0, c:0 }, e: { r:0, c:2 } }];
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Students');
    XLSX.writeFile(wb, 'student-demographics.xlsx');
});

document.addEventListener('DOMContentLoaded', () => {
    const downloadModuleBtn = document.getElementById('downloadModuleChart');

    if (downloadModuleBtn) {
        downloadModuleBtn.addEventListener('click', () => {
            downloadChartPNG(
                moduleChartInstance,
                'Overall Module Attempts per Level',
                'overall-module-attempts.png'
            );
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const downloadPrePostBtn = document.getElementById('downloadPrePostChart');

    if (downloadPrePostBtn) {
        downloadPrePostBtn.addEventListener('click', () => {
            if (!pretestChartInstance || !posttestChartInstance) {
                console.warn('Charts not ready');
                return;
            }

            const preCanvas = pretestChartInstance.canvas;
            const postCanvas = posttestChartInstance.canvas;

            // Get rendered sizes
            const preWidth = preCanvas.offsetWidth;
            const preHeight = preCanvas.offsetHeight;
            const postWidth = postCanvas.offsetWidth;
            const postHeight = postCanvas.offsetHeight;

            const padding = 20;
            const spacing = 50; // space between charts
            const width = preWidth + postWidth + spacing + padding * 2;
            const height = Math.max(preHeight, postHeight) + padding * 2 + 40; // extra for title

            const exportCanvas = document.createElement('canvas');
            exportCanvas.width = width;
            exportCanvas.height = height;
            const ctx = exportCanvas.getContext('2d');

            // Background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, width, height);

            // Optional title
            const titleHeight = 40;
            ctx.fillStyle = '#111827';
            ctx.font = 'bold 18px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('Pre & Post Test Scores', width / 2, padding + titleHeight / 2);

            // Draw pretest chart (left)
            ctx.drawImage(
                preCanvas,
                0, 0, preCanvas.width, preCanvas.height, // source
                padding, padding + titleHeight, preWidth, preHeight // destination
            );

            // Draw post-test chart (right)
            ctx.drawImage(
                postCanvas,
                0, 0, postCanvas.width, postCanvas.height, // source
                padding + preWidth + spacing, padding + titleHeight, postWidth, postHeight // destination
            );

            // Download combined image
            const link = document.createElement('a');
            link.href = exportCanvas.toDataURL('image/png');
            link.download = 'prepost-charts.png';
            link.click();
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // Export Pre/Post Chart Excel
    const exportPrePostBtn = document.getElementById('exportPrePostExcel');
    if (exportPrePostBtn) {
        exportPrePostBtn.addEventListener('click', () => {
            const rows = [
                ['Pretest Attempts'],
                ['Attempt Number', 'Total Score', 'Full Name', 'Username', 'Grade Level', 'Section', 'Score', 'Created At']
            ];

            Object.values(prePostRawData.pretest).forEach(attempt => {
                const total = attempt.totalScore;
                if (attempt.users.length) {
                    attempt.users.forEach(u => {
                        rows.push([
                            attempt.attemptNumber,
                            total,
                            u.user,
                            u.username,
                            u.grade_level,
                            u.section,
                            u.score,
                            u.created_at
                        ]);
                    });
                } else {
                    rows.push([attempt.attemptNumber, total, '', '', '', '', '', '']);
                }
            });

            // Post-test
            rows.push([]);
            rows.push(['Post-test Attempts']);
            rows.push(['Attempt Number', 'Total Score', 'Full Name', 'Username', 'Grade Level', 'Section', 'Score', 'Created At']);

            Object.values(prePostRawData.posttest).forEach(attempt => {
                const total = attempt.totalScore;
                if (attempt.users.length) {
                    attempt.users.forEach(u => {
                        rows.push([
                            attempt.attemptNumber,
                            total,
                            u.user,
                            u.username,
                            u.grade_level,
                            u.section,
                            u.score,
                            u.created_at
                        ]);
                    });
                } else {
                    rows.push([attempt.attemptNumber, total, '', '', '', '', '', '']);
                }
            });

            const ws = XLSX.utils.aoa_to_sheet(rows);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'PrePost Test');
            XLSX.writeFile(wb, 'prepost-test.xlsx');
        });
    }

    // Export Module Chart Excel
    const exportModuleBtn = document.getElementById('exportModuleExcel');
    if (exportModuleBtn) {
        exportModuleBtn.addEventListener('click', () => {
            const rows = [
                ['Module Attempts per Level'],
                ['Level', 'Total Attempts', 'Full Name', 'Username', 'Grade Level', 'Section', 'Created At', 'User Attempts']
            ];

            Object.entries(moduleRawData).forEach(([levelId, levelData]) => {
                const totalAttempts = levelData.totalAttempts;
                const users = levelData.users || [];
                if (users.length) {
                    users.forEach(u => {
                        rows.push([
                            levelId,
                            totalAttempts,
                            u.user,
                            u.username,
                            u.grade_level,
                            u.section,
                            u.created_at,
                            u.totalAttempts
                        ]);
                    });
                } else {
                    rows.push([levelId, totalAttempts, '', '', '', '', '', '']);
                }
            });

            const ws = XLSX.utils.aoa_to_sheet(rows);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Module Attempts');
            XLSX.writeFile(wb, 'module-attempts.xlsx');
        });
    }
});


