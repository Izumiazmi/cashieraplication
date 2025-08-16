<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AnggotaController extends Controller
{
    public function create($token)
    {
        return view('admin.anggota.create', compact('token'));
    }

    public function store(Request $request, $token)
    {
        $request->validate([
            'nama_owner' => 'required|string|max:255',
            'nama_toko' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'status' => 'required|in:aktif,nonaktif',
            'username' => 'required|string|unique:anggotas,username',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:standard,pro',
        ]);

        Anggota::create([
            'nama_owner' => $request->nama_owner,
            'nama_toko' => $request->nama_toko,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'status' => $request->status,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.anggota.index', $token)->with('success', 'Anggota berhasil ditambahkan');
    }
}
