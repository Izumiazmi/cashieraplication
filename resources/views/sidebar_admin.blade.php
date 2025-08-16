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
        <a href="{{ route('admin.dashboard', ['token' => config('app.admin_route_token')]) }}">📊 Dashboard</a>
        <li><a href="{{ route('admin.anggota.index', ['token' => config('app.admin_route_token')]) }}">📋 Table Admin</a></li>
        <li><a href="/">⚙️ Pengaturan</a></li>
        <li><a href="{{ route('admin.logout.get') }}">🪜 Keluar</a></li>
    </ul>
</div>