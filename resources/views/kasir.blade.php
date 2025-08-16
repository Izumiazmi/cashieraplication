<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Halaman Kasir | {{ $anggota->nama_owner }}</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="pos-layout">
    @include('sidebar')

    <div class="main-content">
        <div class="content-wrapper">
            <div class="left-content">
                @if($menusPerJenis->isNotEmpty())

                @php
                $colorIndex = 0;
                @endphp


                {{-- CARD UNTUK MENU KATEGORI "KOPI" --}}
                @if(isset($menusPerJenis['Kopi']) && $menusPerJenis['Kopi']->isNotEmpty())
                <div class="card">
                    <h3>Menu Utama - Kopi</h3>
                    <div class="menu-grid">
                        @foreach($menusPerJenis['Kopi'] as $menu)
                        {{-- 2. Gunakan $colorIndex untuk memilih warna --}}
                        <button class="btn btn-menu-item" style="background-color: {{ $colors[$colorIndex % count($colors)] }};" data-id="{{ $menu->id }}" data-nama="{{ $menu->nama }}" data-harga="{{ $menu->harga }}">
                            <div class="menu-name">{{ $menu->nama }}</div>
                            <div class="menu-price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
                        </button>
                        {{-- 3. Naikkan penghitung setiap selesai satu item --}}
                        @php $colorIndex++; @endphp
                        @endforeach
                    </div>
                </div>
                @endif


                {{-- CARD UNTUK MENU KATEGORI "NON-KOPI" --}}
                @if(isset($menusPerJenis['Non-Kopi']) && $menusPerJenis['Non-Kopi']->isNotEmpty())
                <div class="card">
                    <h3>Menu Utama - Non-Kopi</h3>
                    <div class="menu-grid">
                        @foreach($menusPerJenis['Non-Kopi'] as $menu)
                        {{-- Penghitung $colorIndex akan melanjutkan nilainya dari loop sebelumnya --}}
                        <button class="btn btn-menu-item" style="background-color: {{ $colors[$colorIndex % count($colors)] }};" data-id="{{ $menu->id }}" data-nama="{{ $menu->nama }}" data-harga="{{ $menu->harga }}">
                            <div class="menu-name">{{ $menu->nama }}</div>
                            <div class="menu-price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
                        </button>
                        @php $colorIndex++; @endphp
                        @endforeach
                    </div>
                </div>
                @endif


                {{-- CARD UNTUK MENU KATEGORI "MAKANAN" --}}
                @if(isset($menusPerJenis['Makanan']) && $menusPerJenis['Makanan']->isNotEmpty())
                <div class="card">
                    <h3>Menu Tambahan - Makanan & Topping</h3>
                    <div class="menu-grid">
                        @foreach($menusPerJenis['Makanan'] as $menu)
                        <button class="btn btn-menu-item" style="background-color: {{ $colors[$colorIndex % count($colors)] }};" data-id="{{ $menu->id }}" data-nama="{{ $menu->nama }}" data-harga="{{ $menu->harga }}">
                            <div class="menu-name">{{ $menu->nama }}</div>
                            <div class="menu-price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
                        </button>
                        @php $colorIndex++; @endphp
                        @endforeach
                    </div>
                </div>
                @endif
                @else

                {{-- Jika TIDAK ADA menu sama sekali, tampilkan pesan ini --}}
                <div class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <h3>Selamat Datang</h3>
                    <p>Anda belum menambahkan menu apapun. Silakan tambahkan menu terlebih dahulu.</p>
                    {{-- Tombol ini akan mengarah ke halaman manajemen menu --}}
                    <a href="{{ route('anggota.menu.index', ['anggota' => $anggota->id]) }}" class="btn-primary">
                        + Tambah Menu
                    </a>
                </div>
                @endif

            </div>

            <div class="right-content">

                {{-- Wadah ini akan diisi oleh JavaScript --}}
                <div class="widget-list" id="orderList">
                    <div class="empty-cart-placeholder">
                        <div class="empty-cart-icon">🛒</div>
                        <h4>Keranjang Kosong</h4>
                        <p>Menu yang Anda pilih akan muncul di sini.</p>
                    </div>
                </div>

                {{-- Wadah untuk Total dan tombol Simpan --}}
                <div class="total-container">
                    <div class="widget">
                        {{-- Beri ID agar total harga bisa di-update --}}
                        <h4 id="totalPrice">Total : Rp 0</h4>
                        <div class="total-action">
                            <button id="saveOrderBtn" class="btn" data-url="{{ route('anggota.transactions.store', ['anggota' => $anggota->id]) }}">Simpan</button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
