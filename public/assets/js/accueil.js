document.addEventListener('DOMContentLoaded', () => {
  const navyColor = getComputedStyle(document.documentElement).getPropertyValue('--navy').trim();

  // Historical data chart (if exists)
  const historyData = JSON.parse(document.getElementById('historyData').textContent || '[]');
  const mainSoundCanvas = document.getElementById('mainSoundChart');
  if (mainSoundCanvas && historyData.length > 0) {
    new Chart(mainSoundCanvas, {
      type: 'line',
      data: {
        labels: historyData.map(d => new Date(d.temps)),
        datasets: [{
          label: 'Niveau Sonore (dB)',
          data: historyData.map(d => d.valeur),
          borderColor: navyColor,
          backgroundColor: navyColor + '20',
          fill: true,
          tension: 0.4,
          pointRadius: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: {
            type: 'time',
            time: { unit: 'hour', tooltipFormat: 'HH:mm' },
            grid: { display: false },
            ticks: { color: '#888' }
          },
          y: {
            beginAtZero: true,
            grid: { color: '#eef2f7' },
            ticks: { color: '#888' }
          }
        }
      }
    });
  }

  // Live gauge chart
  const liveGaugeCanvas = document.getElementById('liveGaugeChart');
  const maxGaugeValue = 120;

  // Use initial liveValue if exists, else 0
  const initialLiveValue = (() => {
    try {
      return JSON.parse(document.getElementById('liveValue').textContent);
    } catch {
      return 0;
    }
  })();

  if (liveGaugeCanvas) {
    const liveGaugeChart = new Chart(liveGaugeCanvas, {
      type: 'doughnut',
      data: {
        datasets: [{
          data: [initialLiveValue, maxGaugeValue - initialLiveValue],
          backgroundColor: [navyColor, '#eef2f7'],
          borderColor: [navyColor, '#eef2f7'],
          borderWidth: 1,
          circumference: 180,
          rotation: 270
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '80%',
        plugins: { tooltip: { enabled: false } }
      }
    });

    // Connect WebSocket to update live gauge
    const ws = new WebSocket('ws://localhost:8080');

    ws.onopen = () => {
      console.log('WebSocket connected');
    };

    ws.onerror = (err) => {
      console.error('WebSocket error:', err);
    };

    ws.onmessage = (event) => {
      try {
        const data = JSON.parse(event.data);
        const soundValue = data.valeur;

        liveGaugeChart.data.datasets[0].data = [soundValue, maxGaugeValue - soundValue];
        liveGaugeChart.update();
      } catch (e) {
        console.error('Failed to parse WebSocket message', e);
      }
    };
  }
});

// Scroll header color toggle remains as is
const header = document.querySelector('.site-header');
window.addEventListener('scroll', () => {
  header.classList.toggle('scrolled', window.scrollY > 10);
});
