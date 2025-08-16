<!DOCTYPE html>
<html lang="id" class="landing-page">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Anggota: {{ $anggota->nama_owner }}</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">

</head>

<body class="landing-page">@include('sidebar')
    <div class="main-content" id="profilePage">
        <form id="profileUpdateForm" action="{{ route('anggota.profil.update', ['anggota' => $anggota->id]) }}" method="POST">
            @csrf
            {{-- CARD PERTAMA: INFORMASI PROFIL --}}
            <div class="card" id="profile-card">
                <div class="card-header">
                    <h3>Informasi Profil</h3>
                </div>
                <div class="menu-grid">

                    {{-- Item untuk Nama Owner --}}
                    <div class="btn">
                        <div class="menu-name">Nama Owner</div>
                        <div class="menu-price">
                            {{-- Tampilan Normal --}}
                            <span class="view-mode">{{ $anggota->nama_owner }}</span>
                            {{-- Tampilan saat Edit --}}
                            <input type="text" name="nama_owner" value="{{ $anggota->nama_owner }}" class="edit-mode">
                        </div>
                    </div>

                    {{-- Item untuk Nama Toko --}}
                    <div class="btn">
                        <div class="menu-name">Nama Toko</div>
                        <div class="menu-price">
                            <span class="view-mode">{{ $anggota->nama_toko }}</span>
                            <input type="text" name="nama_toko" value="{{ $anggota->nama_toko }}" class="edit-mode">
                        </div>
                    </div>

                    {{-- Item untuk No. WhatsApp --}}
                    <div class="btn">
                        <div class="menu-name">No. WhatsApp</div>
                        <div class="menu-price">
                            <span class="view-mode">{{ $anggota->no_hp }}</span>
                            <input type="text" name="no_hp" value="{{ $anggota->no_hp }}" class="edit-mode">
                        </div>
                    </div>

                    {{-- Item untuk Status --}}
                    <div class="btn">
                        <div class="menu-name">Status</div>
                        <div class="menu-price">
                            <span class="view-mode">
                                <span class="status-cell {{ in_array($anggota->status, ['aktif', 'demo']) ? 'status-active' : 'status-non-active' }}">
                                    {{ ucfirst($anggota->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="btn">
                        <div class="menu-name">Version</div>
                        <div class="menu-price">
                            <span class="view-mode">
                                <span class="status-cell {{ in_array($anggota->status, ['aktif', 'demo']) ? 'status-active' : 'status-non-active' }}">
                                    {{ $anggota->role == 'standard' ? 'Standard' : 'Pro' }}
                                </span>
                            </span>
                        </div>
                    </div>

                    {{-- Item untuk Alamat --}}
                    <div class="btn" style="grid-column: 1 / -1;">
                        <div class="menu-name">Alamat Toko</div>
                        <div class="menu-price" style="white-space: normal; text-align: left; padding-top: 8px;">
                            <span class="view-mode">{{ $anggota->alamat }}</span>
                            <textarea name="alamat" class="edit-mode" rows="3">{{ $anggota->alamat }}</textarea>
                        </div>
                    </div>

                </div>
            </div>

            {{-- CARD KEDUA: INFORMASI AKUN --}}
            <div class="card" id="account-card">
                <h3>Informasi Akun</h3>
                <div class="menu-grid">

                    {{-- Item untuk Username --}}
                    <div class="btn">
                        <div class="menu-name">Username</div>
                        <div class="menu-price">
                            <span class="view-mode">{{ $anggota->username }}</span>
                            <input type="text" name="username" value="{{ $anggota->username }}" class="edit-mode" readonly>
                        </div>
                    </div>

                    {{-- Item untuk Password --}}
                    <div class="btn">
                        <div class="menu-name">Password</div>
                        <div class="menu-price">
                            <span class="view-mode">••••••••</span>
                            {{-- Saat edit, kita minta password baru (opsional) --}}
                            <input type="password" name="password" placeholder="Min 8 (opsional)" class="edit-mode" readonly>
                        </div>
                    </div>

                    <div class="btn">
                        <div class="menu-name">Tanggal Bergabung</div>
                        <div class="menu-price">
                            {{-- Format tanggal menjadi '07 Agustus 2025' --}}
                            <span class="view-mode">{{ $anggota->created_at->translatedFormat('d F Y') }}</span>
                            {{-- Kolom ini tidak bisa diedit, jadi tidak ada .edit-mode --}}
                        </div>
                    </div>

                    {{-- Item untuk Terakhir Diperbarui --}}
                    <div class="btn">
                        <div class="menu-name">Terakhir Diperbarui</div>
                        <div class="menu-price">
                            {{-- Format tanggal menjadi '... hari yang lalu' --}}
                            <span class="view-mode">{{ $anggota->updated_at->diffForHumans() }}</span>
                            {{-- Kolom ini tidak bisa diedit, jadi tidak ada .edit-mode --}}
                        </div>
                    </div>

                </div>
                <div class="card-actions">
                    <a href="#" class="btn-primary btn-edit">Edit Profil</a>
                    <button type="button" class="btn-secondary btn-cancel">Batal</button>
                    <button type="button" id="saveTriggerBtn" class="btn-primary btn-save">Simpan Perubahan</button>
                </div>
            </div>

        </form>
    </div>
    <div id="saveConfirmModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3>Konfirmasi Perubahan</h3>
            <p>Apakah Anda yakin ingin menyimpan perubahan ini?</p>
            <div class="modal-actions">
                <button id="cancelSaveBtn" class="btn-secondary">Batal</button>
                <button id="confirmSaveBtn" class="btn-primary" @if(($anggota->status ?? Auth::user()->status) === 'demo') disabled @endif>Ya, Simpan</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
