@extends('master')

@section('konten')
<link rel="stylesheet" href="{{ asset('css/pengguna/profil_page.css') }}">
<link rel="stylesheet" href="/css/styleprofil.css">
<main class="main-content-profil">
    {{-- Banner --}}
    <header class="header-banner page-header-main">
        <h1 class="header-title-large">PROFIL</h1>
    </header>

    {{-- Sejarah Section --}}
    <section class="history-section">
        <div class="history-header-flex">
            <div class="minimal-logo-container">
                <img src="{{ asset('image/logo2.png') }}" alt="Logo Naz Hidrofarm">
            </div>
            <div class="history-title-container">
                <h2>Sejarah Naz Hidrofarm</h2>
                <p class="intro-p">
                    NAZ Hidrofarm merupakan <strong>Pusat Pelatihan Pertanian dan Perdesaan
                    Swadaya (P4S)</strong> yang bergerak di bidang usahatani budidaya tanaman
                    hortikultura dengan menggunakan sistem hidroponik.
                </p>
            </div>
        </div>

        {{-- Slider Sejarah --}}
        <div class="history-slider-container">
            <div class="history-slider">
                <div class="history-slide active">
                    <p>
                        Usahatani ini berawal dari kegemaran pemilik usaha dalam berkebun dan
                        mencoba hal baru di dunia pertanian yang dapat mempermudah dalam proses
                        berkebun. Sehingga pada bulan <strong>Januari 2019</strong> kami mencoba belajar menanam
                        dengan sistem hidroponik, mulai dari hidroponik sederhana menggunakan barang
                        bekas hingga memiliki modul/instalasi dari bahan pvc yang masih terbatas.
                    </p>
                </div>
                <div class="history-slide">
                    <p>
                        Berbekal kemampuan menanam dengan cara hidroponik dan didukung oleh
                        adanya lahan kosong yang dimiliki, maka pada bulan Maret 2020 kami memutuskan
                        untuk menjadikan hidroponik sebagai usahatani dengan membangun kebun
                        hidroponik dengan nama kebun NAZ Hidrofarm dengan jumlah awal 1000 lubang
                        tanam dengan jenis tanaman selada hijau keriting.
                    </p>
                </div>
                <div class="history-slide">
                    <p>
                        Selada merupakan sayuran yang cukup sulit untuk dibudidayakan di dataran
                        rendah. Dengan kandungan air lebih dari 70%, selada merupakan sayuran yang
                        mudah layu. Dengan kelemahan tersebut, kami berinisiatif untuk membudidayakan
                        selada dengan sistem hidroponik dan dengan pengemasan yang baik.
                    </p>
                </div>
                <div class="history-slide">
                    <p>
                        Target usahatani ini awalnya hanya masyarakat sekitar rumah, namun dengan
                        seiring berjalannya waktu dan semakin meluasnya jejaring pasar yang terbentuk,
                        maka dalam waktu 2 bulan kami memperluas kebun dan menambah lubang tanam
                        menjadi 5000 LT.
                    </p>
                </div>
            </div>
            <div class="slider-nav">
                <button type="button" class="prev-history"><i class="fas fa-chevron-left"></i></button>
                <div class="dots-container"></div>
                <button type="button" class="next-history"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

        {{-- Carousel Layanan --}}
        <div class="aesthetic-carousel-section">
            <h3 class="layanan-title">Layanan dan Produk</h3>
            <div class="carousel-wrapper">
                <button type="button" class="carousel-control prev"><i class="fas fa-chevron-left"></i></button>
                <div class="carousel-horizontal">
                    <div class="carousel-item">
                        <img src="{{ asset('image/home.webp') }}" alt="Sayuran Segar">
                        <div class="caption">
                            <h4>Sayuran Premium</h4>
                            <p>Kualitas nutrisi terbaik dari sistem hidroponik modern.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('image/4.png') }}" alt="Melon Hidroponik">
                        <div class="caption">
                            <h4>Melon Eksklusif</h4>
                            <p>Budidaya buah melon dengan rasa dan kesegaran yang terjaga.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('image/magang.jpeg') }}" alt="Edukasi">
                        <div class="caption">
                            <h4>Pusat Edukasi</h4>
                            <p>Tempat pembelajaran praktis bagi generasi muda.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('image/kunjungan.webp') }}" alt="P4S Nazfram">
                        <div class="caption">
                            <h4>Pemberdayaan Sosial</h4>
                            <p>Berkontribusi dalam kemajuan ekonomi kreatif perdesaan.</p>
                        </div>
                    </div>
                </div>
                <button type="button" class="carousel-control next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    {{-- Visi Misi --}}
    <section class="visi-misi-minimal">
        <div class="content-grid">
            <div class="visi-box">
                <h3>Visi</h3>
                <p>
                    Menjadi pusat pelatihan pertanian swadaya terdepan yang mewujudkan usaha pertanian unggul
                    bersama masyarakat melalui pemberdayaan sosial, ekonomi, dan ekologi yang berkelanjutan.
                </p>
            </div>
            <div class="misi-box">
                <h3>Misi</h3>
                <ol>
                    <li>Meningkatkan kapasitas pengetahuan dan keterampilan petani melalui pelatihan modern.</li>
                    <li>Membangun jaringan strategis untuk memperkuat posisi tawar petani di pasar global.</li>
                    <li>Mengelola usaha tani yang menguntungkan, berkah, dan ramah lingkungan.</li>
                    <li>Menumbuhkan jiwa kewirausahaan pertanian bagi generasi masa depan.</li>
                </ol>
            </div>
        </div>
    </section>

    {{-- Struktur Organisasi --}}
    <section class="struktur-minimal">
        <h3>Struktur Organisasi</h3>
        <div class="struktur-grid">
            <div class="member-card">
                <span class="role">Ketua</span>
                <span class="name">Irda Firdalitha</span>
            </div>
            <div class="member-card">
                <span class="role">Sekretaris</span>
                <span class="name">Tatang Hoerudin</span>
            </div>
            <div class="member-card">
                <span class="role">Bendahara</span>
                <span class="name">Ahmad Dimyati</span>
            </div>
            <div class="member-card">
                <span class="role">Sarpras</span>
                <span class="name">Rendi Maolana</span>
            </div>
            <div class="member-card">
                <span class="role">Pemasaran</span>
                <span class="name">Athoillah</span>
            </div>
            <div class="member-card">
                <span class="role">Pelatihan</span>
                <span class="name">Aprizal Mauladani</span>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // History Slider
    const slides = document.querySelectorAll('.history-slide');
    const prevBtn = document.querySelector('.prev-history');
    const nextBtn = document.querySelector('.next-history');
    const dotsContainer = document.querySelector('.dots-container');
    let currentSlide = 0;
    let slideInterval;

    if (slides.length > 0 && dotsContainer) {
        // Buat dots
        slides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (index === currentSlide) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });
    }

    const dots = document.querySelectorAll('.dot');

    function goToSlide(index) {
        if (slides.length === 0) return;
        slides[currentSlide].classList.remove('active');
        if (dots[currentSlide]) dots[currentSlide].classList.remove('active');
        currentSlide = (index + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) dots[currentSlide].classList.add('active');
    }

    function nextSlide() { goToSlide(currentSlide + 1); }
    function prevSlide() { goToSlide(currentSlide - 1); }

    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);

    function startAutoSlide() {
        if (slideInterval) clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 5000);
    }

    function stopAutoSlide() {
        if (slideInterval) clearInterval(slideInterval);
    }

    startAutoSlide();

    const sliderContainer = document.querySelector('.history-slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);
    }

    // Carousel Horizontal
    const carousel = document.querySelector('.carousel-horizontal');
    const prevCarousel = document.querySelector('.carousel-control.prev');
    const nextCarousel = document.querySelector('.carousel-control.next');

    if (prevCarousel && carousel) {
        prevCarousel.addEventListener('click', () => {
            carousel.scrollBy({ left: -350, behavior: 'smooth' });
        });
    }

    if (nextCarousel && carousel) {
        nextCarousel.addEventListener('click', () => {
            carousel.scrollBy({ left: 350, behavior: 'smooth' });
        });
    }
});
</script>
@endsection


