<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Naz Hidrofarm')</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-responsive.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/admin/modal-detail-mobile.css') }}">

    @stack('styles')

    <style>
        .main-header.navbar {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            left: 0 !important;
            z-index: 1040 !important;
        }

        .main-sidebar {
            z-index: 1050 !important;
        }

        .content-wrapper {
            margin-top: 57px !important;
        }

        @media (max-width: 768px) {
            .main-footer {
                font-size: 11px;
                padding: 8px 16px;
            }

            .main-footer a {
                font-size: 11px;
            }
        }

        /* Responsive SweetAlert2 for Mobile */
        @media (max-width: 576px) {
            .swal2-popup {
                width: 85% !important;
                font-size: 0.8rem !important;
                padding: 0.75rem !important;
                border-radius: 15px !important;
            }

            .swal2-title {
                font-size: 1.1rem !important;
            }

            .swal2-content,
            .swal2-html-container {
                font-size: 0.85rem !important;
            }

            .swal2-actions button {
                padding: 6px 12px !important;
                font-size: 0.8rem !important;
                margin: 5px !important;
                min-width: 70px !important;
            }

            .swal2-icon {
                transform: scale(0.7) !important;
                margin-top: 5px !important;
                margin-bottom: 5px !important;
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

    <script src="{{ asset('js/admin/theme-default.js') }}"></script>

    @stack('scripts')
</body>

</html>