<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Anggota;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Anggota $anggota, Request $request)
    {
        // Keamanan: Pastikan yang mengakses adalah pemilik data
        if (Auth::user()->id !== $anggota->id) {
            abort(403, 'Akses Ditolak');
        }

        // 1. Mulai query dasar untuk transaksi anggota ini
        $query = $anggota->transactions()->with('details')->latest();

        // 2. Terapkan filter pencarian jika ada input dari form
        // Filter berdasarkan keyword (kode transaksi)
        $query->when($request->keyword, function ($q, $keyword) {
            // Menggunakan 'where' untuk mencari kecocokan persis atau 'like' untuk pencarian parsial
            return $q->where('id', 'like', "%{$keyword}%");
        });

        // Filter berdasarkan tanggal
        $query->when($request->tanggal, function ($q, $tanggal) {
            // whereDate akan membandingkan hanya bagian tanggal dari kolom created_at
            return $q->whereDate('created_at', $tanggal);
        });

        // 3. Ambil semua data hasil query (tanpa paginasi)
        // Ganti ->paginate(15) menjadi ->get()
        $transactions = $query->get();

        // Kirim data transaksi ke view
        return view('table_transaksi', [
            'anggota' => $anggota,
            'transactions' => $transactions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Anggota $anggota)
    {
        // Validasi data yang masuk
        $request->validate([
            'total' => 'required|integer|min:0',
            'order' => 'required|array|min:1',
            'order.*.id' => 'required|integer|exists:menus,id',
            'order.*.jumlah' => 'required|integer|min:1',
        ]);

        // Gunakan DB Transaction untuk memastikan semua query berhasil atau tidak sama sekali
        DB::beginTransaction();
        try {
            // 1. Buat record transaksi utama
            $transaction = $anggota->transactions()->create([
                'total_harga' => $request->total,
            ]);

            // 2. Loop dan simpan setiap item pesanan ke transaction_details
            foreach ($request->order as $item) {
                $transaction->details()->create([
                    'menu_id' => $item['id'],
                    'nama_pesanan' => $item['nama'],
                    'harga' => $item['harga'],
                    'jumlah' => $item['jumlah'],
                    'tanggal_pesanan' => now(),
                ]);
            }

            // Jika semua berhasil, commit transaksi
            DB::commit();

            $transaction->load('details.menu');

            // 2. Kembalikan semua data yang dibutuhkan sebagai JSON
            return response()->json([
                'message' => 'Transaksi berhasil disimpan!',
                'transaction' => $transaction,
                'anggota' => $anggota, // Data toko/anggota
                'user_role' => Auth::guard('anggota')->user()->role // Role user
            ]);
        } catch (\Exception $e) {
            // Jika ada error, batalkan semua query
            DB::rollBack();

            // Kirim respons error
            return response()->json(['message' => 'Terjadi kesalahan saat menyimpan transaksi.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Anggota $anggota, Transaction $transaction)
    {
        // Keamanan: Pastikan yang mengakses adalah pemilik data dan transaksi ini milik dia
        if (Auth::user()->id !== $anggota->id || $transaction->anggota_id !== $anggota->id) {
            abort(403, 'Akses Ditolak');
        }

        // Eager load detail transaksi beserta info menu terkait
        $transaction->load('details.menu');

        // Kirim data transaksi dan detailnya ke view
        return view('table_detailTransaksi', [
            'anggota' => $anggota,
            'transaction' => $transaction
        ]);
    }

    public function getJson(Anggota $anggota, Transaction $transaction)
    {
        // Pastikan transaksi ini milik anggota yang benar (keamanan)
        if ($transaction->anggota_id !== $anggota->id) {
            abort(403);
        }

        $transaction->load('details.menu');

        return response()->json($transaction);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anggota $anggota, Transaction $transaction)
    {
        // Keamanan: Pastikan transaksi ini milik anggota yang benar.
        if ($transaction->anggota_id !== $anggota->id) {
            abort(403, 'Akses Ditolak');
        }

        // Hapus data transaksi.
        // Detailnya akan ikut terhapus otomatis jika Anda sudah mengatur
        // onDelete('cascade') di migrasi transaction_details.
        $transaction->delete();

        // Redirect kembali ke halaman riwayat dengan pesan sukses.
        return redirect()->route('anggota.history.index', ['anggota' => $anggota->id])
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
