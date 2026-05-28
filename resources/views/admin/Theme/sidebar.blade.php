<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary custom-sidebar elevation-3">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('storage/image/logo2.png') }}" alt="Naz Hidrofarm Logo" class="brand-image" style="opacity: 1; box-shadow: none;">
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

                <li class="nav-item">
                    <a href="{{ route('admin.transaksi-offline') }}" class="nav-link {{ request()->routeIs('admin.transaksi-offline') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>Transaksi</p>
                    </a>
                </li>

                <li class="nav-header">MANAJEMEN DATA</li>

                <li class="nav-item has-treeview {{ (request()->routeIs('admin.produk*', 'admin.transaksi*') && !request()->routeIs('admin.transaksi-offline*')) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ (request()->routeIs('admin.produk*', 'admin.transaksi*') && !request()->routeIs('admin.transaksi-offline*')) ? 'active' : '' }}">
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
                            <a href="{{ route('admin.transaksi') }}" class="nav-link {{ (request()->routeIs('admin.transaksi*') && !request()->routeIs('admin.transaksi-offline*')) ? 'active-sub' : '' }}">
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
