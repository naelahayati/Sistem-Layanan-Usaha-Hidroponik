<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Naz Hidrofarm')</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-color: #b9d8fa;
            --hover-color: #0056b3;
            --transition-speed: 0.3s;
            --sidebar-bg: #72A8D8;
            --sidebar-text: #ffffff;
        }

        /* Sidebar Background */
        .main-sidebar.sidebar-dark-primary {
            background-color: var(--sidebar-bg) !important;
        }

        .main-sidebar .brand-link,
        .main-sidebar .user-panel,
        .main-sidebar .nav-header {
            background-color: var(--sidebar-bg) !important;
            color: var(--sidebar-text) !important;
        }

        /* Sidebar Layout Fix - Mendukung Mode Kecil (Collapse) */
        :root {
            --sidebar-width: 270px;
        }

        .main-sidebar {
            width: var(--sidebar-width) !important;
            overflow-x: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            height: 100vh !important;
            transition: width 0.3s ease-in-out !important;
        }

        /* Saat sidebar benar-benar mengecil (tidak di-hover) */
        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) {
            width: 4.6rem !important;
        }

        /* Logo tengah saat sidebar mengecil */
        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) .brand-link {
            justify-content: center !important;
            padding: 0.8125rem 0 !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) .brand-link .brand-image {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* Penanda Aktif saat Mode Kecil (tidak di-hover) - Kotak Rapi */
        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) .nav-sidebar .nav-item>.nav-link.active {
            width: 2.8rem !important;
            height: 2.8rem !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 8px !important;
        }

        /* Sembunyikan teks dan panah hanya saat tidak di-hover dalam mode collapse */
        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) .nav-link p,
        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) .brand-text,
        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) .user-panel .info {
            display: none !important;
        }

        /* Jarak Icon saat Mode Kecil (tidak di-hover) */
        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) .nav-sidebar .nav-link {
            margin: 10px auto !important;
            justify-content: center !important;
            align-items: center !important;
            display: flex !important;
            width: 2.8rem !important;
            height: 2.8rem !important;
            padding: 0 !important;
            border-radius: 8px !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) .nav-sidebar .nav-icon {
            margin: 0 !important;
            width: auto !important;
        }

        /* KEMBALIKAN TAMPILAN NORMAL SAAT DI-HOVER */
        .sidebar-mini.sidebar-collapse .main-sidebar:hover {
            width: var(--sidebar-width) !important;
        }

        /* ==========================================
           FIX MARGIN SIDEBAR (DESKTOP VS MOBILE)
           ========================================== */

        /* Aturan ini HANYA boleh berjalan di Desktop (Layar >= 992px) */
        @media (min-width: 992px) {
            /* Saat sidebar terbuka normal */
            .content-wrapper,
            .main-header,
            .main-footer {
                margin-left: var(--sidebar-width) !important;
                transition: margin-left 0.3s ease-in-out !important;
            }

            /* Saat sidebar mengecil (collapsed) di desktop */
            .sidebar-collapse .content-wrapper,
            .sidebar-collapse .main-header,
            .sidebar-collapse .main-footer {
                margin-left: 4.6rem !important;
                transition: margin-left 0.3s ease-in-out !important;
            }
        }

        /* Aturan wajib saat layar Mobile / Tablet (Layar <= 991.98px) */
        @media (max-width: 991.98px) {
            .content-wrapper,
            .main-header,
            .main-footer,
            .sidebar-collapse .content-wrapper,
            .sidebar-collapse .main-header,
            .sidebar-collapse .main-footer {
                margin-left: 0 !important; /* Paksa penuh ke kiri tanpa sisa putih */
                transition: margin-left 0.3s ease-in-out !important;
            }
        }

        .main-header {
            position: sticky !important;
            top: 0 !important;
            z-index: 1030 !important;
        }

        .brand-link {
            width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 1001 !important;
            background-color: var(--sidebar-bg) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
            /* Tambahkan Flexbox Centering */
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            padding: 0.8125rem 0.5rem !important;
        }

        .brand-link .brand-image {
            float: none !important;
            margin-left: 0 !important;
            margin-right: 10px !important;
            max-height: 50px;
            width: auto;
        }

        /* User Panel Centering */
        .user-panel {
            position: relative !important;
            background-color: transparent !important;
            margin-top: 0 !important;
            margin-bottom: 10px !important;
            padding-top: 15px !important;
            border-bottom: 15px solid rgba(255, 255, 255, 0.2) !important;
            /* Tambahkan Flexbox Centering */
            padding-bottom: 10px !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
            color: white !important;
        }

        /* Jarak icon profil dengan teks username */
        .user-panel .image {
            margin-right: 10px !important;
        }

        .user-panel .info {
            padding: 0 !important;
            display: block !important;
        }

        .user-panel .info a {
            color: #ffffff !important;
            /* Putih Murni */
            font-weight: 500 !important;
            text-decoration: none !important;
        }

        /* Logout Menu Specific Spacing */
        .nav-logout {
            margin-top: 20px !important;
            /* Jarak lebih jauh dari Daftar User */
            margin-bottom: 30px !important;
            /* Jarak merata bawah */
        }

        /* === FIX SCROLLBAR HORIZONTAL === */
        /* Global: Hilangkan scrollbar horizontal dari semua elemen sidebar */
        .main-sidebar,
        .main-sidebar .sidebar,
        .main-sidebar .sidebar nav,
        .main-sidebar .sidebar .nav,
        .main-sidebar .sidebar .nav-sidebar {
            overflow-x: hidden !important;
        }

        .sidebar {
            overflow-y: auto !important;
            overflow-x: hidden !important;
            display: block !important;
        }

        /* Saat sidebar collapse dan di-hover membesar */
        .sidebar-mini.sidebar-collapse .main-sidebar:hover,
        .sidebar-mini.sidebar-collapse .main-sidebar:hover .sidebar,
        .sidebar-mini.sidebar-collapse .main-sidebar:hover .sidebar nav,
        .sidebar-mini.sidebar-collapse .main-sidebar:hover .sidebar .nav {
            overflow-x: hidden !important;
            max-width: var(--sidebar-width) !important;
        }

        /* Pastikan konten di dalam sidebar tidak melebihi lebar sidebar */
        .sidebar * {
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Sidebar Menu Items - Spesifik untuk sidebar saja */
        .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            background-color: transparent !important;
            margin: 2px 10px !important;
            padding: 8px 12px !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            white-space: nowrap !important;
            overflow: hidden !important;
        }

        /* Pastikan header tetap menggunakan warna gelap agar terlihat di background putih */
        .main-header .nav-link {
            color: #444 !important;
        }

        .main-header .nav-link:hover {
            color: #000 !important;
        }

        /* Dropdown Treeview - Lebar disamakan dengan menu utama agar sejajar */
        .nav-treeview {
            background: rgba(255, 255, 255, 0.08) !important;
            border-radius: 8px !important;
            /* Samakan radius dengan menu utama */
            margin: 4px 10px !important;
            /* Margin kiri-kanan 10px, persis sama dengan menu utama */
            padding: 4px 0 !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden !important;
        }

        .nav-treeview .nav-item {
            margin: 0 !important;
        }

        .nav-treeview .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            padding: 5px 15px 5px 45px !important;
            /* Padding kiri lebih dalam agar teks menjorok tapi kotak tetap ramping */
            font-size: 0.85rem !important;
            /* Ukuran font sedikit lebih kecil agar lebih rapi */
            margin: 0 !important;
            border-radius: 0 !important;
            /* Hilangkan radius dalam agar tidak menumpuk */
        }

        .nav-treeview .nav-link.active {
            background-color: #004a99 !important;
            color: #ffffff !important;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            padding: 6px 15px !important;
            font-size: 0.9rem !important;
            margin: 0 !important;
            border-radius: 6px !important;
        }

        .nav-treeview .nav-link.active {
            background-color: #004a99 !important;
            color: #ffffff !important;
        }

        /* Sidebar Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        /* === GLOBAL PASTEL BUTTON STYLES === */
        /* Tombol Hapus - Pastel Merah */
        .btn-hapus,
        .deleteProductBtn,
        .deleteMagangBtn,
        .deleteKunjunganBtn,
        .deleteAdminBtn,
        #btnDeleteJadwal {
            background-color: #FFCDD2 !important;
            color: #C62828 !important;
            border: 1px solid #EF9A9A !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
        }

        .btn-hapus:hover,
        .deleteProductBtn:hover,
        .deleteMagangBtn:hover,
        .deleteKunjunganBtn:hover,
        .deleteAdminBtn:hover,
        #btnDeleteJadwal:hover {
            background-color: #EF9A9A !important;
            color: #B71C1C !important;
        }

        /* Tombol Edit - Pastel Hijau */
        .btn-edit,
        .editProductBtn,
        .editMagangBtn,
        .editKunjunganBtn,
        .editAdminBtn,
        #btnEditJadwal {
            background-color: #C8E6C9 !important;
            color: #2E7D32 !important;
            border: 1px solid #A5D6A7 !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
        }

        .btn-edit:hover,
        .editProductBtn:hover,
        .editMagangBtn:hover,
        .editKunjunganBtn:hover,
        .editAdminBtn:hover,
        #btnEditJadwal:hover {
            background-color: #A5D6A7 !important;
            color: #1B5E20 !important;
        }

        /* Tombol Tambah - Pastel Biru */
        .btn-tambah,
        .content-wrapper .btn-primary {
            background-color: #BBDEFB !important;
            color: #1565C0 !important;
            border: 1px solid #90CAF9 !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
        }

        .btn-tambah:hover,
        .content-wrapper .btn-primary:hover {
            background-color: #90CAF9 !important;
            color: #0D47A1 !important;
        }
/* Page Transition */
.wrapper {
    opacity: 0;
    transition: opacity 0.4s ease;
}

//* Aturan wajib saat layar Mobile / Tablet (Layar <= 991.98px) */
        @media (max-width: 991.98px) {
            /* 1. Menghilangkan sisa space putih */
            .content-wrapper,
            .main-header,
            .main-footer,
            .sidebar-collapse .content-wrapper,
            .sidebar-collapse .main-header,
            .sidebar-collapse .main-footer {
                margin-left: 0 !important;
                transition: margin-left 0.3s ease-in-out !important;
            }

            /* 2. Mengecilkan Lebar Utama Sidebar & Mengunci Posisinya (Tetap Diam saat Di-scroll) */
            .main-sidebar,
            .sidebar-mini.sidebar-collapse .main-sidebar:hover {
                width: 220px !important; /* Lebar dirampingkan dari 270px menjadi 220px */
                position: fixed !important; /* Membuat sidebar mengambang dan diam di tempat */
                height: 100vh !important; /* Memastikan tinggi penuh satu layar HP */
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                z-index: 1040 !important; /* Berada di atas content wrapper */
            }

            /* Menyesuaikan pergeseran konten utama saat sidebar laci menu terbuka di mobile */
            .sidebar-open .content-wrapper,
            .sidebar-open .main-header {
                transform: translate3d(220px, 0, 0) !important;
            }

            /* 3. Mengecilkan Ukuran Font & Jarak Menu Utama agar Hemat Ruang */
            .nav-sidebar .nav-link {
                padding: 6px 10px !important; /* Padding diperketat */
            }

            .nav-sidebar .nav-link p {
                font-size: 0.8rem !important; /* Ukuran teks menu utama lebih kecil */
            }

            .nav-sidebar .nav-icon {
                font-size: 0.95rem !important; /* Ukuran icon disesuaikan */
            }

            /* 4. Membuat Sub-menu (Dropdown) Jauh Lebih Kecil & Ramping */
            .nav-treeview {
                margin: 2px 4px !important; /* Kotak sub-menu dirampingkan ke tengah */
            }

            .nav-treeview .nav-link {
                font-size: 0.75rem !important; /* Ukuran font sub-menu dibuat ekstra ramping */
                padding: 4px 10px 4px 30px !important; /* Jarak menjorok disesuaikan */
            }

            /* 5. Mengecilkan Font Judul Kategori (MANAJEMEN DATA, dll) */
            .main-sidebar .nav-header {
                font-size: 0.7rem !important;
                padding-top: 10px !important;
                padding-bottom: 3px !important;
            }

            /* 6. Mengecilkan Ukuran Teks Nama User & Logo Admin di Atas */
            .brand-text {
                font-size: 0.9rem !important;
            }

            .user-panel .info a {
                font-size: 0.78rem !important;
            }
        }


</style>

</head>

<body class="hold-transition sidebar-mini sidebar-collapse layout-fixed">


    <div class="wrapper">
        @include('admin.Theme.header')

        @include('admin.Theme.sidebar')

        <div class="content-wrapper">

            @yield('content')

        </div>

        <footer class="main-footer">
            <strong>Copyright &copy; 2026 <a href="#">Fiela Team (Alfi & Naela)</a>.</strong>
            All rights reserved.
        </footer>

        <aside class="control-sidebar control-sidebar-dark"></aside>

    </div>

    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>

    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
    <script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

    <script src="{{ asset('js/admin/adminlte.js') }}"></script>
    <script src="{{ asset('js/admin/pages/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Logika active menu JS dihapus karena tumpang tindih dengan logika backend Blade yang lebih akurat.

            /**
             * LOGIKA: Menjaga Status Sidebar (Besar/Kecil) tetap konsisten setelah pindah halaman
             */
            const body = document.body;
            const pushMenuBtn = document.querySelector('[data-widget="pushmenu"]');

            // 1. Cek status terakhir dari LocalStorage saat halaman dimuat
            const sidebarStatus = localStorage.getItem('sidebar-status');
            if (sidebarStatus === 'collapsed') {
                body.classList.add('sidebar-collapse');
            } else {
                body.classList.remove('sidebar-collapse');
            }

            // 2. Simpan status saat tombol garis tiga diklik
            pushMenuBtn.addEventListener('click', function () {
                // Beri sedikit delay agar AdminLTE selesai memproses class-nya
                setTimeout(() => {
                    if (body.classList.contains('sidebar-collapse')) {
                        localStorage.setItem('sidebar-status', 'collapsed');
                    } else {
                        localStorage.setItem('sidebar-status', 'expanded');
                    }
                }, 100);
            });

            /**
             * LOGIKA: Menjaga posisi scroll sidebar tetap konsisten
             */
            const mainSidebar = document.querySelector('.main-sidebar');
            const sidebarEl = document.querySelector('.sidebar');
            let lastScrollTop = 0;

            // Simpan posisi scroll saat kursor keluar (sidebar mengecil)
            mainSidebar.addEventListener('mouseleave', function () {
                lastScrollTop = sidebarEl.scrollTop;
            });

            // Kembalikan posisi scroll saat kursor masuk (sidebar membesar)
            mainSidebar.addEventListener('mouseenter', function () {
                sidebarEl.scrollTop = lastScrollTop;
            });

            // Sinkronisasi saat ada klik pada pushmenu (garis tiga)
            document.querySelector('[data-widget="pushmenu"]').addEventListener('click', function () {
                setTimeout(() => {
                    sidebarEl.scrollTop = lastScrollTop;
                }, 350); // Tunggu animasi selesai
            });
        });


window.addEventListener('load', function () {
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            const wrapper = document.querySelector('.wrapper');
            wrapper.style.transition = 'opacity 0.35s ease';
            wrapper.style.opacity = '1';
        });
    });
});

    </script>
</body>

</html>
