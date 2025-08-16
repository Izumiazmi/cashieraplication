<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan halaman form login
    public function showLoginForm()
    {
        return view('login_anggota'); // Ganti dengan nama file Blade Anda
    }

    // Memproses data login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba login menggunakan guard 'anggota'
        if (Auth::guard('anggota')->attempt($credentials)) {
            $request->session()->regenerate();

            // 1. Ambil data anggota yang baru saja login
            $anggota = Auth::guard('anggota')->user();

            if ($anggota->status !== 'aktif' && $anggota->status !== 'demo') {
                // 3. Jika status bukan 'aktif', paksa logout kembali
                Auth::guard('anggota')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // 4. Kembalikan ke halaman login dengan pesan error spesifik
                return back()->withErrors([
                    'akun_tidak_aktif' => 'Akun Anda tidak aktif. Silahkan hubungi admin.',
                ])->onlyInput('username');
            }

            // 2. Arahkan ke route kasir DENGAN menyertakan ID anggota
            return redirect()->intended(route('anggota.kasir', ['anggota' => $anggota->id]));
        }

        // Jika gagal, kembalikan ke form login dengan pesan error
        return back()->withErrors([
            'username' => 'Username atau password yang diberikan salah.',
        ])->onlyInput('username');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::guard('anggota')->logout();

        // $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('member/login'); // Arahkan ke halaman utama atau login
    }
}
