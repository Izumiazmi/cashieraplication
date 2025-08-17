<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashapp</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Pengaturan Dasar */
        body {
            font-family: sans-serif;
            margin: 0;
            color: #333;
            line-height: 1.6;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
            border-radius: 8px;
            /* Sudut sedikit melengkung */
        }

        /* Header Baru yang Fullscreen dengan Background Samar */
        header {
            /* 1. Membuat Header Setinggi Layar */
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;

            /* 3. Menambahkan Gambar Latar Belakang */
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
            url("{{ asset('image/kasir.jpg') }}");
            background-size: cover;
            /* Memastikan gambar menutupi seluruh area */
            background-position: center;
            /* Posisi gambar di tengah */

            /* 4. Mengatur Tampilan Teks */
            color: white;
            /* Ubah warna teks menjadi putih agar terlihat */
            text-align: center;
            padding: 20px;
        }


        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        /* Bagian Fitur (Inti dari Tata Letak) */
        .feature {
            display: flex;
            align-items: center;
            /* Membuat konten vertikal di tengah */
            gap: 40px;
            /* Jarak antara gambar dan teks */
            padding: 60px 5%;
            /* Padding atas/bawah 60px, kiri/kanan 5% */
            max-width: 1100px;
            margin: 0 auto;
            /* Pusatkan section di tengah halaman */
        }

        /* Variasi untuk membalik urutan */
        .feature.feature-reverse {
            flex-direction: row-reverse;
            /* Ini yang membalik urutan gambar dan teks */
        }

        /* Ukuran relatif untuk gambar dan konten teks */
        .feature-image,
        .feature-content {
            flex: 1;
            /* Masing-masing mengambil setengah dari ruang yang tersedia */
        }

        .feature-content h2 {
            font-size: 2em;
            margin-top: 0;
        }

        /* Pengaturan untuk layar kecil (Mobile Responsiveness) */
        @media (max-width: 768px) {
            header {
                justify-content: flex-start;
                /* Alihkan konten ke bagian atas container */
                padding-top: 25vh;
                /* Beri jarak dari atas sebesar 25% dari tinggi layar */
            }

            .feature {
                flex-direction: column;
                /* Ubah tata letak menjadi tumpukan vertikal */
                text-align: center;
            }

            /* Di layar kecil, kita tidak perlu membalik urutannya */
            .feature.feature-reverse {
                flex-direction: column;
            }
        }

        /* ================================== */
        /* === STYLE UNTUK BAGIAN CTA BARU === */
        /* ================================== */

        /* Container untuk bagian CTA */
        .cta-section {
            text-align: center;
            padding: 80px 20px;
            /* Tambah padding vertikal */
            background-color: #f8f9fa;
            color: #333;
        }

        .cta-section h2 {
            font-size: 2.2em;
            margin-bottom: 15px;
        }

        .cta-section p {
            font-size: 1.1em;
            max-width: 600px;
            margin: 0 auto 40px auto;
            color: #666;
        }

        /* Container untuk tombol-tombol */
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        /* ========================================== */
        /* === STYLE DASAR UNTUK SEMUA TOMBOL (PENTING) === */
        /* ========================================== */
        .btn {
            display: inline-flex;
            /* Menggunakan flexbox untuk alignment ikon & teks */
            align-items: center;
            /* Menengahkan ikon & teks secara vertikal */
            justify-content: center;
            /* Menengahkan ikon & teks secara horizontal */
            padding: 12px 24px;
            /* Atur jarak internal tombol */
            color: white;
            text-decoration: none;
            border-radius: 8px;
            /* Sudut lebih membulat */
            border: none;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            /* Efek transisi */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Tambah bayangan halus */
        }

        .btn:hover {
            transform: translateY(-2px);
            /* Efek sedikit terangkat saat di-hover */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn i {
            margin-right: 10px;
            /* Jarak antara ikon dan teks */
            font-size: 1.1em;
            /* Sedikit perbesar ikon */
        }

        /* Style untuk tombol WhatsApp */
        .btn-whatsapp {
            background-color: #25D366;
        }

        .btn-whatsapp:hover {
            background-color: #128C7E;
        }

        /* Style untuk tombol Login (sekunder) */
        .btn-secondary {
            background-color: #343a40;
            /* Warna abu-abu gelap */
        }

        .btn-secondary:hover {
            background-color: #23272b;
        }

        /* ================================== */
        /* === STYLE UNTUK KOTAK AKUN DEMO === */
        /* ================================== */

        .demo-credentials {
            max-width: 400px;
            margin: 40px auto;
            /* Memberi jarak dari elemen atas dan bawah */
            padding: 20px;
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .demo-title {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1em;
            color: #6c757d;
            text-align: center;
        }

        .credential-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* Membuat item rata kiri dan kanan */
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .credential-item .label {
            font-weight: bold;
            color: #333;
        }

        .credential-item code {
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 4px;
            font-family: 'Courier New', Courier, monospace;
        }

        .copy-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #6c757d;
            transition: color 0.2s ease;
        }

        .copy-btn:hover {
            color: #333;
        }

        /* ======================================= */
        /* === STYLE UNTUK KOTAK TUTORIAL CETAK === */
        /* ======================================= */

        .tutorial-box {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            text-align: left;
            /* Atur teks di dalam kotak menjadi rata kiri */
        }

        .tutorial-box h4 {
            margin-top: 0;
            font-size: 1.2em;
            color: #333;
        }

        .btn-download {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-download:hover {
            background-color: #0056b3;
        }

        .tutorial-steps {
            padding-left: 20px;
            /* Beri indentasi pada daftar langkah */
            margin-top: 15px;
            text-align: left;
        }

        .tutorial-steps li {
            margin-bottom: 8px;
            /* Jarak antar langkah */
        }



        /* =================== */
        /* === STYLE FOOTER === */
        /* =================== */
        /* Ganti style footer Anda dengan ini */
        footer {
            background-color: #333;
            /* Kembalikan warna latar gelap */
            color: #ccc;
            /* Kembalikan warna teks terang */
            text-align: center;
            padding: 40px 20px;
            margin-top: 80px;
        }

        .footer-content p {
            margin: 0 0 15px 0;
            font-size: 14px;
        }

        /* Style khusus untuk link di dalam copyright */
        .footer-content p a {
            color: #ecf0f1;
            /* Warna putih agar sedikit menonjol */
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-content p a:hover {
            color: #3498db;
            /* Warna biru saat di-hover */
        }


        /* Pastikan link di footer juga berwarna terang */
        .social-links a {
            color: #ccc;
            font-size: 24px;
        }

        .social-links a:hover {
            color: white;
        }

        .social-links {
            list-style: none;
            /* Menghilangkan bulatan/bullet point */
            padding: 0;
            margin: 0;
            text-align: center;
            /* Pastikan container di tengah */
        }

        .social-links li {
            display: inline-block;
            /* KUNCI UTAMA: Membuat item sejajar horizontal */
            margin: 0 15px;
            /* Memberi jarak antar ikon */
        }

    </style>
</head>
<body>

    <header>
        <h1>Website Kasir Modern</h1>
        <p>Solusi terbaik untuk mengelola penjualan di toko Anda.</p>
    </header>

    <main>
        <section class="feature">
            <div class="feature-image">
                <img src="{{ asset('image/kasir.jpg') }}" alt="Tampilan Kasir">
            </div>
            <div class="feature-content">
                <h2>Manajemen Kasir Yang Mudah</h2>
                <p>Desain kasir kami yang simpel memungkinkan Anda melayani pelanggan lebih cepat. Cukup klik menu, atur jumlah pesanan, dan simpan transaksi. Tidak ada lagi antrean panjang, hanya pelayanan yang efisien.
                    <br>Fokus pada pelanggan Anda, biar kami yang urus kerumitannya. Sistem Point-of-Sale (POS) kami dirancang untuk kecepatan dan kemudahan, bahkan untuk karyawan baru sekalipun.</p>

            </div>
        </section>

        <section class="feature feature-reverse">
            <div class="feature-image">
                <img src="{{ asset('image/dashboard.jpg') }}" alt="Tampilan Laporan">
            </div>
            <div class="feature-content">
                <h2>Laporan Penjualan</h2>
                <p>Lihat performa toko Anda secara real-time. Dasbor analitik kami menyajikan data penjualan mingguan, dan bulanan dalam bentuk grafik yang mudah dibaca. Pantau tren, identifikasi produk terlaris, dan buat strategi bisnis yang lebih cerdas berdasarkan data, bukan tebakan.<br>
                    Ubah data penjualan menjadi keuntungan. Analisis visual kami membantu Anda memahami kesehatan bisnis Anda dalam sekejap, mulai dari pendapatan total hingga performa setiap kategori produk.</p>


            </div>
        </section>
        <section class="feature">
            <div class="feature-image">
                <img src="{{ asset('image/menu.jpg') }}" alt="Tampilan Kasir">
            </div>
            <div class="feature-content">
                <h2>Manajemen Menu yang Mudah</h2>
                <p>Kopi baru? Topping musiman? Atur dan perbarui daftar menu Anda kapan saja tanpa kesulitan. Tambah produk baru, ubah harga, dan kelompokkan item dengan mudah untuk menjaga menu Anda tetap segar dan menarik.
                    Berikan pelanggan Anda pilihan. Sistem manajemen menu kami yang fleksibel memudahkan Anda untuk mengelola semua produk, mulai dari kopi, non-kopi, hingga makanan ringan dalam satu tempat.</p>


            </div>
        </section>

        <section class="feature feature-reverse">
            <div class="feature-image">
                <img src="{{ asset('image/transaksi.jpg') }}" alt="Tampilan Laporan">
            </div>
            <div class="feature-content">
                <h2>History Penjualan</h2>
                <p>Tidak ada lagi data yang hilang. Lacak setiap transaksi yang pernah terjadi dengan mudah. Cari berdasarkan kode, lihat detail pesanan, dan cetak ulang struk kapan pun dibutuhkan untuk rekonsiliasi atau kebutuhan pelanggan.<br>
                    Setiap penjualan adalah cerita. Dengan riwayat transaksi yang detail, Anda memiliki catatan digital yang lengkap dan aman, membantu Anda dalam pembukuan dan memberikan layanan pelanggan yang lebih baik.</p>


            </div>
        </section>

        <section class="feature">
            <div class="feature-image">
                <img src="{{ asset('image/pembukuan.jpg') }}" alt="Cetak Struk">
            </div>
            <div class="feature-content">
                <h2>Laporan Bulanan</h2>
                <p>Tutup buku setiap bulan tanpa pusing. Sistem kami secara otomatis merangkum semua aktivitas penjualan Anda ke dalam laporan bulanan yang rapi dan profesional. Lihat total pendapatan, jumlah item terjual, dan cetak laporan PDF untuk arsip Anda.<br>
                    Fokus pada pengembangan bisnis, biarkan kami yang mengurus rekapnya. Dapatkan laporan bulanan yang komprehensif, siap untuk dianalisis atau dicetak, memberikan Anda gambaran besar tentang performa bisnis Anda dari waktu ke waktu. </p>


            </div>
        </section>
    </main>
    <section class="feature feature-reverse">
        <div class="feature-content" style="flex: 1; max-width: 100%;">
            <h2>Cara Penggunaan</h2>
            <p>
                Tingkatkan pengalaman pelanggan dengan struk transaksi yang dapat dicetak langsung. Sistem kami mendukung pencetakan melalui aplikasi pihak ketiga untuk fleksibilitas maksimal.
            </p>

            <div class="tutorial-box">
                <h4>Coba Cetak Struk Sekarang:</h4>
                <p>
                    Untuk mencoba fitur cetak struk, kami merekomendasikan aplikasi <strong>RawBT</strong> di Android.
                    <a href="https://play.google.com/store/apps/details?id=ru.a402d.rawbtprinter" target="_blank" class="btn-download">Unduh RawBT di Play Store</a>
                </p>
                <ol class="tutorial-steps">
                    <li>Instal aplikasi RawBT dari link di atas.</li>
                    <li>Buka RawBT, masuk ke Pengaturan, dan hubungkan printer thermal Anda (atau pilih "Demo Printer" untuk simulasi).</li>
                    <li>Aktifkan fitur <strong>"Web service"</strong> atau <strong>Required permissions</strong> di dalam pengaturan RawBT.</li>
                    <li>Buka aplikasi kasir kami di handphone anda dan lakukan transaksi, struk akan otomatis muncul di RawBT untuk dicetak.</li>

                </ol>
                <li><strong>Saran:</strong> Untuk pengalaman terbaik, gunakan aplikasi ini di perangkat <strong>Tablet atau iPad</strong>.</li>

            </div>
        </div>
    </section>

    <section class="cta-section">
        <h2>Siap Meningkatkan Penjualan Anda?</h2>
        <p>Hubungi kami untuk informasi lebih lanjut atau masuk jika Anda sudah menjadi anggota.</p>

        <div class="demo-credentials">
            <p class="demo-title">Atau coba langsung dengan akun demo kami:</p>
            <div class="credential-item">
                <span class="label">Username:</span>
                <code id="demo-user">demo123</code>
                <button class="copy-btn" data-clipboard-target="#demo-user" aria-label="Salin Username">
                    <i class="far fa-copy"></i>
                </button>
            </div>
            <div class="credential-item">
                <span class="label">Password:</span>
                <code id="demo-pass">12345678</code>
                <button class="copy-btn" data-clipboard-target="#demo-pass" aria-label="Salin Password">
                    <i class="far fa-copy"></i>
                </button>
            </div>
        </div>
        <div class="cta-buttons">
            <a href="https://wa.me/6281374195580" target="_blank" class="btn btn-whatsapp">
                <i class="fab fa-whatsapp"></i> Hubungi via WhatsApp
            </a>
            <a href="{{ route('anggota.login') }}" target="_blank" class="btn btn-secondary">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <p>&copy; 2025 <a href="https://izumiazmi.github.io/portofolio-azmi/">Izumi. </a>Hak Cipta Dilindungi.</p>
            <ul class="social-links">
                <li><a href="https://github.com/Izumiazmi" aria-label="Github"><i class="fab fa-github"></i></a></li>
                <li><a href="https://www.linkedin.com/in/muhammad-habib-al-azmi?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" aria-label="Linkedin"><i class="fab fa-linkedin"></i></a></li>
            </ul>
        </div>
    </footer>
    <script>
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.dataset.clipboardTarget;
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    const textToCopy = targetElement.textContent;

                    // Cek apakah API Clipboard modern tersedia dan aman
                    if (navigator.clipboard && window.isSecureContext) {
                        // --- METODE MODERN (UNTUK HTTPS) ---
                        navigator.clipboard.writeText(textToCopy).then(() => {
                            showCopySuccess(button);
                        }).catch(err => {
                            console.error('Gagal menyalin (modern):', err);
                            alert('Gagal menyalin teks.');
                        });
                    } else {
                        // --- METODE CADANGAN (UNTUK HTTP / BROWSER LAMA) ---
                        const textArea = document.createElement('textarea');
                        textArea.value = textToCopy;

                        // Buat textarea tidak terlihat
                        textArea.style.position = 'absolute';
                        textArea.style.left = '-9999px';

                        document.body.appendChild(textArea);
                        textArea.select(); // Pilih teks di dalamnya
                        try {
                            document.execCommand('copy'); // Perintah salin klasik
                            showCopySuccess(button);
                        } catch (err) {
                            console.error('Gagal menyalin (cadangan):', err);
                            alert('Gagal menyalin teks.');
                        } finally {
                            document.body.removeChild(textArea); // Hapus textarea setelah selesai
                        }
                    }
                }
            });
        });

        // Fungsi bantuan untuk menampilkan feedback visual
        function showCopySuccess(button) {
            const originalIcon = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check" style="color: green;"></i>'; // Pastikan Anda sudah memuat Font Awesome

            setTimeout(() => {
                button.innerHTML = originalIcon;
            }, 1500);
        }

    </script>


</body>
</html>
