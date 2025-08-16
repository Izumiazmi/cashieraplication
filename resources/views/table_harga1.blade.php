<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixed Layout Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
</head>

<body class="table-page-layout halaman-menu"> @include('sidebar')
    <div class="main-content">
        <div class="content-wrapper">
            <div class="left-content">
                <div class="page-header">
                    <h1>Manajemen Menu</h1>
                    <button id="addBtn" class="btn" data-store-url="{{ route('anggota.menu.store', ['anggota' => $anggota->id ?? Auth::user()->id]) }}" @if(($anggota->status ?? Auth::user()->status) === 'demo') disabled @endif>+</button>


                </div>
                @if ($errors->any())
                <div class="alert-danger" style="padding: 1rem; border-radius: 5px; margin-bottom: 20px;">
                    <strong>Oops! Terjadi kesalahan:</strong>
                    <ul style="margin-top: 0.5rem; padding-left: 1.5rem; margin-bottom: 0;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif


                <div class="card card-statistik">
                    <h3>Daftar Menu</h3>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Harga</th>
                                    <th>Jenis</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menus as $menu)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $menu->nama }}</td>
                                    <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                    <td>{{ $menu->jenis }}</td>
                                    <td>
                                        @if ($menu->transaction_details_count > 0)
                                        <div class="action-buttons">
                                            <button class="action-btn edit-btn" data-nama="{{ $menu->nama }}" data-harga="{{ $menu->harga }}" data-jenis="{{ $menu->jenis }}" data-update-url="{{ route('anggota.menu.update', ['anggota' => Auth::user()->id, 'menu' => $menu->id]) }}">
                                                Edit
                                            </button>
                                        </div>
                                        @else
                                        <div class="action-buttons">
                                            <button class="action-btn edit-btn" data-nama="{{ $menu->nama }}" data-harga="{{ $menu->harga }}" data-jenis="{{ $menu->jenis }}" data-update-url="{{ route('anggota.menu.update', ['anggota' => Auth::user()->id, 'menu' => $menu->id]) }}">
                                                Edit
                                            </button>
                                            <button type="button" class="action-btn delete-btn" style="background-color: #e74c3c;" data-url="{{ route('anggota.menu.destroy', ['anggota' => $anggota->id, 'menu' => $menu->id]) }}">
                                                Hapus
                                            </button>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align: center;">Belum ada data menu.</td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <h3>Konfirmasi Hapus</h3>
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            <div class="modal-actions">
                <button id="cancelDeleteBtn" class="btn-secondary">Batal</button>
                <button id="confirmDeleteBtn" class="btn-danger" @if(($anggota->status ?? Auth::user()->status) === 'demo') disabled @endif>Ya, Hapus</button>

            </div>
        </div>
    </div>

    {{-- FORM HAPUS TERSEMBUNYI (UNTUK DIGUNAKAN OLEH JAVASCRIPT) --}}
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <div id="formModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <button class="close-btn">&times;</button>
            <h3 id="modalTitle">Tambah Data Baru</h3>

            <form id="dataForm" method="POST" action="{{ route('anggota.menu.store', ['anggota' => Auth::user()->id]) }}">
                @csrf
                <div class="form-group">
                    <label for="namaInput">Nama Menu</label>
                    {{-- Tambahkan atribut 'name' --}}
                    <input type="text" id="namaInput" name="nama" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="hargaInput">Harga</label>
                    <div class="price-input-wrapper">
                        <span class="currency-symbol">Rp</span>
                        {{-- Tambahkan atribut 'name' --}}
                        <input type="text" id="hargaInput" name="harga" inputmode="numeric" placeholder="0" required autocomplete="off">

                    </div>
                </div>
                <div class="form-group">
                    <label for="jenisInput">Jenis</label>
                    {{-- Tambahkan atribut 'name' --}}
                    <select id="jenisInput" name="jenis" required>
                        <option value="Kopi">Kopi</option>
                        <option value="Non-Kopi">Non-Kopi</option>
                        <option value="Makanan">Makanan & Topping</option>
                    </select>
                </div>
                <button type="submit" class="btn" @if(($anggota->status ?? Auth::user()->status) === 'demo') disabled @endif>Simpan</button>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
