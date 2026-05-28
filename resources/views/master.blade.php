<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naz Hidrofarm</title>
    <link rel="stylesheet" href="{{ asset('css/stylemaster.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pengguna/master-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pengguna/pengguna-responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pengguna/user-pages-mobile.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/pengguna/calendar-mobile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')

</head>
<body>

     @if(!request()->routeIs('register', 'login'))

    <header class="site-header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo">
                    <img src="{{ asset('image/logo.png') }}" alt="Naz Hidrofarm" class="navbar-brand-image">
                    <span class="navbar-brand-text">
                        Naz
                        <small>Hidrofarm</small>
                    </span>
                </div>

                <ul class="nav-links">
                    <li>
                        <a href="{{ route('nazfram.home') }}" class="{{ request()->routeIs('nazfram.home') ? 'active' : '' }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('nazfram.profil') }}" class="{{ request()->routeIs('nazfram.profil') ? 'active' : '' }}">Profil</a>
                    </li>
                    <li>
                        <a
                            href="{{ route('nazfram.produk') }}"
                            class="{{ (request()->routeIs('nazfram.produk') || request()->routeIs('nazfram.beli-produk') || request()->routeIs('nazfram.keranjang') || request()->routeIs('nazfram.riwayat-pesanan') || request()->routeIs('nazfram.pesanan')) ? 'active' : '' }}"
                        >Produk</a>
                    </li>
                   <li class="nav-item">
                     <a href="{{ route('nazfram.kunjungan') }}"
                     class="nav-link {{ (Request::is('*kunjungan*') || (Request::is('*riwayat-reservasi*') && request('type') != 'magang')) ? 'active-kuning' : '' }}">
                     Kunjungan
                     </a>
                   </li>
                   <li class="nav-item">
                     <a href="{{ route('nazfram.pelatihan') }}"
                     class="nav-link {{ (Request::is('*pelatihan*') || Request::is('*pendaftaran*') || request('type') == 'magang') ? 'active-kuning' : '' }}">
                     Magang
                     </a>
                   </li>
                </ul>

                <div class="nav-action">
                    @guest
                        <a href="{{ route('login') }}" class="btn-akun" style="text-decoration: none; display: inline-block; text-align: center;">Masuk</a>
                    @endguest

                    @auth
                        <div class="user-dropdown">
                            <button class="btn-akun" id="userDropdown">
                                <i class="fas fa-user-circle"></i> {{ Auth::user()->username }}
                            </button>
                            <div class="dropdown-content">
                                @if(Auth::user()->role !== 'admin')
                                    <a href="{{ route('nazfram.profil-saya') }}" class="dropdown-item-link">
                                        <i class="fas fa-id-card"></i> Profil Saya
                                    </a>
                                    <a href="{{ route('nazfram.riwayat-pesanan') }}" class="dropdown-item-link">
                                        <i class="fas fa-history"></i> Riwayat Pesanan
                                    </a>
                                @endif
                                @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item-link">
                                        <i class="fas fa-chart-line"></i> Dashboard
                                    </a>
                                @endif
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="logout-link">
                                        <i class="fas fa-right-from-bracket"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>
    </header>
    @endif

     <main class="{{ request()->routeIs('register', 'login') ? 'auth-layout' : '' }}">
      @yield('konten')
    </main>

   @if(!request()->routeIs('register', 'login'))

    <footer class="footer-modern">
        <div class="footer-container">
            <div class="footer-section info">
                <h3 class="footer-title">Naz Hidrofarm</h3>

                <div class="contact-group">
                    <div class="contact-item">
                        <div class="contact-label">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Alamat</span>
                        </div>
                        <p class="contact-detail">
                            Dusun Krajan 2 RT 015/003 Desa Tanjungsari Timur <br>
                            Kecamatan Cikaum Kabupaten Subang <br>
                            Provinsi Jawa Barat
                        </p>
                    </div>

                    <div class="contact-item">
                        <div class="contact-label">
                            <i class="fas fa-clock"></i>
                            <span>Jam Kerja</span>
                        </div>
                        <p class="contact-detail">Senin - Jum'at | 07:00 - 20:00</p>
                    </div>
                </div>
            </div>

            <div class="footer-section maps">
                <div class="maps-wrapper">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.01893076364!2d107.75533689999999!3d-6.391558599999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69417c02550531%3A0x799f0496ee0596cc!2sNAZ%20HIDROFARM!5e0!3m2!1sid!2sid!4v1776437458972!5m2!1sid!2sid" width="100%" class="maps-iframe" style="border:0; border-radius: 12px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <a href="https://maps.google.com/?cid=8763728446171223756&g_mp=Cidnb29nbGUubWFwcy5wbGFjZXMudjEuUGxhY2VzLlNlYXJjaFRleHQ" target="_blank" rel="noopener noreferrer" class="maps-btn-minimal">Lihat Lokasi</a>
                </div>
            </div>

            <div class="footer-section social">
                <h3 class="footer-title">Ikuti Kami</h3>
                <div class="social-links-minimal">
                    <a href="https://www.facebook.com/profile.php?id=100069384955773" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>

                    <a href="https://www.instagram.com/nazhidrofarm" aria-label="Instagram" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>

                    <a href="https://www.tiktok.com/@nazhidrofarm" aria-label="TikTok" target="_blank" rel="noopener noreferrer"><i class="fab fa-tiktok"></i></a>

                    <a href="https://wa.me/6282240867746" aria-label="WhatsApp" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom-minimal">
            <p>&copy; 2026 <strong>Fiela Team</strong>. All Rights Reserved.</p>
        </div>
    </footer>
     @endif

    <script src="{{ asset('js/scriptmaster.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown Logic (Click to Open, Click Outside to Close)
            const userBtn = document.getElementById('userDropdown');
            const dropdownContent = document.querySelector('.dropdown-content');

            if (userBtn && dropdownContent) {
                userBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownContent.classList.toggle('show');
                });

                document.addEventListener('click', function(e) {
                    if (!userBtn.contains(e.target) && !dropdownContent.contains(e.target)) {
                        dropdownContent.classList.remove('show');
                    }
                });
            }
        });

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                width: '350px',
                background: '#ffffff',
                iconColor: '#2d5a27',
                padding: '1.5rem',
            });
        @endif

        // Penanganan khusus untuk auto-redirect setelah pembayaran
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('payment_success')) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "Pembayaran Anda telah berhasil diproses!",
                showConfirmButton: false,
                timer: 2500,
                width: '350px',
                background: '#ffffff',
                iconColor: '#2d5a27',
                padding: '1.5rem',
            });
            // Bersihkan URL tanpa merefresh halaman
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        @if(session('error'))
            @php
                $isAksesTerbatas = str_contains(session('error'), 'akun admin');
            @endphp
            @if($isAksesTerbatas)
                Swal.fire('Akses Terbatas', "{{ session('error') }}", 'warning');
            @else
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Ditolak!',
                    text: "{{ session('error') }}",
                    showConfirmButton: true,
                    confirmButtonColor: '#2d5a27',
                    width: '350px',
                    background: '#ffffff',
                });
            @endif
        @endif
    </script>

</body>
</html>
