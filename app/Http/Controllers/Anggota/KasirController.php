<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Anggota;

class KasirController extends Controller
{
    /**
     * Menampilkan halaman kasir.
     */
    public function index(Anggota $anggota)
    {
        // Keamanan: Pastikan anggota yang di URL sama dengan yang login
        if (Auth::user()->id !== $anggota->id) {
            abort(403, 'Akses Ditolak');
        }

        // 1. Ambil semua menu milik anggota, lalu kelompokkan berdasarkan 'jenis'
        $menusPerJenis = $anggota->menus()->get()->groupBy('jenis');

        // 2. Siapkan palet warna yang akan kita gunakan secara berulang
        $colors = [
            '#16a085',
            '#27ae60',
            '#2980b9',
            '#8e44ad',
            '#2c3e50',
            '#f39c12',
            '#d35400',
            '#c0392b',
            '#7f8c8d',
            '#f1c40f',
            '#1abc9c',
            '#3498db'
        ];

        // 3. Kirim data yang sudah dikelompokkan dan palet warna ke view
        return view('kasir', [
            'anggota'       => $anggota,
            'menusPerJenis' => $menusPerJenis,
            'colors'        => $colors
        ]);
    }

    public function store(Request $request, Anggota $anggota)
    {
        // 1. Ambil data pesanan dari request (dikirim oleh JS)
        $orderData = $request->input('order'); // Misal, JS mengirim data dalam format JSON
        $totalHarga = $request->input('total');

        // 2. Buat record transaksi utama
        $transaction = $anggota->transactions()->create([
            'total_harga' => $totalHarga,
        ]);

        // 3. Loop dan simpan setiap item pesanan ke transaction_details
        foreach ($orderData as $item) {
            $transaction->details()->create([
                'menu_id' => $item['id'],
                'nama_pesanan' => $item['nama'],
                'harga' => $item['harga'],
                'jumlah' => $item['jumlah'],
            ]);
        }

        return response()->json(['message' => 'Transaksi berhasil disimpan!']);
    }
}
