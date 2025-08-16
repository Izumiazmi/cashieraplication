<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belum Ada Data | {{ $anggota->nama_owner }}</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">

    {{-- CSS TAMBAHAN UNTUK MEMPERCANTIK TAMPILAN --}}
    <style>
        /* Mengatur wrapper agar konten bisa di tengah sempurna */
        .content-wrapper.empty-state {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 100px);
            padding: 2rem;
        }


        .card.empty-state-card {

            max-width: 450px;
            width: 100%;
            padding: 2.5rem;
        }

        .empty-state-card,
        .empty-state-card h3,
        .empty-state-card p {
            text-align: center;
        }

        /* Mengatur ukuran gambar ilustrasi */
        .empty-state-card img {
            max-width: 200px;
            margin-bottom: 1.5rem;
        }

        /* Memberi sedikit gaya pada teks paragraf */
        .empty-state-card p {
            font-style: normal;
            color: #a0aec0;
            line-height: 1.6;
        }

        /* Memberi efek hover pada tombol */
        .empty-state-card .action-btn {
            transition: background-color 0.3s, transform 0.2s;
        }

        .empty-state-card .action-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .empty-state-card p {
            text-align: center;
        }

    </style>

</head>

<body class="dashboard-layout">
    @include('sidebar')
    <div class="main-content">
        <div class="content-wrapper empty-state">
            <div class="card empty-state-card">
                <h3>Belum Ada Penjualan Hari Ini</h3>
                {{-- <p>
                    Sepertinya belum ada transaksi yang tercatat untuk tanggal
                    <strong>{{ $targetTanggal->translatedFormat('l, d F Y') }}</strong>.
                <br>
                Silakan lakukan penjualan melalui halaman kasir untuk melihat laporan di sini.
                </p> --}}
                <br>
                <a href="{{ route('anggota.kasir', $anggota) }}" class="action-btn" style="text-decoration: none; background-color: #2ecc71; padding: 12px 24px; font-weight: bold;">
                    Buka Halaman Kasir
                </a>

            </div>
        </div>
    </div>
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
