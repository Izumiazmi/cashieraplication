<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanHarianController extends Controller
{

    public function index(Request $request, Anggota $anggota)
    {
        // 2. Ambil input tanggal dari request
        $tanggalPencarian = $request->input('tanggal');

        // Langkah 1: Buat subquery untuk menghitung total item terjual per hari
        $penjualanSubquery = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->select(
                DB::raw('DATE(transactions.created_at) as tanggal_penjualan'),
                DB::raw('SUM(transaction_details.jumlah) as total_penjualan')
            )
            ->where('transactions.anggota_id', $anggota->id);

        // 3. Tambahkan filter tanggal jika ada input pencarian
        if ($tanggalPencarian) {
            $penjualanSubquery->whereDate('transactions.created_at', $tanggalPencarian);
        }

        $penjualanSubquery->groupBy('tanggal_penjualan');


        // Langkah 2: Query utama untuk total pendapatan, digabung dengan subquery di atas
        $laporanQuery = DB::table('transactions')
            ->joinSub($penjualanSubquery, 'penjualan', function ($join) {
                $join->on(DB::raw('DATE(transactions.created_at)'), '=', 'penjualan.tanggal_penjualan');
            })
            ->where('transactions.anggota_id', $anggota->id);

        // 3. Tambahkan filter tanggal di sini juga
        if ($tanggalPencarian) {
            $laporanQuery->whereDate('transactions.created_at', $tanggalPencarian);
        }

        $laporanHarian = $laporanQuery->select(
            'penjualan.tanggal_penjualan as tanggal',
            'penjualan.total_penjualan as grand_total_penjualan',
            DB::raw('SUM(transactions.total_harga) as grand_total_pendapatan')
        )
            ->groupBy('tanggal', 'grand_total_penjualan')
            ->orderBy('tanggal', 'desc')
            ->paginate(30);

        return view('data_harian', compact('anggota', 'laporanHarian'));
    }

    public function show(Anggota $anggota, $tanggal)
    {
        $targetTanggal = Carbon::parse($tanggal)->setTimezone('Asia/Jakarta');
        $tanggalSebelumnya = $targetTanggal->copy()->subDay();

        $transactions = Transaction::where('anggota_id', $anggota->id)
            ->whereDate('created_at', $targetTanggal)
            ->get();

        if ($transactions->isEmpty()) {
            return view('emty_file', compact('anggota', 'targetTanggal'));
        }

        $transactionIds = $transactions->pluck('id');

        // Query untuk detail produk di tabel bawah
        $detailProduk = DB::table('transaction_details')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
            ->whereIn('transaction_details.transaction_id', $transactionIds)
            ->select(
                'menus.nama',
                'menus.jenis',
                DB::raw('SUM(transaction_details.jumlah) as total_terjual'),
                DB::raw('SUM(transaction_details.harga * transaction_details.jumlah) as total_pendapatan')
            )
            ->groupBy('menus.id', 'menus.nama', 'menus.jenis')
            ->orderBy('total_terjual', 'desc')
            ->get();

        // Query BARU untuk data grafik dan Info Box
        $dataKategori = DB::table('transaction_details')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
            ->whereIn('transaction_details.transaction_id', $transactionIds)
            ->select(
                'menus.jenis',
                DB::raw('SUM(transaction_details.jumlah) as total_penjualan'),
                DB::raw('SUM(transaction_details.harga * transaction_details.jumlah) as total_pendapatan')
            )
            ->groupBy('menus.jenis')
            ->get();

        // Memproses data untuk dikirim ke Chart.js
        $chartLabels = $dataKategori->pluck('jenis');
        $chartDataPendapatan = $dataKategori->pluck('total_pendapatan');
        $chartDataPenjualan = $dataKategori->pluck('total_penjualan');

        // Grand Total untuk Info Box dan footer tabel
        $grandTotalTerjual = $chartDataPenjualan->sum();
        $grandTotalPendapatan = $chartDataPendapatan->sum();

        // Data Persentase
        $dataPersentase = $this->calculatePercentageChange($anggota, $targetTanggal, $tanggalSebelumnya);

        return view('data_harian_detail', compact(
            'anggota',
            'targetTanggal',
            'detailProduk',
            'grandTotalTerjual',
            'grandTotalPendapatan',
            'chartLabels',
            'chartDataPendapatan',
            'chartDataPenjualan',
            'dataPersentase'
        ));
    }

    private function getChartData($transactionIds, $column)
    {
        $kategori = ['Kopi', 'Non-Kopi', 'Makanan'];
        $result = [];

        foreach ($kategori as $jenis) {
            $query = DB::table('transaction_details')
                ->join('menus', 'transaction_details.menu_id', '=', 'menus.id') // <-- NAMA TABEL DIPERBARUI
                ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->whereIn('transaction_details.transaction_id', $transactionIds)
                ->where('menus.jenis', $jenis) // <-- NAMA TABEL DIPERBARUI
                ->select(
                    DB::raw('HOUR(transactions.created_at) as jam'),
                    $column === 'total_pendapatan'
                        ? DB::raw('SUM(transaction_details.harga * transaction_details.jumlah) as total')
                        : DB::raw('SUM(transaction_details.jumlah) as total')
                )
                ->groupBy('jam')
                ->pluck('total', 'jam');

            $hourlyData = array_fill_keys(range(8, 22), 0);

            foreach ($query as $jam => $total) {
                if (isset($hourlyData[$jam])) {
                    $hourlyData[$jam] = $total;
                }
            }
            $result[str_replace('-', '_', strtolower($jenis))] = array_values($hourlyData);
        }
        return $result;
    }

    private function getTotalsByCategory($transactionIds)
    {
        $query = DB::table('transaction_details')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id') // <-- NAMA TABEL DIPERBARUI
            ->whereIn('transaction_details.transaction_id', $transactionIds)
            ->select(
                'menus.jenis', // <-- NAMA TABEL DIPERBARUI
                DB::raw('SUM(transaction_details.harga * transaction_details.jumlah) as total_pendapatan')
            )
            ->groupBy('menus.jenis') // <-- NAMA TABEL DIPERBARUI
            ->pluck('total_pendapatan', 'jenis');

        return [
            'semua' => $query->sum(),
            'kopi' => $query->get('Kopi', 0),
            'non_kopi' => $query->get('Non-Kopi', 0),
            'makanan' => $query->get('Makanan', 0),
        ];
    }

    private function calculatePercentageChange(Anggota $anggota, $currentDate, $previousDate)
    {
        $totalHariIni = Transaction::where('anggota_id', $anggota->id)->whereDate('created_at', $currentDate)->sum('total_harga');
        $totalKemarin = Transaction::where('anggota_id', $anggota->id)->whereDate('created_at', $previousDate)->sum('total_harga');

        if ($totalKemarin > 0) {
            $perubahan = (($totalHariIni - $totalKemarin) / $totalKemarin) * 100;
        } else if ($totalHariIni > 0) {
            $perubahan = 100;
        } else {
            $perubahan = 0;
        }

        return [
            'semua' => [
                'pendapatan' => ['nilai' => round($perubahan, 1), 'status' => $perubahan >= 0 ? 'up' : 'down'],
                'penjualan' => ['nilai' => round($perubahan, 1), 'status' => $perubahan >= 0 ? 'up' : 'down']
            ]
        ];
    }
}
