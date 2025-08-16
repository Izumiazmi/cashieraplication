<!DOCTYPE html>
<html lang="id" class="landing-page">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Anggota Baru</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
</head>

<body class="landing-page">
    @include('sidebar_admin')
    <div class="main-content" id="profilePage">

        {{-- Menampilkan error validasi jika ada --}}
        @if ($errors->any())
        <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <strong>Oops! Terjadi kesalahan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form id="addAnggotaForm" action="{{ route('admin.anggota.store', ['token' => $token]) }}" method="POST">
            @csrf

            {{-- CARD PERTAMA: INFORMASI PROFIL --}}
            <div class="card" id="profile-card">
                <div class="card-header">
                    <h3>Tambah Anggota Baru</h3>
                    <a href="{{ route('admin.anggota.index', ['token' => $token]) }}" class="btn-back">
                        &lt; Kembali
                    </a>
                </div>
                <div class="menu-grid">
                    {{-- Nama Owner --}}
                    <div class="btn">
                        <div class="menu-name">Nama Owner</div>
                        <div class="menu-price">
                            <input type="text" name="nama_owner" value="{{ old('nama_owner') }}" placeholder="Masukkan nama owner" required>
                        </div>
                    </div>

                    {{-- Nama Toko --}}
                    <div class="btn">
                        <div class="menu-name">Nama Toko</div>
                        <div class="menu-price">
                            <input type="text" name="nama_toko" value="{{ old('nama_toko') }}" placeholder="Masukkan nama toko" required>
                        </div>
                    </div>

                    {{-- No. WhatsApp --}}
                    <div class="btn">
                        <div class="menu-name">No. WhatsApp</div>
                        <div class="menu-price">
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 08123456789" required>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="btn">
                        <div class="menu-name">Status</div>
                        <div class="menu-price">
                            <select name="status" required>
                                <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Active</option>
                                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Non-Active</option>
                                <option value="demo" {{ old('status') == 'demo' ? 'selected' : '' }}>Demo</option>
                            </select>
                        </div>
                    </div>

                    {{-- Version --}}
                    <div class="btn">
                        <div class="menu-name">Version</div>
                        <div class="menu-price">
                            <select name="role" required>
                                <option value="standard" {{ old('role') == 'standard' ? 'selected' : '' }}>Standard</option>
                                <option value="pro" {{ old('role') == 'pro' ? 'selected' : '' }}>Pro</option>
                            </select>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="btn" style="grid-column: 1 / -1;">
                        <div class="menu-name">Alamat Toko</div>
                        <div class="menu-price" style="white-space: normal; text-align: left; padding-top: 8px;">
                            <textarea name="alamat" rows="3" placeholder="Masukkan alamat lengkap toko" required>{{ old('alamat') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD KEDUA: INFORMASI AKUN --}}
            <div class="card" id="account-card">
                <h3>Informasi Akun</h3>
                <div class="menu-grid">
                    {{-- Username --}}
                    <div class="btn">
                        <div class="menu-name">Username</div>
                        <div class="menu-price">
                            <input type="text" name="username" value="{{ old('username') }}" placeholder="Buat username" required>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="btn">
                        <div class="menu-name">Password</div>
                        <div class="menu-price">
                            <input type="password" name="password" placeholder="Buat password (min. 8 karakter)" required>
                        </div>
                    </div>
                </div>
                <div class="card-actions">
                    <button type="submit" class="btn-primary">Simpan Anggota</button>
                    <a href="{{ route('admin.anggota.index', ['token' => $token]) }}" class="btn-secondary">Batal</a>
                </div>
            </div>

        </form>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
