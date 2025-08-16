document.addEventListener('DOMContentLoaded', function() {
    const themeToggleButton = document.getElementById('theme-toggle-btn');
    const body = document.body;

    // Keluar jika tombol tidak ada di halaman ini
    if (!themeToggleButton) {
        return;
    }

    const themeIcon = themeToggleButton.querySelector('i');

    // Fungsi untuk mengubah ikon (bulan/matahari)
    const updateThemeIcon = () => {
        if (body.classList.contains('light-theme')) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    };

    // Saat halaman dimuat, cek tema yang tersimpan di localStorage
    if (localStorage.getItem('theme') === 'light') {
        body.classList.add('light-theme');
    }

    // Perbarui ikon saat halaman dimuat
    updateThemeIcon();

    // Tambahkan event listener untuk tombol
    themeToggleButton.addEventListener('click', () => {
        body.classList.toggle('light-theme');

        // Simpan atau hapus preferensi tema di localStorage
        if (body.classList.contains('light-theme')) {
            localStorage.setItem('theme', 'light');
        } else {
            localStorage.removeItem('theme');
        }

        // Perbarui ikon setelah diklik
        updateThemeIcon();
    });
});