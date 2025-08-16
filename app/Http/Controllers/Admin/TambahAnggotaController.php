<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TambahAnggotaController extends Controller
{
    public function index($token, Request $request) // Tambahkan Request $request
    {
        // 1. Mulai query builder, jangan langsung get()
        $query = Anggota::query();

        // 2. Terapkan filter PENCARIAN jika ada input 'search'
        $query->when($request->search, function ($q, $search) {
            // Cari di beberapa kolom sekaligus
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nama_owner', 'like', "%{$search}%")
                    ->orWhere('nama_toko', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        });

        // 3. Terapkan filter STATUS jika ada input 'status'
        $query->when($request->status, function ($q, $status) {
            return $q->where('status', $status);
        });

        // 4. Ambil hasilnya setelah semua filter diterapkan, urutkan dari yang terbaru
        $anggotas = $query->latest()->get();

        // 5. Kirim data ke view (tidak ada perubahan di sini)
        return view('table_admin_anggota', [
            'token' => $token,
            'anggotas' => $anggotas
        ]);
    }

    public function create($token)
    {
        return view('tambah_anggota', compact('token'));
    }

    public function store($token, Request $request)
    {
        // dd($request->all()); 
        // 1. Validasi Input
        $request->validate([
            'nama_owner' => 'required|string|max:255',
            'nama_toko' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'username' => 'required|string|max:255|unique:anggotas', // Pastikan username unik di tabel anggotas
            'password' => 'required|string|min:8', // Minimal 8 karakter
            'status' => 'required|in:aktif,nonaktif,demo',
            'role' => 'required|in:standard,pro',
        ]);

        // 2. Simpan ke Database
        Anggota::create([
            'nama_owner' => $request->nama_owner,
            'nama_toko' => $request->nama_toko,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'username' => $request->username,
            'password' => Hash::make($request->password), // <- Password WAJIB di-hash!
            'status' => $request->status,
            'role' => $request->role,
        ]);

        // 3. Redirect kembali ke halaman daftar anggota dengan pesan sukses
        return redirect()->route('admin.anggota.index', ['token' => $token])
            ->with('success', 'Anggota baru berhasil ditambahkan!');
    }
    public function update($token, Request $request, Anggota $anggota)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'nama_owner' => 'required|string|max:255',
            'nama_toko' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'status' => 'required|in:aktif,nonaktif,demo',
            // Username harus unik, KECUALI untuk user ini sendiri
            'username' => ['required', 'string', 'max:255', Rule::unique('anggotas')->ignore($anggota->id)],
            // Password bersifat opsional (nullable), hanya di-update jika diisi
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:standard,pro',
        ]);

        // 2. Cek jika ada password baru yang diisi
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            // Jika password kosong, hapus dari array agar tidak menimpa password lama
            unset($validatedData['password']);
        }
        // 3. Update data di database
        $anggota->update($validatedData);

        // 4. KEMBALIKAN RESPON JSON UNTUK DITANGKAP OLEH JAVASCRIPT FETCH
        return redirect()->route('admin.anggota.show', ['token' => $token, 'anggota' => $anggota->id])
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function show($token, Anggota $anggota)
    {

        return view('profile_anggota_admin', [
            'token'   => $token,
            'anggota' => $anggota // Kirim data anggota yang spesifik ini ke view
        ]);
    }

    public function edit($token, Anggota $anggota)
    {
        return view('admin.anggota.edit', [
            'token' => $token,
            'anggota' => $anggota
        ]);
    }

    public function destroy($token, Anggota $anggota)
    {
        // Hapus semua data terkait
        foreach ($anggota->transactions as $transaction) {
            $transaction->details()->delete(); // hapus detail transaksi dulu
            $transaction->delete(); // lalu hapus transaksi
        }

        $anggota->menus()->delete(); // hapus menu terkait

        // Terakhir, hapus anggota
        $anggota->delete();

        return redirect()->route('admin.anggota.index', ['token' => $token])
            ->with('success', 'Anggota dan semua datanya berhasil dihapus.');
    }
}
