<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Anggota; // <-- Import model Anggota
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PembukuanController extends Controller
{
    /**
     * Menampilkan daftar rekapitulasi penjualan per bulan untuk anggota spesifik.
     */
    public function index(Request $request, Anggota $anggota)
    {
        // ... (kode untuk mengambil $bulan dan $tahun tetap sama)
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // --- TAMBAHAN BARU: Mengambil daftar tahun yang tersedia ---
        $availableYears = DB::table('transactions')
            ->where('anggota_id', $anggota->id)
            ->select(DB::raw('YEAR(created_at) as tahun'))
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Pastikan tahun saat ini selalu ada dalam daftar
        $currentYear = date('Y');
        if (!$availableYears->contains($currentYear)) {
            $availableYears->prepend($currentYear);
        }
        // --- AKHIR TAMBAHAN BARU ---

        $query = DB::table('transactions')
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            // ... (sisa query Anda tidak perlu diubah)
            ->where('transactions.anggota_id', $anggota->id)
            ->select(
                DB::raw('YEAR(transactions.created_at) as tahun'),
                DB::raw('MONTH(transactions.created_at) as bulan_angka'),
                DB::raw('SUM(transaction_details.harga * transaction_details.jumlah) as total_pendapatan'),
                DB::raw('SUM(transaction_details.jumlah) as total_penjualan')
            )
            ->whereYear('transactions.created_at', $tahun)
            ->whereMonth('transactions.created_at', $bulan)
            ->groupBy('tahun', 'bulan_angka')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan_angka', 'desc');

        $pembukuan = $query->get()->map(function ($item) {
            $item->bulan_nama = \Carbon\Carbon::create()->month($item->bulan_angka)->translatedFormat('F');
            return $item;
        });

        // Kirim variabel $availableYears ke view
        return view('pembukuan', compact('pembukuan', 'anggota', 'availableYears'));
    }

    /**
     * Menampilkan detail penjualan untuk anggota, bulan, dan tahun tertentu.
     */
    public function show(Anggota $anggota, $tahun, $bulan)
    {

        Carbon::setWeekStartsAt(Carbon::MONDAY);

        // 1. Ambil detail produk (kode ini tidak perlu diubah)
        $detailProduk = DB::table('transaction_details')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.anggota_id', $anggota->id)
            ->whereYear('transactions.created_at', $tahun)
            ->whereMonth('transactions.created_at', $bulan)
            ->select(
                'menus.nama',
                'menus.jenis',
                DB::raw('SUM(transaction_details.jumlah) as total_terjual'),
                DB::raw('SUM(transaction_details.jumlah * transaction_details.harga) as total_pendapatan')
            )
            ->groupBy('menus.nama', 'menus.jenis')
            ->orderBy('total_terjual', 'desc')
            ->get();

        // 2. Siapkan data total (kode ini tidak perlu diubah)
        $infoBoxTotals = [
            'semua' => $detailProduk->sum('total_pendapatan'),
            'kopi' => $detailProduk->where('jenis', 'Kopi')->sum('total_pendapatan'),
            'non_kopi' => $detailProduk->where('jenis', 'Non-Kopi')->sum('total_pendapatan'),
            'makanan' => $detailProduk->where('jenis', 'Makanan')->sum('total_pendapatan'),
        ];
        $infoBoxTotalsPenjualan = [
            'semua' => $detailProduk->sum('total_terjual'),
            'kopi' => $detailProduk->where('jenis', 'Kopi')->sum('total_terjual'),
            'non_kopi' => $detailProduk->where('jenis', 'Non-Kopi')->sum('total_terjual'),
            'makanan' => $detailProduk->where('jenis', 'Makanan')->sum('total_terjual'),
        ];

        // =================================================================== //
        //                      PERUBAHAN UTAMA DI SINI                        //
        // =================================================================== //

        // 3. Inisialisasi DUA array untuk DUA chart
        $chartDataPendapatan = ['kopi' => [0, 0, 0, 0], 'non_kopi' => [0, 0, 0, 0], 'makanan' => [0, 0, 0, 0]];
        $chartDataPenjualan = ['kopi' => [0, 0, 0, 0], 'non_kopi' => [0, 0, 0, 0], 'makanan' => [0, 0, 0, 0]];

        // Ambil data transaksi bulanan
        $transaksiBulanan = DB::table('transaction_details')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.anggota_id', $anggota->id)
            ->whereYear('transactions.created_at', $tahun)
            ->whereMonth('transactions.created_at', $bulan)
            ->select(
                'menus.jenis',
                'transactions.created_at',
                DB::raw('transaction_details.jumlah * transaction_details.harga as pendapatan'),
                'transaction_details.jumlah' // Pastikan 'jumlah' diambil
            )
            ->get();

        // Kelompokkan data ke dalam dua array tersebut
        foreach ($transaksiBulanan as $transaksi) {
            $tanggal = Carbon::parse($transaksi->created_at);

            // SEKARANG PERHITUNGAN INI AKAN BENAR KARENA MINGGU DIMULAI DARI SENIN
            $mingguKe = $tanggal->weekOfMonth - 1; // Gunakan metode yang lebih andal

            if ($mingguKe < 4) {
                $jenisKey = str_replace('-', '_', strtolower($transaksi->jenis));
                if (array_key_exists($jenisKey, $chartDataPendapatan)) {
                    $chartDataPendapatan[$jenisKey][$mingguKe] += $transaksi->pendapatan;
                    $chartDataPenjualan[$jenisKey][$mingguKe] += $transaksi->jumlah;
                }
            }
        }

        $tanggalSaatIni = Carbon::create($tahun, $bulan);
        $tanggalBulanLalu = $tanggalSaatIni->copy()->subMonth();

        // Ambil total bulan lalu, DIKELOMPOKKAN per jenis
        $totalsBulanLalu = DB::table('transaction_details')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.anggota_id', $anggota->id)
            ->whereYear('transactions.created_at', $tanggalBulanLalu->year)
            ->whereMonth('transactions.created_at', $tanggalBulanLalu->month)
            ->select(
                'menus.jenis',
                DB::raw('SUM(transaction_details.jumlah * transaction_details.harga) as total_pendapatan'),
                DB::raw('SUM(transaction_details.jumlah) as total_terjual')
            )
            ->groupBy('menus.jenis')
            ->get()
            ->keyBy(function ($item) {
                return str_replace('-', '_', strtolower($item->jenis));
            }); // Hasilnya: ['kopi' => ..., 'non_kopi' => ...]

        // Siapkan array untuk menampung hasil kalkulasi
        $dataPersentase = [];
        $kategori = ['semua', 'kopi', 'non_kopi', 'makanan'];

        foreach ($kategori as $key) {
            // Ambil total saat ini dari infoBox
            $pendapatanSaatIni = $infoBoxTotals[$key] ?? 0;
            $penjualanSaatIni = $infoBoxTotalsPenjualan[$key] ?? 0;

            // Ambil total bulan lalu dari query baru
            $pendapatanBulanLalu = $totalsBulanLalu[$key]->total_pendapatan ?? 0;
            if ($key === 'semua') { // 'semua' adalah hasil penjumlahan
                $pendapatanBulanLalu = $totalsBulanLalu->sum('total_pendapatan');
            }

            $penjualanBulanLalu = $totalsBulanLalu[$key]->total_terjual ?? 0;
            if ($key === 'semua') {
                $penjualanBulanLalu = $totalsBulanLalu->sum('total_terjual');
            }

            // Logika kalkulasi yang sudah diperbaiki
            $persentasePendapatan = 0;
            if ($pendapatanBulanLalu > 0) {
                $persentasePendapatan = (($pendapatanSaatIni - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100;
            } elseif ($pendapatanSaatIni > 0) { // Jika bulan lalu 0 dan sekarang ada, anggap 100%
                $persentasePendapatan = 100;
            }

            $persentasePenjualan = 0;
            if ($penjualanBulanLalu > 0) {
                $persentasePenjualan = (($penjualanSaatIni - $penjualanBulanLalu) / $penjualanBulanLalu) * 100;
            } elseif ($penjualanSaatIni > 0) {
                $persentasePenjualan = 100;
            }

            // Simpan hasilnya
            $dataPersentase[$key] = [
                'pendapatan' => ['nilai' => round($persentasePendapatan, 1), 'status' => $persentasePendapatan >= 0 ? 'up' : 'down'],
                'penjualan' => ['nilai' => round($persentasePenjualan, 1), 'status' => $persentasePenjualan >= 0 ? 'up' : 'down'],
            ];
        }

        // 4. Siapkan data footer (kode ini tidak perlu diubah)
        $grandTotalTerjual = $detailProduk->sum('total_terjual');
        $grandTotalPendapatan = $detailProduk->sum('total_pendapatan');

        // Kirim KEDUA data chart ke view
        return view('pembukuan_detail', [
            'anggota' => $anggota,
            'tahun' => $tahun,
            'bulan' => Carbon::create()->month($bulan)->locale('id')->monthName,
            'detailProduk' => $detailProduk,
            'chartDataPendapatan' => $chartDataPendapatan,
            'chartDataPenjualan' => $chartDataPenjualan,
            'infoBoxTotals' => $infoBoxTotals,
            'infoBoxTotalsPenjualan' => $infoBoxTotalsPenjualan,
            'dataPersentase' => $dataPersentase,
            'grandTotalTerjual' => $grandTotalTerjual,
            'grandTotalPendapatan' => $grandTotalPendapatan,
        ]);
    }

    /**
     * Helper function untuk mengambil pendapatan mingguan per kategori untuk anggota spesifik.
     */
    private function getWeeklyData(Anggota $anggota, $jenis, $start, $end)
    {
        $data = DB::table('transaction_details')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.anggota_id', $anggota->id)
            ->where('menus.jenis', $jenis)
            ->whereBetween('transactions.created_at', [$start, $end])
            ->select(
                DB::raw('SUM(transaction_details.jumlah * transaction_details.harga) as pendapatan'),
                DB::raw('SUM(transaction_details.jumlah) as terjual')
            )
            ->first();

        return [
            'pendapatan' => (int) $data->pendapatan,
            'terjual' => (int) $data->terjual
        ];
    }

    public function cetakPdf(Anggota $anggota, $tahun, $bulan)
    {
        // Keamanan: Pastikan hanya role 'pro' yang bisa mengakses
        if (Auth::user()->role !== 'pro') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        // Ambil semua data yang dibutuhkan (logika ini bisa Anda salin dari method show)
        $detailProduk = DB::table('transaction_details')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.anggota_id', $anggota->id)
            ->whereYear('transactions.created_at', $tahun)
            ->whereMonth('transactions.created_at', $bulan)
            ->select('menus.nama', 'menus.jenis', DB::raw('SUM(transaction_details.jumlah) as total_terjual'), DB::raw('SUM(transaction_details.jumlah * transaction_details.harga) as total_pendapatan'))
            ->groupBy('menus.nama', 'menus.jenis')->orderBy('total_terjual', 'desc')->get();

        $transaksiBulanan = DB::table('transaction_details')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.anggota_id', $anggota->id)
            ->whereYear('transactions.created_at', $tahun)
            ->whereMonth('transactions.created_at', $bulan)
            ->select('menus.jenis', 'transactions.created_at', DB::raw('transaction_details.jumlah * transaction_details.harga as pendapatan'), 'transaction_details.jumlah')->get();

        $chartDataPendapatan = ['kopi' => [0, 0, 0, 0], 'non_kopi' => [0, 0, 0, 0], 'makanan' => [0, 0, 0, 0]];
        $chartDataPenjualan = ['kopi' => [0, 0, 0, 0], 'non_kopi' => [0, 0, 0, 0], 'makanan' => [0, 0, 0, 0]];

        foreach ($transaksiBulanan as $transaksi) {
            $tanggal = \Carbon\Carbon::parse($transaksi->created_at);
            $mingguKe = floor(($tanggal->day - 1) / 7);
            if ($mingguKe < 4) {
                $jenisKey = str_replace('-', '_', strtolower($transaksi->jenis));
                if (array_key_exists($jenisKey, $chartDataPendapatan)) {
                    $chartDataPendapatan[$jenisKey][$mingguKe] += $transaksi->pendapatan;
                    $chartDataPenjualan[$jenisKey][$mingguKe] += $transaksi->jumlah;
                }
            }
        }

        $grandTotalTerjual = $detailProduk->sum('total_terjual');
        $grandTotalPendapatan = $detailProduk->sum('total_pendapatan');
        $bulanNama = \Carbon\Carbon::create()->month($bulan)->locale('id')->monthName;

        // Siapkan data untuk dikirim ke view PDF
        $data = [
            'anggota' => $anggota,
            'tahun' => $tahun,
            'bulanNama' => $bulanNama,
            'detailProduk' => $detailProduk,
            'grandTotalTerjual' => $grandTotalTerjual,
            'grandTotalPendapatan' => $grandTotalPendapatan,
            'chartDataPendapatan' => $chartDataPendapatan,
            'chartDataPenjualan' => $chartDataPenjualan,
        ];

        // Buat PDF
        $pdf = PDF::loadView('pdf.pembukuan', $data);

        // Buat nama file dinamis
        $namaFile = 'laporan-' . strtolower($anggota->nama_toko) . '-' . strtolower($bulanNama) . '-' . $tahun . '.pdf';

        // Download PDF
        return $pdf->download($namaFile);
    }
}
