(function () {
    var holder = document.getElementById('report-data');
    var canvas = document.getElementById('report-chart');

    if (!holder || !canvas || typeof Chart === 'undefined') {
        return;
    }

    var payload;

    try {
        payload = JSON.parse(holder.textContent);
    } catch (error) {
        return;
    }

    new Chart(canvas, {
        type: 'line',
        data: payload,
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Events' } },
                x: { title: { display: true, text: 'Date (UTC)' } }
            }
        }
    });
})();
