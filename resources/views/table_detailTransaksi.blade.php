<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper(substr($anggota->username, 0, 3)) }}-{{ $transaction->id }} | {{ $anggota->nama_owner }}</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
</head>

<body class="table-page-layout"> @include('sidebar')

    <div class="main-content">
        <div class="content-wrapper">
            <div class="left-content">
                <div class="page-header">
                    <a href="{{ route('anggota.history.index', $anggota) }}" class="btn-back">
                        < Kembali</a>
                </div>
                <div class="card card-statistik card-detail">
                    <div class="card-header">
                        <p>
                            <strong>Kode: </strong>{{ strtoupper(substr($anggota->username, 0, 3)) }}-{{ $transaction->id }}
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <!-- Ini adalah 4 spasi -->
                            <strong>Tanggal:</strong> {{ $transaction->created_at->format('d F Y, H:i') }}
                        </p>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Harga Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop melalui 'details' dari satu 'transaction' --}}
                                @forelse($transaction->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->nama_pesanan }}</td>
                                    <td>{{ $detail->menu->jenis ?? 'N/A' }}</td>
                                    <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                    <td>{{ $detail->jumlah }}</td>
                                    <td>Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td>Tidak ada detail item untuk transaksi ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-footer">
                                    <td colspan="4"></td>
                                    <td style="font-weight: bold; text-align: center;">Total Keseluruhan</td>
                                    <td style="font-weight: bold; text-align: ">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
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
