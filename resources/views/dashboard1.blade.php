<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixed Layout Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
</head>

<body class="dashboard-layout">
    @include('sidebar')

    <div class="main-content">
        <div class="content-wrapper">
            <div class="left-content">
                {{-- <div class="card">
                        <h3>Analitik Penjualan Tebanyak</h3>
                        <div class="menu-grid">
                            <button class="btn" style="background-color: #0f71d3ff;">
                                <div class="menu-name">Espreso</div>
                                <div class="menu-price">100 Pcs</div>
                            </button>
                            <button class="btn" style="background-color: #7a22a3ff;">
                                <div class="menu-name">Varian Non-Kopi</div>
                                <div class="menu-price">40 Pcs</div>
                            </button>
                            <button class="btn" style="background-color: #239407ff;">
                                <div class="menu-name">Menu Tambahan</div>
                                <div class="menu-price">30 Pcs</div>
                            </button>
                        </div>
                    </div> --}}
                <div class="card card-statistik">
                    <h3>Statistik Penjualan</h3>
                    <div class="statistik-container" data-sales-url="{{ route('anggota.analytics.salesData', ['anggota' => $anggota->id]) }}" data-chart-url="{{ route('anggota.analytics.data', ['anggota' => $anggota->id]) }}">
                        <div class="statistik-info">
                            <div class="filter-buttons">
                                <button class="filter-btn active" data-period="day">Hari ini</button>
                                <button class="filter-btn " data-period="week">Minggu ini</button>
                                <button class="filter-btn" data-period="month">Bulan ini</button>
                            </div>
                            <div class="info-item">
                                <div class="sales-header">
                                    <span class="info-label">Total Penjualan</span>
                                    <div class="percentage-indicator up">
                                        <span class="arrow">▲</span>
                                        <span class="percent-value">5.2%</span>
                                    </div>
                                </div>
                                <span class="total-amount">Rp 0</span>
                            </div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="myPieChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>

            <div class="right-content">
                <div class="widget-list">
                    <div class="todo-header">
                        <h4>Rincian</h4>
                        {{-- <button id="addTodoBtn" class="add-btn">+</button> --}}
                    </div>

                    <div class="todo-list-container">
                        <div class="todo-group">
                            <h5 class="todo-date-header">Kopi</h5>
                            <ul class="todo-items">
                                <li>
                                    <label>
                                        <input type="checkbox">
                                        <span>Hubungi supplier susu</span>
                                    </label>
                                    <button class="delete-btn">&times;</button>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox">
                                        <span>Jadwalkan rapat mingguan</span>
                                    </label>
                                    <button class="delete-btn">&times;</button>
                                </li>
                            </ul>
                        </div>

                        <div class="todo-group">
                            <h5 class="todo-date-header">Kemarin, 28 Juli 2025</h5>
                            <ul class="todo-items">
                                <li>
                                    <label>
                                        <input type="checkbox" checked>
                                        <span>Beli stok biji kopi Arabica</span>
                                    </label>
                                    <button class="delete-btn">&times;</button>
                                </li>
                            </ul>
                        </div>

                        <div class="todo-group">
                            <h5 class="todo-date-header">Jumat, 25 Juli 2025</h5>
                            <ul class="todo-items">
                                <li>
                                    <label>
                                        <input type="checkbox" checked>
                                        <span>Lakukan inventaris bulanan</span>
                                    </label>
                                    <button class="delete-btn">&times;</button>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="todoModalOverlay" class="modal-overlay">
        <div class="modal-content">
            <button id="closeModalBtn" class="close-btn">&times;</button>
            <h3>Tambah Tugas Baru</h3>
            <input type="text" id="todoInput" placeholder="Tulis tugas di sini...">
            <button id="saveTodoBtn" class="btn">Simpan</button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // === 1. PENGATURAN AWAL ===
            const container = document.querySelector('.statistik-container');
            if (!container) {
                console.error('Elemen .statistik-container tidak ditemukan!');
                return;
            }

            // Ambil kedua URL dari satu tempat
            const salesDataUrl = container.dataset.salesUrl;
            const chartDataUrl = container.dataset.chartUrl;

            // Ambil elemen untuk Total Penjualan
            const totalAmountEl = document.querySelector('.total-amount');
            const percentageIndicatorEl = document.querySelector('.percentage-indicator');
            const percentValueEl = document.querySelector('.percent-value');
            const arrowEl = document.querySelector('.arrow');

            // Ambil elemen untuk Chart
            const ctx = document.getElementById('myPieChart');
            if (!ctx) return;

            // === 2. INISIALISASI CHART.JS (dari kode Anda) ===
            const myChart = new Chart(ctx, {
                type: 'pie'
                , data: {
                    labels: []
                    , datasets: [{
                        label: 'Penjualan Produk'
                        , data: []
                        , backgroundColor: ['#3498db', '#9b59b6', '#2ecc71', '#f1c40f']
                        , borderColor: '#2c3e50'
                        , borderWidth: 3
                    }]
                }
                , options: {
                    responsive: true
                    , plugins: {
                        legend: {
                            position: 'right'
                            , labels: {
                                color: '#ffffff'
                                , font: {
                                    size: 14
                                }
                                , boxWidth: 15
                                , padding: 20
                            }
                        }
                        , tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'currency'
                                        , currency: 'IDR'
                                        , minimumFractionDigits: 0
                                    }).format(context.raw);
                                }
                            }
                        }
                        , datalabels: {
                            color: '#ffffff'
                            , font: {
                                weight: 'bold'
                                , size: 16
                            }
                            , formatter: (value, context) => {
                                const total = context.chart.data.datasets[0].data.reduce((total, datapoint) => total + datapoint, 0);
                                if (value === 0 || total === 0) return '';
                                return `${Math.round((value / total) * 100)}%`;
                            }
                        }
                    }
                }
                , plugins: [ChartDataLabels]
            });

            // === 3. FUNGSI-FUNGSI UNTUK MENGAMBIL DATA ===

            // Fungsi untuk update Total Penjualan
            async function updateSalesTotal(period) {
                totalAmountEl.textContent = 'Memuat...';
                try {
                    const response = await fetch(`${salesDataUrl}?period=${period}`);
                    if (!response.ok) throw new Error('Gagal mengambil data penjualan');
                    const data = await response.json();

                    totalAmountEl.textContent = data.total;
                    percentValueEl.textContent = `${Math.abs(data.percentageChange)}%`;
                    percentageIndicatorEl.classList.remove('up', 'down');
                    if (data.percentageChange >= 0) {
                        percentageIndicatorEl.classList.add('up');
                        arrowEl.textContent = '▲';
                    } else {
                        percentageIndicatorEl.classList.add('down');
                        arrowEl.textContent = '▼';
                    }
                } catch (error) {
                    console.error('Error di updateSalesTotal:', error);
                    totalAmountEl.textContent = 'Gagal Memuat';
                }
            }

            // Fungsi untuk update Chart
            async function updateChart(period) {
                try {
                    const response = await fetch(`${chartDataUrl}?period=${period}`);
                    if (!response.ok) throw new Error('Gagal mengambil data chart');
                    const data = await response.json();

                    if (data.length === 0) {
                        myChart.data.labels = ['Tidak ada penjualan'];
                        myChart.data.datasets[0].data = [1];
                        myChart.data.datasets[0].backgroundColor = ['#4a5568'];
                    } else {
                        myChart.data.labels = data.map(item => item.label);
                        myChart.data.datasets[0].data = data.map(item => Number(item.value));
                        myChart.data.datasets[0].backgroundColor = ['#3498db', '#9b59b6', '#2ecc71', '#f1c40f']; // Reset warna
                    }
                    myChart.update();
                } catch (error) {
                    console.error('Error di updateChart:', error);
                }
            }

            // === 4. EVENT LISTENER TERPADU ===
            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const selectedPeriod = this.dataset.period;

                    // Panggil KEDUA fungsi update
                    updateSalesTotal(selectedPeriod);
                    updateChart(selectedPeriod);
                });
            });

            // === 5. MUAT DATA AWAL ===
            // Secara otomatis klik tombol 'Hari Ini' untuk memuat data awal
            document.querySelector('.filter-btn[data-period="day"]').click();
        });

    </script>


    <script src="{{ asset('js/script.js') }}"></script>
</body>



</html>
