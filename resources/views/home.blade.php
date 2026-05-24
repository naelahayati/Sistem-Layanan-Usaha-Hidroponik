@extends('master')

@section('konten')
<link rel="stylesheet" href="/css/stylehome.css">

<main class="main-content">
    {{-- Hero Section (REVERTED TO ORIGINAL) --}}
    <section class="hero-section page-header-main">
        <div class="hero-content">
            <h2 class="hero-subtitle">Welcome to the farm</h2>
            <h1 class="hero-title cd-headline rotate-1 letters">
                <span>Naz Hidrofarm is</span>
                <span class="cd-words-wrapper">
                    <b class="is-visible">HealthyLife</b>
                    <b>HappyLife</b>
                    <b>PureNature</b>
                    <b>EcoFriendly</b>
                    <b>GreenLife</b>
                </span>
            </h1>
        </div>
    </section>

    {{-- About Section Asimetris --}}
    <section class="about-asymmetric">
        <div class="about-text-offset">
            <h2 class="offset-title">Naz Hidrofarm</h2>
            <div class="description-text">
                <p>
                    Didirikan pada tahun 2019, <strong>Naz Hidrofarm</strong> membawa kesegaran alam langsung ke meja makan keluarga melalui teknologi pertanian modern. Kami secara konsisten mengembangkan metode hidroponik yang mengedepankan standar kebersihan dan nutrisi optimal.
                </p>
                <p>
                    Sebagai pusat edukasi pertanian, kami menawarkan program <strong>kunjungan</strong> kebun dan <strong>magang</strong> intensif untuk memahami sistem pertanian berkelanjutan bertenaga inovasi masa depan.
                </p>
                <a href="{{ url('Nazfram/profil') }}" class="btn-outline-elegant">
                    Selengkapnya
                </a>
            </div>
        </div>

        {{-- Dynamic Photo Collage (Using ORIGINAL images only) --}}
        <div class="photo-collage">
            <div class="collage-item item-vertical">
                <img src="/image/2.png" alt="Proses Hidroponik">
            </div>
            <div class="collage-item item-horizontal">
                <img src="/image/5.png" alt="Hasil Panen">
            </div>
            <div class="collage-item item-small">
                <img src="/image/4.png" alt="Detail Tanaman">
            </div>
        </div>
    </section>

    {{-- Product Section (Square Gallery) --}}
    <section class="product-section">
        <h2 class="section-title-centered">Produk Naz Hidrofarm</h2>

        <div class="product-gallery">
            <div class="product-square-card">
                <img src="/image/4.png" alt="Melon">
                <div class="product-info-overlay">
                    <div class="product-icon"><i class="fas fa-leaf"></i></div>
                    <h4>Melon Premium</h4>
                    <p>Manis alami, nutrisi terjaga.</p>
                </div>
            </div>
            <div class="product-square-card">
                <img src="/image/5.png" alt="Sayuran">
                <div class="product-info-overlay">
                    <div class="product-icon"><i class="fas fa-tint"></i></div>
                    <h4>Sayuran Segar</h4>
                    <p>Tanpa pestisida, bebas polusi.</p>
                </div>
            </div>
            <div class="product-square-card">
                <img src="/image/2.png" alt="Bibit">
                <div class="product-info-overlay">
                    <div class="product-icon"><i class="fas fa-seedling"></i></div>
                    <h4>Bibit Unggul</h4>
                    <p>Kualitas varietas pilihan.</p>
                </div>
            </div>
        </div>

        <a href="{{ url('Nazfram/produk') }}" class="btn-outline-elegant">
            Selengkapnya
        </a>
    </section>

    {{-- Kunjungan & Magang Section (Asymmetrical Style) --}}
    <section class="about-asymmetric1" style="padding-top: 50px; background-color: var(--cream-warm);">
        <div class="photo-collage" style="grid-template-columns: 1fr;">
             <div class="collage-item" style="height: 400px;">
                <img src="/image/5.png" alt="Kunjungan Edukasi">
            </div>
        </div>

        <div class="about-text-offset">
            <h2 class="offset-title" style="color: var(--sage-dark);">Edukasi & Magang</h2>
            <div class="description-text">
                <p>
                    Kami membuka pintu bagi siapa saja yang ingin belajar. Program <strong>kunjungan edukasi</strong> dirancang untuk sekolah dan instansi, sementara program <strong>magang</strong> membekali generasi muda dengan keterampilan agribisnis praktis.
                </p>
                <div style="display: flex; gap: 20px; margin-top: 10px;">
                    <a href="{{ url('Nazfram/kunjungan') }}" class="btn-outline-elegant">Kunjungan</a>
                    <a href="{{ url('Nazfram/pelatihan') }}" class="btn-outline-elegant">Magang</a>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="/js/jquery.min.js"></script>
<script src="/js/scripthome.js"></script>
@endsection
