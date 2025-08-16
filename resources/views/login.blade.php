<!DOCTYPE html>
<html lang="id" class="landing-page">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="{{ asset('css/sementara.css') }}">
</head>

<body class="landing-page">
    <div class="main-content">
        <div class="content-wrapper">
            <div class="left-content">

                <div class="card">
                    <form action="{{ route('admin.login') }}" method="POST" autocomplete="off">
                        @csrf

                        <h3 class="form-title">Login</h3>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" autocomplete="off" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" autocomplete="off" required>
                        </div>

                        <button type="submit" class="btn">Login</button>

                    </form>
                </div>

            </div>

        </div>
    </div>
    <div id="saveConfirmModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3>Pemberitahuan</h3>
            {{-- Beri id pada tag <p> ini --}}
            <p id="modal-message">Pesan error akan muncul di sini.</p>
            <div class="modal-actions">
                <button id="cancelSaveBtn" class="btn-secondary">Oke</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('saveConfirmModal');
            const closeModalBtn = document.getElementById('cancelSaveBtn');
            const modalMessageEl = document.getElementById('modal-message'); // Target elemen pesan

            // 1. Cek apakah ada SALAH SATU error yang ingin kita tampilkan di modal
            @if($errors->has('akun_tidak_aktif') || $errors->has('username'))

            // 2. Ambil pesan error yang sesuai dari Controller
            let errorMessage = '';
            @if($errors->has('akun_tidak_aktif'))
            errorMessage = @json($errors->first('akun_tidak_aktif'));
            @else
            errorMessage = @json($errors->first('username'));
            @endif

            // 3. Masukkan pesan error ke dalam modal dan tampilkan
            if (modalMessageEl && errorMessage) {
                modalMessageEl.textContent = errorMessage;
                modal.style.display = 'flex';
            }
            @endif

            // Logika untuk tombol "Oke" agar bisa menutup modal (tidak berubah)
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }

            // Opsional: izinkan menutup modal dengan klik di area luar (tidak berubah)
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        });

    </script>

    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
