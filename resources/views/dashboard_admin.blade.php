<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixed Layout Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
</head>

<body class="dashboard-layout">
    @include('sidebar_admin')

    <div class="main-content">
        <div class="content-wrapper">
            <div class="left-content">
                <div class="card">
                    <h3>Analitik</h3>
                    <div class="menu-grid">
                        <button class="btn" style="background-color: #0f71d3ff;">
                            <div class="menu-name">Anggota Active</div>
                            <div class="menu-price">{{ $analytics['anggotaAktif'] }} Member</div>
                        </button>
                        <button class="btn" style="background-color: #7a22a3ff;">
                            <div class="menu-name">Anggota Non-Active</div>
                            <div class="menu-price">{{ $analytics['anggotaNonAktif'] }} Member</div>
                        </button>
                    </div>
                </div>
                <div class="card card-statistik">
                    <h3>Statistik</h3>
                    <div class="statistik-container">
                        <div class="statistik-info">
                            <div class="filter-buttons">
                                <button class="filter-btn active">Hari ini</button>
                                <button class="filter-btn">Minggu ini</button>
                                <button class="filter-btn">Bulan ini</button>
                            </div>
                            <div class="info-item">
                                <div class="sales-header">
                                    <span class="info-label">Total Penjualan</span>
                                    <div class="percentage-indicator up">
                                        <span class="arrow">▲</span>
                                        <span class="percent-value">5.2%</span>
                                    </div>
                                </div>
                                <span class="total-amount">Rp 1.250.000</span>
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
                        <h4>To Do List</h4>
                        <button id="addTodoBtn" class="add-btn">+</button>
                    </div>

                    <div class="todo-list-container">
                        @foreach ($todos as $date => $todoItems)
                        <div class="todo-group">
                            @if ($date == \Carbon\Carbon::today()->translatedFormat('l, d F Y'))
                            <h5 class="todo-date-header">Hari Ini, {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</h5>
                            @else
                            <h5 class="todo-date-header">{{ $date }}</h5>
                            @endif
                            <ul class="todo-items">
                                @foreach ($todoItems as $todo)
                                <li>
                                    <label>
                                        <input type="checkbox">
                                        <span>{{ $todo->text }}</span>
                                    </label>
                                    <form action="{{ route('admin.todos.delete', ['token' => $token, 'todo' => $todo->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn">&times;</button>
                                    </form>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="todoModalOverlay" class="modal-overlay">
        <div class="modal-content">
            <button id="closeModalBtn" class="close-btn">&times;</button>
            <h3>Tambah Tugas Baru</h3>

            <form id="todoForm" method="POST" action="{{ route('admin.todos.store', ['token' => config('app.admin_route_token')]) }}">
                @csrf
                <input type="text" name="text" id="todoInput" placeholder="Tulis tugas di sini..." autocomplete="off" required>
                <input type="hidden" name="date" value="{{ \Carbon\Carbon::today()->toDateString() }}">
                <button type="submit" class="btn">Simpan</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        let myChart;

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('myPieChart');
            const themeToggle = document.getElementById('themeToggle');
            const chartDataFromPHP = @json(isset($chartData) ? $chartData : ['aktif' => 0, 'nonAktif' => 0]);

            // SET DEFAULT: TEMA GELAP AKTIF
            document.body.classList.add('dark-theme');
            themeToggle.checked = true;

            const data = {
                labels: ['Active', 'Non-Active']
                , datasets: [{
                    label: 'Member'
                    , data: [
                        chartDataFromPHP.aktif
                        , chartDataFromPHP.nonAktif
                    ]
                    , backgroundColor: ['#196b04ff', '#800a0aff']
                    , borderColor: '#2d2d2d'
                    , borderWidth: 3
                }]
            };

            const config = {
                type: 'pie'
                , data: data
                , options: {
                    responsive: true
                    , plugins: {
                        legend: {
                            display: true
                            , position: 'right'
                            , labels: {
                                color: '#ffffff', // warna default dark
                                font: {
                                    size: 14
                                }
                                , boxWidth: 15
                                , padding: 20
                            }
                        }
                        , datalabels: {
                            color: '#ffffff'
                            , font: {
                                weight: 'bold'
                                , size: 16
                            }
                            , formatter: (value, context) => {
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = (value / total * 100).toFixed(1) + '%';
                                return percentage;
                                // return value; // Cukup tampilkan angka aslinya
                            }
                        }
                    }
                }
                , plugins: [ChartDataLabels]
            };

            myChart = new Chart(ctx, config);

            // Fungsi untuk update tema dan chart
            function updateTheme(isDark) {
                document.body.classList.toggle('dark-theme', isDark);

                const legendColor = isDark ? '#ffffff' : '#2d2d2d';
                const dataLabelColor = isDark ? '#ffffff' : '#000000';

                if (myChart) {
                    myChart.options.plugins.legend.labels.color = legendColor;
                    myChart.options.plugins.datalabels.color = dataLabelColor;
                    myChart.update();
                }
            }

            // Toggle event
            themeToggle.addEventListener('change', function() {
                const isDark = this.checked;
                updateTheme(isDark);
            });

            // Jalankan sekali saat awal load
            updateTheme(true); // karena default-nya adalah dark
        });

    </script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>



</html>
