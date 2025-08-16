<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data {{ $bulan }} {{ $tahun }} | {{ $anggota->nama_owner }}</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
    <style>
        .statistik-container {
            display: flex;
            flex-direction: row;
            align-items: stretch;
            gap: 2rem;
        }

        .statistik-info {
            flex: 1;
            /* Biarkan bisa tumbuh dan menyusut */
            max-width: 380px;
            /* Batasi lebar maksimumnya di 380px pada layar besar */
            min-width: 280px;
        }

        .chart-wrapper {
            flex: 1;
            min-width: 0;
            height: 450px;
        }

        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .card {
            padding: 20px;
            border-radius: 8px;
        }

        .info-item {
            padding: 15px;
            border-radius: 6px;
        }

        .total-amount {
            font-size: 2rem;
            font-weight: bold;
        }

        .total-penjualan {
            margin-top: 20px;
            /* Anda bisa sesuaikan angkanya, misal: 1rem, 24px, dll. */
        }

        /* --- ATURAN RESPONSIVE UNTUK LAYAR KECIL --- */
        /* Kode ini hanya aktif pada layar dengan lebar 768px ke bawah */
        @media (max-width: 768px) {
            .statistik-container {
                flex-direction: column;
            }

            .statistik-info {
                max-width: 100%;
            }

            .chart-wrapper {
                margin-top: 2rem;
            }

            .total-amount {
                font-size: 1.5rem;
            }
        }

    </style>

</head>

<body class="dashboard-layout pembukuan-detail">
    @include('sidebar')
    <div class="main-content">
        <div class="content-wrapper">
            <div class="left-content">

                <div class="card card-statistik">
                    <h3>Statistik Pendapatan - {{ $bulan }} {{ $tahun }}</h3>
                    <div class="statistik-container">
                        <div class="statistik-info">
                            <div class="filter-buttons">
                                <button class="filter-btn active" data-kategori="semua">Semua</button>
                                <button class="filter-btn " data-kategori="kopi">Kopi</button>
                                <button class="filter-btn" data-kategori="non-kopi">Non-Kopi</button>
                                <button class="filter-btn" data-kategori="makanan">Makanan & Topping</button>
                            </div>

                            <div class="info-item total-pendapatan">
                                <div class="sales-header">
                                    <span class="info-label">Total Pendapatan</span>
                                    <div id="pendapatan-indicator" class="percentage-indicator up">
                                        <span id="pendapatan-arrow" class="arrow">▲</span>
                                        <span id="pendapatan-percent-value" class="percent-value">--%</span>
                                    </div>
                                </div>
                                <span class="total-amount">Rp 1.250.000</span>
                            </div>
                            <div class="info-item total-penjualan">
                                <div class="sales-header">
                                    <span class="info-label">Total Penjualan</span>
                                    <div id="penjualan-indicator" class="percentage-indicator up">
                                        <span id="penjualan-arrow" class="arrow">▲</span>
                                        <span id="penjualan-percent-value" class="percent-value">--%</span>
                                    </div>
                                </div>
                                <span class="total-amount">1.250.000</span>
                            </div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="pendapatanBulananChart"></canvas>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="penjualanBulananChart"></canvas>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Total Terjual</th>
                                    <th>Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detailProduk as $produk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $produk->nama }}</td>
                                    <td>{{ $produk->jenis }}</td>
                                    <td>{{ $produk->total_terjual }} Pcs</td>
                                    <td>Rp. {{ number_format($produk->total_pendapatan, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align: center;">Tidak ada data untuk ditampilkan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-footer">
                                    <td colspan="3" style="font-weight: bold;">Total Keseluruhan :</td>
                                    <td style="font-weight: bold;">{{ number_format($grandTotalTerjual) }} Pcs</td>
                                    <td style="font-weight: bold;">Rp. {{ number_format($grandTotalPendapatan, 0, ',', '.') }}</td>

                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil DUA set data dari controller
            const dataPendapatan = {
                kopi: @json($chartDataPendapatan['kopi'])
                , non_kopi: @json($chartDataPendapatan['non_kopi'])
                , makanan: @json($chartDataPendapatan['makanan'])
            };
            const dataPenjualan = {
                kopi: @json($chartDataPenjualan['kopi'])
                , non_kopi: @json($chartDataPenjualan['non_kopi'])
                , makanan: @json($chartDataPenjualan['makanan'])
            };

            const totalPendapatanData = @json($infoBoxTotals);
            const totalPenjualanData = @json($infoBoxTotalsPenjualan);
            const dataPersentase = @json($dataPersentase);

            // Variabel untuk DUA instance chart
            let pendapatanChart = null;
            let penjualanChart = null;

            // ===============================================
            // FUNGSI BARU UNTUK UPDATE INDIKATOR PERSEN
            // ===============================================
            function updatePercentageIndicators(filterKey = 'semua') {
                const pendapatanIndicator = document.getElementById('pendapatan-indicator');
                const penjualanIndicator = document.getElementById('penjualan-indicator');

                // Ambil data untuk filter yang dipilih
                const indicatorData = dataPersentase[filterKey];

                // PENTING: Cek apakah data untuk kunci ini ada.
                // Jika tidak ada, sembunyikan indikator dan hentikan fungsi.
                if (!indicatorData) {
                    pendapatanIndicator.style.display = 'none';
                    penjualanIndicator.style.display = 'none';
                    return; // Keluar dari fungsi untuk mencegah eror
                }

                // Jika data ada, tampilkan kembali indikator
                pendapatanIndicator.style.display = 'flex';
                penjualanIndicator.style.display = 'flex';

                // Update Indikator Pendapatan
                const pPendapatan = indicatorData.pendapatan;
                document.getElementById('pendapatan-arrow').textContent = pPendapatan.status === 'up' ? '▲' : '▼';
                document.getElementById('pendapatan-percent-value').textContent = `${Math.abs(pPendapatan.nilai)}%`;
                pendapatanIndicator.className = `percentage-indicator ${pPendapatan.status}`;

                // Update Indikator Penjualan
                const pPenjualan = indicatorData.penjualan;
                document.getElementById('penjualan-arrow').textContent = pPenjualan.status === 'up' ? '▲' : '▼';
                document.getElementById('penjualan-percent-value').textContent = `${Math.abs(pPenjualan.nilai)}%`;
                penjualanIndicator.className = `percentage-indicator ${pPenjualan.status}`;
            }



            function updateInfoBoxes(filterKey = 'semua') {
                const pendapatanEl = document.querySelector('.total-pendapatan .total-amount');
                const penjualanEl = document.querySelector('.total-penjualan .total-amount');

                // Ambil nilai baru dari data
                const newPendapatan = totalPendapatanData[filterKey] || 0;
                const newPenjualan = totalPenjualanData[filterKey] || 0;

                // Update teks di HTML dengan format angka Indonesia
                pendapatanEl.textContent = `Rp ${newPendapatan.toLocaleString('id-ID')}`;
                penjualanEl.textContent = newPenjualan.toLocaleString('id-ID');
            }


            // Fungsi tema yang sudah diperbaiki
            function getCurrentTheme() {
                if (document.documentElement.getAttribute('data-theme') === 'dark') return 'dark';
                if (document.body.classList.contains('dark-theme') || document.documentElement.classList.contains('dark-theme')) return 'dark';
                const dashboardLayout = document.querySelector('.dashboard-layout');
                if (dashboardLayout && dashboardLayout.classList.contains('dark-theme')) return 'dark';
                return 'light';
            }

            function getThemeColors() {
                // NOTE: Kode Anda terbalik, tema 'dark' seharusnya menggunakan warna terang.
                // Saya sesuaikan logikanya.
                const theme = getCurrentTheme();
                if (theme === 'dark') {
                    return {
                        textColor: '#f5f2f2ff'
                        , gridColor: 'rgba(255, 255, 255, 0.2)'
                        , titleColor: '#f1ededff'
                    , };
                } else { // light theme
                    return {
                        textColor: '#f5ececff'
                        , gridColor: 'rgba(0, 0, 0, 0.1)'
                        , titleColor: '#f5f0f0ff'
                    , };
                }
            }

            // =====================================
            // FUNGSI UNTUK CHART 1: PENDAPATAN (Rp)
            // =====================================
            function createPendapatanChart(showDatasets = ['kopi', 'non_kopi', 'makanan']) {
                if (pendapatanChart) {
                    pendapatanChart.destroy();
                }
                const ctx = document.getElementById('pendapatanBulananChart').getContext('2d');
                const themeColors = getThemeColors();

                const datasets = [];
                if (showDatasets.includes('kopi')) datasets.push({
                    label: 'Kopi'
                    , data: dataPendapatan.kopi
                    , backgroundColor: '#3498db'
                });
                if (showDatasets.includes('non_kopi')) datasets.push({
                    label: 'Non-Kopi'
                    , data: dataPendapatan.non_kopi
                    , backgroundColor: '#2ecc71'
                });
                if (showDatasets.includes('makanan')) datasets.push({
                    label: 'Makanan'
                    , data: dataPendapatan.makanan
                    , backgroundColor: '#9b59b6'
                });

                pendapatanChart = new Chart(ctx, {
                    type: 'bar'
                    , data: {
                        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4']
                        , datasets: datasets
                    }
                    , options: {
                        responsive: true
                        , maintainAspectRatio: false
                        , scales: {
                            y: {
                                beginAtZero: true
                                , grid: {
                                    color: themeColors.gridColor
                                }
                                , ticks: {
                                    color: themeColors.textColor
                                    , callback: function(value) {
                                        // --- PERUBAHAN DI SINI ---
                                        // Menggunakan opsi notation: 'compact' untuk menyingkat angka
                                        return new Intl.NumberFormat('id-ID', {
                                            style: 'currency'
                                            , currency: 'IDR'
                                            , notation: 'compact', // Tambahkan ini
                                            compactDisplay: 'short', // Tambahkan ini
                                            minimumFractionDigits: 0
                                            , maximumFractionDigits: 1 // Tampilkan 1 angka di belakang koma jika perlu (misal: 1,6 Jt)
                                        }).format(value);
                                    }
                                }


                            }
                            , x: {
                                grid: {
                                    color: themeColors.gridColor
                                }
                                , ticks: {
                                    color: themeColors.textColor
                                    , font: {
                                        weight: 'bold'
                                    }
                                }
                            }
                        }
                        , plugins: {
                            title: {
                                display: true
                                , text: 'Grafik Pendapatan per Minggu (Rp)'
                                , color: themeColors.titleColor
                                , font: {
                                    size: 16
                                    , weight: 'bold'
                                }
                            }
                            , legend: {
                                position: 'right'
                                , labels: {
                                    color: themeColors.textColor
                                    , usePointStyle: true
                                    , pointStyle: 'circle'
                                }
                            }
                            , tooltip: {
                                callbacks: {
                                    label: (context) => `${context.dataset.label}: Rp ${context.parsed.y.toLocaleString('id-ID')}`
                                }
                            }
                        }
                    }
                });
            }

            // =====================================
            // FUNGSI UNTUK CHART 2: PENJUALAN (Pcs)
            // =====================================
            function createPenjualanChart(showDatasets = ['kopi', 'non_kopi', 'makanan']) {
                if (penjualanChart) {
                    penjualanChart.destroy();
                }
                const ctx = document.getElementById('penjualanBulananChart').getContext('2d');
                const themeColors = getThemeColors();

                const datasets = [];
                if (showDatasets.includes('kopi')) datasets.push({
                    label: 'Kopi'
                    , data: dataPenjualan.kopi
                    , backgroundColor: '#3498db'
                });
                if (showDatasets.includes('non_kopi')) datasets.push({
                    label: 'Non-Kopi'
                    , data: dataPenjualan.non_kopi
                    , backgroundColor: '#2ecc71'
                });
                if (showDatasets.includes('makanan')) datasets.push({
                    label: 'Makanan'
                    , data: dataPenjualan.makanan
                    , backgroundColor: '#9b59b6'
                });

                penjualanChart = new Chart(ctx, {
                    type: 'bar'
                    , data: {
                        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4']
                        , datasets: datasets
                    }
                    , options: {
                        responsive: true
                        , maintainAspectRatio: false
                        , scales: {
                            y: {
                                beginAtZero: true
                                , grid: {
                                    color: themeColors.gridColor
                                }
                                , ticks: {
                                    color: themeColors.textColor
                                    , stepSize: 1
                                }
                            }
                            , x: {
                                grid: {
                                    color: themeColors.gridColor
                                }
                                , ticks: {
                                    color: themeColors.textColor
                                    , font: {
                                        weight: 'bold'
                                    }
                                }
                            }
                        }
                        , plugins: {
                            title: {
                                display: true
                                , text: 'Grafik Penjualan per Minggu (Pcs)'
                                , color: themeColors.titleColor
                                , font: {
                                    size: 16
                                    , weight: 'bold'
                                }
                            }
                            , legend: {
                                position: 'right'
                                , labels: {
                                    color: themeColors.textColor
                                    , usePointStyle: true
                                    , pointStyle: 'circle'
                                }
                            }
                            , tooltip: {
                                callbacks: {
                                    label: (context) => `${context.dataset.label}: ${context.parsed.y} Pcs`
                                }
                            }
                        }
                    }
                });
            }

            // Panggil kedua fungsi untuk membuat chart
            createPendapatanChart();
            createPenjualanChart();
            updateInfoBoxes('semua');
            updatePercentageIndicators('semua');

            // Update event listener untuk filter agar me-refresh kedua chart
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const text = this.textContent.trim();
                    let filter = ['kopi', 'non_kopi', 'makanan'];
                    let infoBoxKey = 'semua';

                    if (text === 'Kopi') filter = ['kopi'];
                    if (text === 'Non-Kopi') filter = ['non_kopi'];
                    if (text.includes('Makanan')) filter = ['makanan'];
                    if (text === 'Kopi') {
                        chartFilter = ['kopi'];
                        infoBoxKey = 'kopi';
                    } else if (text === 'Non-Kopi') {
                        chartFilter = ['non_kopi'];
                        infoBoxKey = 'non_kopi';
                    } else if (text.includes('Makanan')) {
                        chartFilter = ['makanan'];
                        infoBoxKey = 'makanan';
                    }

                    createPendapatanChart(filter);
                    createPenjualanChart(filter);
                    updateInfoBoxes(infoBoxKey);
                    updatePercentageIndicators(infoBoxKey);
                });
            });
        });

    </script>


    <script src="{{ asset('js/script.js') }}"></script>
</body>



</html>
