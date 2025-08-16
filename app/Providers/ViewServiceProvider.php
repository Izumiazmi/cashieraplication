<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('sidebar', function ($view) {

            // Pastikan hanya berjalan jika user sudah login
            if (Auth::check()) {
                // Ambil data transaksi TERAKHIR dari user yang sedang login
                $latestTransaction = DB::table('transactions')
                    ->where('anggota_id', Auth::id())
                    ->select('created_at')
                    ->latest('created_at') // Mengambil yang paling baru
                    ->first();

                $linkTahun = now()->year;  // Default ke tahun ini
                $linkBulan = now()->month; // Default ke bulan ini

                // Jika ada data transaksi, gunakan tahun & bulan dari sana
                if ($latestTransaction) {
                    $tanggalTerbaru = new \Carbon\Carbon($latestTransaction->created_at);
                    $linkTahun = $tanggalTerbaru->year;
                    $linkBulan = $tanggalTerbaru->month;
                }

                // Kirim variabel ke view sidebar
                $view->with([
                    'linkTahun' => $linkTahun,
                    'linkBulan' => $linkBulan
                ]);
            }
        });
    }
}
