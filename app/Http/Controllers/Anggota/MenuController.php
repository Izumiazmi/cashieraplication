<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Anggota;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Anggota $anggota)
    {
        // dd($anggota);
        if (Auth::user()->id !== $anggota->id) {
            // Jika tidak sama, hentikan proses dan tampilkan error "Akses Ditolak".
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
        $menus = $anggota->menus()
            ->withCount('transactionDetails')
            ->latest()
            ->get();

        return view('table_harga1', [
            'menus' => $menus,
            'anggota' => $anggota,
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
        // 1. Bersihkan input harga dari titik atau karakter lain
        if ($request->has('harga')) {
            $cleanedHarga = preg_replace('/[^0-9]/', '', $request->input('harga'));
            $request->merge(['harga' => $cleanedHarga]);
        }

        // 2. Sekarang, lakukan validasi dengan data yang sudah bersih
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'jenis' => 'required|string|max:255',
        ]);

        $anggota->menus()->create($validatedData);

        return redirect()->route('anggota.menu.index', ['anggota' => $anggota->id])
            ->with('success', 'Menu baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Anggota $anggota, Menu $menu)
    {
        if ($request->has('harga')) {
            $cleanedHarga = preg_replace('/[^0-9]/', '', $request->input('harga'));
            $request->merge(['harga' => $cleanedHarga]);
        }

        if ($menu->anggota_id !== $anggota->id) {
            abort(403, 'Akses ditolak');
        }

        $validatedData = $request->validate([
            'nama'  => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'jenis' => 'required|string|max:255',
        ]);

        $menu->update($validatedData);

        return redirect()->route('anggota.menu.index', ['anggota' => $anggota->id])
            ->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anggota $anggota, Menu $menu)
    {
        // Keamanan: Pastikan menu yang akan dihapus adalah milik anggota yang benar.
        if ($menu->anggota_id !== $anggota->id) {
            abort(403, 'Akses ditolak');
        }

        $menu->delete();

        return redirect()->route('anggota.menu.index', ['anggota' => $anggota->id])
            ->with('success', 'Menu berhasil dihapus.');
    }
}
