<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fixed Layout Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
</head>

<body class="table-page-layout"> @include('sidebar_admin')

    <div class="main-content">
        <div class="content-wrapper">
            <div class="left-content">
                <div class="page-header">
                    <h1>Manajemen Anggota</h1>
                    <a href="{{ route('admin.anggota.create', ['token' => $token]) }}">
                        <button id="addBtn" class="btn">+</button>
                    </a>
                </div>
                <div class="card card-statistik card-list">
                    <div class="card-header">
                        <h3>Statistik Penjualan</h3>
                        <form class="search-form" method="GET" action="{{ route('admin.anggota.index', ['token' => $token]) }}">
                            <input type="text" name="search" class="search-input" placeholder="Cari..." value="{{ request('search') }}" autocomplete="off">
                            <select name="status" class="search-select">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Active</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Non-Active</option>
                            </select>
                            <button type="submit" class="btn-search">Cari</button>
                        </form>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Owner</th>
                                    <th>Nama Toko</th>
                                    <th>No WhatApps</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($anggotas as $anggota)
                                <tr>
                                    <td class="row-number"></td>
                                    <td>{{ $anggota->nama_owner }}</td>
                                    <td>{{ $anggota->nama_toko }}</td>
                                    <td>{{ $anggota->no_hp }}</td>
                                    <td>{{ $anggota->alamat }}</td>
                                    <td class="status-cell {{ in_array($anggota->status, ['aktif', 'demo']) ? 'status-active' : 'status-non-active' }}">
                                        {{ ucfirst($anggota->status) }}
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.anggota.show', ['token' => $token, 'anggota' => $anggota->id]) }}" class="action-btn" style="background-color: #3498db;">Info</a>

                                            {{-- UBAH TOMBOL HAPUS INI --}}
                                            <button type="button" class="action-btn delete-btn" style="background-color: #e74c3c;" data-url="{{ route('admin.anggota.destroy', ['token' => $token, 'anggota' => $anggota->id]) }}">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                {{-- Bagian ini akan tampil jika variabel $anggotas kosong --}}
                                <tr>
                                    {{-- colspan="8" agar pesan ini memenuhi semua 8 kolom tabel --}}
                                    <td colspan="8" style="text-align: center;">Tidak ada data anggota yang ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal-overlay" style="display: none;"> {{-- Awalnya tersembunyi --}}
        <div class="modal-content">
            <h3>Konfirmasi Hapus</h3>
            <p>Apakah Anda yakin ingin menghapus data anggota ini?</p>
            <div class="modal-actions">
                <button id="cancelDeleteBtn" class="btn-secondary">Batal</button>
                <button id="confirmDeleteBtn" class="btn-danger">Ya, Hapus</button>
            </div>
        </div>
    </div>

    {{-- FORM HAPUS TERSEMBUNYI (UNTUK DIGUNAKAN OLEH JAVASCRIPT) --}}
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
