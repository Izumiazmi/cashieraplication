<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembukuan</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        thead {
            background-color: #f2f2f2;
        }

        .total-row td {
            font-weight: bold;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .summary-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-box p {
            margin: 0 0 10px 0;
        }

        .summary-box span {
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Pembukuan</h1>
            <p><strong>Toko:</strong> {{ $anggota->nama_toko }}</p>
            <p><strong>Periode:</strong> {{ $bulanNama }} {{ $tahun }}</p>
        </div>

        <div class="summary-box">
            <p>Total Pendapatan Keseluruhan: <span>Rp {{ number_format($grandTotalPendapatan, 0, ',', '.') }}</span></p>
            <p>Total Penjualan Keseluruhan: <span>{{ number_format($grandTotalTerjual, 0, ',', '.') }} Pcs</span></p>
        </div>

        <div class="section-title">Rangkuman per Minggu</div>
        <table>
            <thead>
                <tr>
                    <th>Minggu</th>
                    <th>Pendapatan Kopi (Rp)</th>
                    <th>Penjualan Kopi (Pcs)</th>
                    <th>Pendapatan Non-Kopi (Rp)</th>
                    <th>Penjualan Non-Kopi (Pcs)</th>
                    <th>Pendapatan Makanan (Rp)</th>
                    <th>Penjualan Makanan (Pcs)</th>
                    <th>Total Pendapatan (Rp)</th>
                    <th>Total Penjualan (Pcs)</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 4; $i++) {{-- HITUNG: Total per baris (mingguan) --}} @php $totalPendapatanMingguan=$chartDataPendapatan['kopi'][$i] + $chartDataPendapatan['non_kopi'][$i] + $chartDataPendapatan['makanan'][$i]; $totalPenjualanMingguan=$chartDataPenjualan['kopi'][$i] + $chartDataPenjualan['non_kopi'][$i] + $chartDataPenjualan['makanan'][$i]; @endphp <tr>
                    <td>Minggu {{ $i + 1 }}</td>
                    <td>{{ number_format($chartDataPendapatan['kopi'][$i], 0, ',', '.') }}</td>
                    <td>{{ number_format($chartDataPenjualan['kopi'][$i], 0, ',', '.') }}</td>
                    <td>{{ number_format($chartDataPendapatan['non_kopi'][$i], 0, ',', '.') }}</td>
                    <td>{{ number_format($chartDataPenjualan['non_kopi'][$i], 0, ',', '.') }}</td>
                    <td>{{ number_format($chartDataPendapatan['makanan'][$i], 0, ',', '.') }}</td>
                    <td>{{ number_format($chartDataPenjualan['makanan'][$i], 0, ',', '.') }}</td>
                    <td><strong>{{ number_format($totalPendapatanMingguan, 0, ',', '.') }}</strong></td>
                    <td><strong>{{ number_format($totalPenjualanMingguan, 0, ',', '.') }}</strong></td>
                    </tr>
                    @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong>{{ number_format(array_sum($chartDataPendapatan['kopi']), 0, ',', '.') }}</strong></td>
                    <td><strong>{{ number_format(array_sum($chartDataPenjualan['kopi']), 0, ',', '.') }}</strong></td>
                    <td><strong>{{ number_format(array_sum($chartDataPendapatan['non_kopi']), 0, ',', '.') }}</strong></td>
                    <td><strong>{{ number_format(array_sum($chartDataPenjualan['non_kopi']), 0, ',', '.') }}</strong></td>
                    <td><strong>{{ number_format(array_sum($chartDataPendapatan['makanan']), 0, ',', '.') }}</strong></td>
                    <td><strong>{{ number_format(array_sum($chartDataPenjualan['makanan']), 0, ',', '.') }}</strong></td>
                    @php
                    $grandTotalPendapatan = array_sum($chartDataPendapatan['kopi']) + array_sum($chartDataPendapatan['non_kopi']) + array_sum($chartDataPendapatan['makanan']);
                    $grandTotalPenjualan = array_sum($chartDataPenjualan['kopi']) + array_sum($chartDataPenjualan['non_kopi']) + array_sum($chartDataPenjualan['makanan']);
                    @endphp
                    <td><strong>{{ number_format($grandTotalPendapatan, 0, ',', '.') }}</strong></td>
                    <td><strong>{{ number_format($grandTotalPenjualan, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="section-title">Detail Penjualan Produk</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Jenis</th>
                    <th>Total Terjual</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailProduk as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->jenis }}</td>
                    <td>{{ $item->total_terjual }} Pcs</td>
                    <td>Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total Keseluruhan :</td>
                    <td>{{ $grandTotalTerjual }} Pcs</td>
                    <td>Rp {{ number_format($grandTotalPendapatan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
