<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminTodo;
use App\Models\Anggota;

class AdminDashboardController extends Controller
{
    public function index($token)
    {
        // Ambil semua data todos
        $todos = AdminTodo::orderBy('date', 'desc')->get()->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->date)->translatedFormat('l, d F Y');
        });

        $anggotaAktif = Anggota::where('status', 'aktif')->count();
        $anggotaNonAktif = Anggota::where('status', 'nonaktif')->count();
        $totalAnggota = $anggotaAktif + $anggotaNonAktif;

        // 2. Siapkan data untuk chart dalam format array
        $chartData = [
            'aktif' => $anggotaAktif,
            'nonAktif' => $anggotaNonAktif,
        ];

        // 3. Siapkan data untuk kartu analitik lainnya (opsional)
        $analytics = [
            'totalAnggota' => $totalAnggota,
            'anggotaAktif' => $anggotaAktif,
            'anggotaNonAktif' => $anggotaNonAktif
        ];
        // dd($chartData);
        // 4. Kirim semua data ke view
        return view('dashboard_admin', [
            'token' => $token,
            'todos' => $todos,
            'chartData' => $chartData,
            'analytics' => $analytics,
            
        ]);
    }
}
