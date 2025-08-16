<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Anggota;
use App\Models\TransactionDetail;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $anggota = Auth::user();

        return view('dashboard1', ['anggota' => $anggota]);
    }

    public function getSalesData(Request $request) // <-- Hapus Anggota $anggota dari sini
    {
        // Ambil ID pengguna yang sedang login
        $anggotaId = Auth::guard('anggota')->id();

        if (!$anggotaId) {
            return response()->json(['error' => 'Tidak terautentikasi'], 401);
        }

        $period = $request->query('period', 'day');

        // (Logika penentuan tanggal tetap sama, tidak perlu diubah)
        $currentStartDate = now()->startOfDay();
        $currentEndDate = now()->endOfDay();
        $previousStartDate = now()->subDay()->startOfDay();
        $previousEndDate = now()->subDay()->endOfDay();

        if ($period === 'week') {
            Carbon::setWeekStartsAt(Carbon::MONDAY);
            $currentStartDate = now()->startOfWeek();
            $currentEndDate = now()->endOfWeek();
            $previousStartDate = now()->subWeek()->startOfWeek();
            $previousEndDate = now()->subWeek()->endOfWeek();
        } elseif ($period === 'month') {
            $currentStartDate = now()->startOfMonth();
            $currentEndDate = now()->endOfMonth();
            $previousStartDate = now()->subMonth()->startOfMonth();
            $previousEndDate = now()->subMonth()->endOfMonth();
        }

        // Gunakan $anggotaId yang didapat dari Auth
        $currentSales = DB::table('transactions')
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.anggota_id', $anggotaId)
            ->whereBetween('transactions.created_at', [$currentStartDate, $currentEndDate])
            ->sum(DB::raw('transaction_details.jumlah * transaction_details.harga'));

        // Query untuk penjualan sebelumnya (dengan cara yang benar)
        $previousSales = DB::table('transactions')
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.anggota_id', $anggotaId)
            ->whereBetween('transactions.created_at', [$previousStartDate, $previousEndDate])
            ->sum(DB::raw('transaction_details.jumlah * transaction_details.harga'));


        // Logika persentase (tetap sama)
        $percentageChange = 0;
        if ($previousSales > 0) {
            $percentageChange = (($currentSales - $previousSales) / $previousSales) * 100;
        } elseif ($currentSales > 0) {
            $percentageChange = 100;
        }

        return response()->json([
            'total' => 'Rp ' . number_format($currentSales, 0, ',', '.'),
            'percentageChange' => round($percentageChange, 1)
        ]);
    }


    public function getChartData(Request $request, Anggota $anggota)
    {
        // Keamanan
        if (Auth::user()->id !== $anggota->id) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $period = $request->query('period', 'day');

        // Tentukan rentang tanggal dengan benar
        $endDate = now();
        $startDate = now();

        if ($period === 'week') {
            Carbon::setWeekStartsAt(Carbon::MONDAY);
            $startDate = now()->startOfWeek();
        } elseif ($period === 'month') {
            $startDate = now()->startOfMonth();
        } else { // 'day'
            $startDate = now()->startOfDay();
        }

        // UBAH QUERY DAN LOGIKA SETELAHNYA
        $salesData = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
            ->where('transactions.anggota_id', $anggota->id)
            ->whereBetween('transactions.created_at', [$startDate, now()])
            ->groupBy('menus.jenis')
            ->select('menus.jenis as label', DB::raw('SUM(transaction_details.jumlah * transaction_details.harga) as value'))
            // Hanya ambil data yang totalnya lebih dari 0
            ->having('value', '>', 0)
            ->get();

        // Langsung kembalikan hasilnya. 
        // Jika hari ini hanya ada penjualan Non-Kopi, hasilnya akan menjadi:
        // [{"label": "Non-Kopi", "value": 1250000}]    
        return response()->json($salesData);
    }
}
