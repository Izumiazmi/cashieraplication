<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Transaksi | {{ $anggota->nama_owner }}</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
</head>

<body class="table-page-layout"> @include('sidebar')

    <div class="main-content">
        <div class="content-wrapper">
            <div class="left-content">
                <div class="page-header">

                </div>

                <div class="card card-statistik card-list">
                    <div class="card-header">
                        <h3>History Transaksi</h3>
                        <form class="search-form" method="GET" action="{{ route('anggota.history.index', ['anggota' => $anggota->id]) }}">
                            {{-- Input untuk code transaksi --}}
                            <input type="text" name="keyword" class="search-input" placeholder="Cari..." value="{{ request('keyword') }}" autocomplete="off">
                            {{-- Input khusus untuk mencari berdasarkan Tanggal --}}
                            <input type="date" name="tanggal" class="search-input" value="{{ request('tanggal') }}">
                            <button type="submit" class="btn-search">Cari</button>
                        </form>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Code Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah Item</th>
                                    <th>Total Harga</th>

                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    {{-- Gunakan $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() untuk penomoran yang benar di setiap halaman paginasi --}}
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ strtoupper(substr($anggota->username, 0, 3)) }}-{{ $transaction->id }}</td>

                                    <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                    <td>{{ $transaction->details->sum('jumlah') }}</td>
                                    <td>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('anggota.history.show', ['anggota' => $anggota->id, 'transaction' => $transaction->id]) }}" class="action-btn" style="background-color: #3498db;">Info</a>
                                            <button type="button" class="action-btn delete-btn" style="background-color: #e74c3c;" data-url="{{ route('anggota.transactions.destroy', ['anggota' => $anggota->id, 'transaction' => $transaction->id]) }}">
                                                Hapus
                                            </button>
                                            @if(Auth::user()->role === 'pro')
                                            {{-- Ganti btn-info dengan class warna yang Anda inginkan --}}
                                            <button class="action-btn btn-cetak-ulang" style="background-color: #9af10eff;" data-id="{{ $transaction->id }}">
                                                Cetak
                                            </button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="8" style="text-align: center;">Belum ada riwayat transaksi.</td>
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
    {{-- FORM HAPUS TERSEMBUNYI --}}
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil data anggota yang sedang login, kita butuh ini untuk data toko di struk
            const anggotaData = @json($anggota);

            // Tambahkan event listener ke seluruh halaman untuk menangkap klik tombol cetak
            document.addEventListener('click', function(event) {
                // Cek apakah yang diklik adalah tombol "Cetak Ulang"
                if (event.target.matches('.btn-cetak-ulang')) {
                    const button = event.target;
                    const transactionId = button.dataset.id;
                    const originalText = button.textContent;

                    // Non-aktifkan tombol sementara untuk mencegah klik ganda
                    button.disabled = true;
                    button.textContent = 'Memuat...';

                    // URL untuk mengambil data JSON transaksi
                    const fetchUrl = `/member/${anggotaData.id}/history/${transactionId}/json`;

                    fetch(fetchUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Data transaksi tidak ditemukan.');
                            }
                            return response.json();
                        })
                        .then(transactionData => {
                            // Panggil fungsi printReceipt yang sudah ada dengan data yang baru diambil
                            printReceipt(transactionData, anggotaData);
                        })
                        .catch(error => {
                            console.error('Gagal mencetak ulang:', error);
                            Swal.fire('Gagal', error.message, 'error');
                        })
                        .finally(() => {
                            // Kembalikan tombol ke keadaan semula setelah selesai
                            setTimeout(() => {
                                button.disabled = false;
                                button.textContent = originalText;
                            }, 1000); // Beri jeda 1 detik
                        });
                }
            });
        });

    </script>

    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
