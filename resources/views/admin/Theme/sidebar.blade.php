<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary custom-sidebar elevation-3">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('image/logo2.png') }}" alt="Naz Hidrofarm Logo" class="brand-image" style="opacity: 1; box-shadow: none;">
        <span class="brand-text font-weight-bold">Naz Hidrofarm</span>
    </a>

    <!-- Sidebar user panel (optional) - Fixed below logo -->
    <div class="user-panel">
        <div class="image">
            <i class="fas fa-user-circle fa-2x text-white"></i>
        </div>
        <div class="info">
            <a href="#" class="d-block text-bold">{{ Auth::user()->username ?? 'Administrator' }}</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-0">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">MANAJEMEN DATA</li>

                <li class="nav-item has-treeview {{ request()->routeIs('admin.produk*', 'admin.transaksi*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.produk*', 'admin.transaksi*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>
                            Produk
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.produk-admin') }}" class="nav-link {{ request()->routeIs('admin.produk-admin') ? 'active-sub' : '' }}">
                                <i class="nav-icon fas fa-box-open"></i>
                                <p>Kelola Produk</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.transaksi') }}" class="nav-link {{ request()->routeIs('admin.transaksi*') ? 'active-sub' : '' }}">
                                <i class="nav-icon fas fa-cash-register"></i>
                                <p>Manajemen Transaksi</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview {{ request()->routeIs('admin.kunjungan*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.kunjungan*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-marked-alt"></i>
                        <p>
                            Kunjungan
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.kunjungan-admin') }}" class="nav-link {{ request()->routeIs('admin.kunjungan-admin') ? 'active-sub' : '' }}">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Kelola Kunjungan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.kunjungan-manajemen') }}" class="nav-link {{ request()->routeIs('admin.kunjungan-manajemen') ? 'active-sub' : '' }}">
                                <i class="nav-icon fas fa-users-cog"></i>
                                <p>Manajemen Kunjungan</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview {{ request()->routeIs('admin.magang*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.magang*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>
                            Magang
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.magang-admin') }}" class="nav-link {{ request()->routeIs('admin.magang-admin') ? 'active-sub' : '' }}">
                                <i class="nav-icon fas fa-book-reader"></i>
                                <p>Kelola Magang</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.magang-manajemen') }}" class="nav-link {{ request()->routeIs('admin.magang-manajemen') ? 'active-sub' : '' }}">
                                <i class="nav-icon fas fa-chalkboard-teacher"></i>
                                <p>Manajemen Magang</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.jadwal-admin') }}" class="nav-link {{ request()->routeIs('admin.jadwal-admin') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            Kelola Jadwal
                        </p>
                    </a>
                </li>

                <li class="nav-header">REKAPITULASI LAPORAN</li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.laporan') }}" class="nav-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Laporan</p>
                    </a>
                </li>

                <li class="nav-header">PENGATURAN SISTEM</li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Pengaturan</p>
                    </a>
                </li>

                <li class="nav-header">MANAJEMEN USER</li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.kelola-admin') }}" class="nav-link {{ request()->routeIs('admin.kelola-admin') || request()->routeIs('admin.admin.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Kelola Admin</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.daftar-user') }}" class="nav-link {{ request()->routeIs('admin.daftar-user') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Daftar User</p>
                    </a>
                </li>

                <li class="nav-item mt-4 mb-5">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="#" class="nav-link btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Keluar (Logout)</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<style>
/* CUSTOM MODERN SIDEBAR NAZ HIDROFARM */

/* Sidebar Background - Solid Blue Menyamakan Top Navbar */
.main-sidebar.custom-sidebar {
    background-color: #72A8D8 !important; /* Warna biru sesuai pesanan */
    border-right: none !important;
}

.main-sidebar.custom-sidebar, 
.main-sidebar.custom-sidebar .brand-link {
    background-color: #72A8D8 !important; 
    border-right: none !important;
}

/* Teks putih bersih */
.custom-sidebar .nav-link,
.custom-sidebar .brand-text,
.custom-sidebar .user-panel a {
    color: #ffffff !important;
}

/* Hilangkan border bawah bawaan AdminLTE */
.custom-sidebar .brand-link, .custom-sidebar .user-panel {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
}

/* Nav Item (Bentuk Pill Modern) */
.custom-sidebar .nav-pills .nav-link {
    border-radius: 8px !important;
    margin: 1px 8px !important; /* Jarak lebih rapat ke bawah, beri sela ke samping */
    transition: all 0.2s ease-in-out;
    padding: 8px 12px;
}

/* Saat menu Utama SEDANG AKTIF (Pill warna gelap) */
.custom-sidebar .nav-pills .nav-link.active {
    background-color: #0b5ed7 !important; /* Deep Royal Blue */
    color: #ffffff !important;
    box-shadow: inset 0 2px 5px rgba(0,0,0,0.2) !important;
}

/* Saat anak menu SEDANG AKTIF (Warna lebih soft) */
.custom-sidebar .nav-pills .nav-link.active-sub {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
    font-weight: bold;
}

/* Efek Sorotan saat Di-hover */
.custom-sidebar .nav-pills .nav-link:not(.active):hover {
    background-color: rgba(255,255,255,0.15) !important;
}

/* Memperkecil dan mengedepankan jarak Ikon dengan Teks */
.custom-sidebar .nav-icon {
    font-size: 1.15rem;
    width: 30px;
    text-align: center;
}

/* Header Text (Kategori Navigasi) */
.custom-sidebar .nav-header {
    color: #ffffff !important;
    background-color: transparent !important;
    font-size: 0.8rem;
    font-weight: bold;
    letter-spacing: 0.5px;
    padding-top: 15px;
    padding-bottom: 5px;
}

/* Anak Menu (Sub-menu) Layout */
.custom-sidebar .nav-treeview {
    padding-top: 2px;
}
.custom-sidebar .nav-treeview > .nav-item > .nav-link {
    font-size: 0.9rem;
    padding: 8px 12px;
    margin-left: 12px !important; /* Memberikan efek berjenjang */
}

/* Tombol Logout Merah */
.custom-sidebar .btn-logout {
    background-color: rgba(220, 53, 69, 0.8) !important;
    border-radius: 10px !important;
    margin-top: 15px !important;
}
.custom-sidebar .btn-logout:hover {
    background-color: #c82333 !important;
}

/* MENGATASI TRANSISI SIDEBAR MENGECIL & MEMBESAR */
body.sidebar-collapse .custom-sidebar:not(:hover) .nav-pills .nav-link {
    margin: 10px auto !important;
    padding: 0 !important;
    width: 2.8rem !important;
    height: 2.8rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
body.sidebar-collapse .custom-sidebar:not(:hover) .nav-treeview > .nav-item > .nav-link {
    margin: 10px auto !important;
    padding: 0 !important;
    justify-content: center !important;
}
body.sidebar-collapse .custom-sidebar:not(:hover) .nav-icon {
    margin: 0 !important;
    width: auto !important;
    font-size: 1.2rem !important;
}
body.sidebar-collapse .custom-sidebar:not(:hover) .nav-header {
    display: none !important; 
}
</style>
