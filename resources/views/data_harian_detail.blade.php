<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian - {{ $targetTanggal->translatedFormat('d F Y') }} | {{ $anggota->nama_owner }}</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
    <style>
        /* Style dari contoh sebelumnya bisa Anda letakkan di sini atau di file css utama */
        .statistik-container {
            display: flex;
            flex-direction: row;
            align-items: stretch;
            gap: 2rem;
        }

        .statistik-info {
            flex: 1;
            max-width: 380px;
            min-width: 280px;
        }

        .chart-wrapper {
            flex: 1;
            min-width: 0;
            height: 450px;
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
        }

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
                    {{-- Judul diubah menjadi Harian --}}
                    <h3>Statistik Harian - {{ $targetTanggal->translatedFormat('l, d F Y') }}</h3>
                    <div class="statistik-container">
                        <div class="statistik-info">
                            {{-- Tombol filter kategori dihapus karena grafik sekarang membandingkan metrik, bukan kategori --}}

                            <div class="info-item total-pendapatan">
                                <div class="sales-header">
                                    <span class="info-label">Total Pendapatan</span>
                                    <div id="pendapatan-indicator" class="percentage-indicator up">
                                        <span id="pendapatan-arrow" class="arrow">▲</span>
                                        <span id="pendapatan-percent-value" class="percent-value">--%</span>
                                    </div>
                                </div>
                                {{-- Data diisi dari grandTotalPendapatan --}}
                                <span class="total-amount">Rp {{ number_format($grandTotalPendapatan, 0, ',', '.') }}</span>
                            </div>

                            <div class="info-item total-penjualan">
                                <div class="sales-header">
                                    <span class="info-label">Total Penjualan</span>
                                    <div id="penjualan-indicator" class="percentage-indicator up">
                                        <span id="penjualan-arrow" class="arrow">▲</span>
                                        <span id="penjualan-percent-value" class="percent-value">--%</span>
                                    </div>
                                </div>
                                {{-- Data diisi dari grandTotalTerjual --}}
                                <span class="total-amount">{{ number_format($grandTotalTerjual) }} Pcs</span>
                            </div>
                        </div>
                        <div class="chart-wrapper">
                            {{-- Hanya butuh satu canvas untuk grafik perbandingan --}}
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Menu</th>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==========================================================
            // JAVASCRIPT DIRUBAH TOTAL UNTUK GRAFIK PERBANDINGAN
            // ==========================================================

            // 1. Ambil data yang sudah disiapkan Controller
            const chartLabels = @json($chartLabels); // -> ['Kopi', 'Non-Kopi', 'Makanan']
            const pendapatanData = @json($chartDataPendapatan); // -> [500000, 300000, 200000]
            const penjualanData = @json($chartDataPenjualan); // -> [25, 15, 10]
            const dataPersentase = @json($dataPersentase);

            // 2. Fungsi untuk mengatur warna tema (opsional, bisa disesuaikan)
            function getThemeColors() {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                return {
                    textColor: isDark ? '#f5f2f2' : '#e9e3e3ff'
                    , gridColor: isDark ? 'rgba(255, 255, 255, 0.2)' : 'rgba(238, 232, 232, 0.1)'
                    , titleColor: isDark ? '#f1eded' : '#f3efefff'
                };
            }

            // 3. Fungsi untuk membuat grafik perbandingan
            function createComparisonChart() {
                const ctx = document.getElementById('comparisonChart').getContext('2d');
                const themeColors = getThemeColors();

                new Chart(ctx, {
                    type: 'bar'
                    , data: {
                        labels: chartLabels
                        , datasets: [{
                                label: 'Pendapatan (Rp)'
                                , data: pendapatanData
                                , backgroundColor: '#3498db'
                                , yAxisID: 'yPendapatan', // Tentukan sumbu Y untuk data ini
                            }
                            , {
                                label: 'Penjualan (Pcs)'
                                , data: penjualanData
                                , backgroundColor: '#2ecc71'
                                , yAxisID: 'yPenjualan', // Tentukan sumbu Y untuk data ini
                            }
                        ]
                    }
                    , options: {
                        responsive: true
                        , maintainAspectRatio: false
                        , interaction: {
                            mode: 'index'
                            , intersect: false
                        , }
                        , scales: {
                            // Sumbu Y pertama untuk Pendapatan (di kiri)
                            yPendapatan: {
                                type: 'linear'
                                , position: 'left'
                                , beginAtZero: true
                                , grid: {
                                    drawOnChartArea: false, // Hanya satu grid utama
                                    color: themeColors.gridColor
                                , }
                                , ticks: {
                                    color: themeColors.textColor
                                    , callback: function(value) {
                                        return new Intl.NumberFormat('id-ID', {
                                            style: 'currency'
                                            , currency: 'IDR'
                                            , notation: 'compact'
                                        }).format(value);
                                    }
                                }
                            },
                            // Sumbu Y kedua untuk Penjualan (di kanan)
                            yPenjualan: {
                                type: 'linear'
                                , position: 'right'
                                , beginAtZero: true
                                , grid: {
                                    color: themeColors.gridColor
                                , }
                                , ticks: {
                                    color: themeColors.textColor
                                    , callback: function(value) {
                                        // Pastikan hanya integer yang ditampilkan
                                        if (Math.floor(value) === value) {
                                            return value + ' Pcs';
                                        }
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
                                , text: 'Perbandingan Pendapatan (Rp) dan Penjualan (Pcs)'
                                , color: themeColors.titleColor
                                , font: {
                                    size: 16
                                    , weight: 'bold'
                                }
                            }
                            , legend: {
                                position: 'bottom'
                                , labels: {
                                    color: themeColors.textColor
                                }
                            }
                            , tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.dataset.yAxisID === 'yPendapatan') {
                                            label += new Intl.NumberFormat('id-ID', {
                                                style: 'currency'
                                                , currency: 'IDR'
                                            }).format(context.parsed.y);
                                        } else {
                                            label += context.parsed.y + ' Pcs';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 4. Fungsi untuk update indikator persentase
            function updatePercentageIndicators() {
                const indicatorData = dataPersentase.semua;
                if (!indicatorData) return;

                // Update Indikator Pendapatan
                const pPendapatan = indicatorData.pendapatan;
                document.getElementById('pendapatan-arrow').textContent = pPendapatan.status === 'up' ? '▲' : '▼';
                document.getElementById('pendapatan-percent-value').textContent = `${Math.abs(pPendapatan.nilai)}%`;
                document.getElementById('pendapatan-indicator').className = `percentage-indicator ${pPendapatan.status}`;

                // Update Indikator Penjualan
                const pPenjualan = indicatorData.penjualan;
                document.getElementById('penjualan-arrow').textContent = pPenjualan.status === 'up' ? '▲' : '▼';
                document.getElementById('penjualan-percent-value').textContent = `${Math.abs(pPenjualan.nilai)}%`;
                document.getElementById('penjualan-indicator').className = `percentage-indicator ${pPenjualan.status}`;
            }

            // Panggil fungsi-fungsi di atas
            createComparisonChart();
            updatePercentageIndicators();
        });

    </script>
    <script src="{{ asset('js/script.js') }}"></script>

</body>
</html>
