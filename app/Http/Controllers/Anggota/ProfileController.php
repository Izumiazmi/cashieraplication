<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        // Ambil data anggota yang sedang login dari session
        $anggota = Auth::guard('anggota')->user();

        // Tampilkan view profil dengan data anggota tersebut
        return view('profile_anggota', compact('anggota'));
    }

    /**
     * Mengupdate data profil dari anggota yang sedang login.
     */
    public function update(Request $request)
    {
        // Ambil data anggota yang sedang login
        $anggota = Auth::guard('anggota')->user();

        /** @var \App\Models\Anggota $anggota */ // <-- TAMBAHKAN BARIS INI untuk menghilangkan garis merah

        // 1. Validasi Input
        $validatedData = $request->validate([
            'nama_owner' => 'required|string|max:255',
            'nama_toko'  => 'required|string|max:255',
            'no_hp'      => 'required|string|max:20',
            'alamat'     => 'required|string',
            'username'   => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('anggotas')->ignore($anggota->id)],
            'password'   => 'nullable|string|min:8',
        ]);

        // 2. Cek jika anggota mengisi password baru
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = \Illuminate\Support\Facades\Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // 3. Update data di database (garis merah akan hilang)
        $anggota->update($validatedData);

        // 4. Redirect kembali ke halaman profil dengan pesan sukses (ROUTE DIPERBAIKI)
        return redirect()->route('anggota.profil.show', ['anggota' => $anggota->id])
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
