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
                        <form class="search-form" method="GET" action="{{ route('anggota.laporan-harian.index', $anggota) }}">
                            <input type="date" name="tanggal" class="search-input" value="{{ request('tanggal') }}">
                            <button type="submit" class="btn-search">Cari</button>
                        </form>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Hari</th>
                                    <th>Tanggal</th>
                                    <th>Total Penjualan</th>
                                    <th>Total Pendapatan</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laporanHarian as $laporan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('l') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d F Y') }}</td>
                                    <td>{{ number_format($laporan->grand_total_penjualan) }} Pcs</td>
                                    <td>Rp. {{ number_format($laporan->grand_total_pendapatan, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            {{-- Link diubah untuk mengarah ke route detail harian --}}
                                            <a href="{{ route('anggota.laporan-harian.show', ['anggota' => $anggota, 'tanggal' => $laporan->tanggal]) }}" class="action-btn" style="background-color: #3498db;">
                                                Info
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align: center;">Tidak ada data penjualan untuk ditampilkan.</td>
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
