    <script>
        // Initialize trading chart
        const ctx = document.getElementById('tradingChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array.from({length: 24}, (_, i) => i + ':00'),
                datasets: [{
                    label: 'BTC/USDT',
                    data: Array.from({length: 24}, () => Math.random() * 2000 + 44000),
                    borderColor: 'rgb(59, 130, 246)',
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        grid: {
                            color: 'rgba(75, 85, 99, 0.3)'
                        },
                        ticks: {
                            color: '#9CA3AF'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#9CA3AF',
                            maxTicksLimit: 8
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 