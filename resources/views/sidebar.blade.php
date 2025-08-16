<!-- Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
    ☰
</button>

<!-- Overlay -->
<div class="overlay" id="overlay"></div><!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Theme Toggle -->
    <div class="theme-toggle">
        <span>Mode</span>
        <label class="switch">
            <input type="checkbox" id="themeToggle">
            <span class="slider"></span>
        </label>
    </div>

    <!-- Menu -->
    <ul class="sidebar-menu">
        {{-- Tambahkan parameter ['anggota' => Auth::user()->id] ke semua route --}}
        <li><a href="{{ route('anggota.kasir', ['anggota' => Auth::user()->id]) }}">📠 Kasir</a></li>
        <li><a href="{{ route('anggota.laporan-harian.show', ['anggota' => Auth::id(), 'tanggal' => date('Y-m-d')]) }}">
                📊 Dashboard
            </a></li>
        <li><a href="{{ route('anggota.menu.index', ['anggota' => Auth::user()->id]) }}">📋 Menu</a></li>
        <li><a href="{{ route('anggota.laporan-harian.index', ['anggota' => Auth::user()->id]) }}">📅 Laporan Harian</a></li>
        <li><a href="{{ route('anggota.pembukuan.index', ['anggota' => Auth::user()->id]) }}">📚 Pembukuan</a></li>
        <li><a href="{{ route('anggota.history.index', ['anggota' => Auth::user()->id]) }}">📜 History Transaksi</a></li>
        <li><a href="{{ route('anggota.profil.show', ['anggota' => Auth::id()]) }}">⚙️ Profil</a></li>
        <li><a href="{{ route('anggota.logout', ['anggota' => Auth::user()->id]) }}">🪜 Keluar</a></li>
    </ul>
</div>
