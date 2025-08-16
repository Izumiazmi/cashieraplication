<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan Bulanan | {{ $anggota->nama_owner }}</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
</head>

<body class="table-page-layout">@include('sidebar')
    <div class="main-content">
        <div class="content-wrapper">
            <div class="left-content">
                <div class="page-header">

                </div>

                <div class="card card-statistik card-list">
                    <div class="card-header">
                        <h3>Statistik Penjualan</h3>
                        <form class="search-form" method="GET" action="{{ route('anggota.pembukuan.index', $anggota) }}">

                            {{-- Dropdown untuk Bulan (tidak ada perubahan) --}}
                            <select name="bulan" class="search-input">
                                @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}" {{ request('bulan', date('m')) == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                    </option>
                                    @endfor
                            </select>

                            {{-- Dropdown untuk Tahun (DIUBAH) --}}
                            <select name="tahun" class="search-input">
                                {{-- Menggunakan variabel $availableYears dari controller --}}
                                @foreach ($availableYears as $y)
                                <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn-search">Cari</button>
                        </form>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Total Penjualan</th>
                                    <th>Total Pendapatan</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pembukuan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->bulan_nama }}</td>
                                    <td>{{ $item->tahun }}</td>
                                    <td>{{ number_format($item->total_penjualan) }} Pcs</td>
                                    <td>Rp. {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('anggota.pembukuan.show', ['anggota' => $anggota, 'tahun' => $item->tahun, 'bulan' => $item->bulan_angka]) }}" class="action-btn" style="background-color: #3498db;">Info</a>
                                            @if(Auth::user()->role === 'pro')
                                            <a href="{{ route('anggota.pembukuan.cetak', ['anggota' => $anggota->id, 'tahun' => $item->tahun, 'bulan' => $item->bulan_angka]) }}" class="action-btn" style="background-color: #33d61dff;">
                                                Cetak
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="text-align: center;">Tidak ada data penjualan.</td>
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
                <button id="confirmDeleteBtn" class="btn-danger">Ya, Hapus</button>
            </div>
        </div>
    </div>
    {{-- FORM HAPUS TERSEMBUNYI --}}
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
