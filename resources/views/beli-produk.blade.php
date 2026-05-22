@extends('master')

@section('konten')
<link rel="stylesheet" href="/css/stylebeli-produk.css">
<div class="page-wrapper">
    <!-- Header Tanaman Hidroponik -->
    <header class="product-header">
        <h1>BELI PRODUK</h1>
    </header>

    <!-- Floating Search & Nav Bar dengan Tombol Kembali -->
    <nav class="action-bar">
        <a class="btn-back" href="{{ route('nazfram.produk') }}" title="Kembali">
            <i class="fas fa-chevron-left"></i>Kembali
        </a>
        <div class="search-input">
            <input id="searchProduk" type="text" placeholder="Cari sayur dan buah segar..." autocomplete="off">
            <i class="fas fa-search" style="color: #ccc; font-size: 1.1rem;"></i>
        </div>
        <a class="btn-cart" href="{{ route('nazfram.keranjang') }}" title="Lihat Keranjang">
            <div class="cart-icon-wrapper">
                <i class="fas fa-shopping-basket"></i>
                <span id="cartBadge" class="cart-badge" style="{{ ($cartCount ?? 0) > 0 ? '' : 'display:none;' }}">
                    {{ $cartCount ?? 0 }}
                </span>
            </div>
            <span class="btn-cart-text">KERANJANG</span>
        </a>
    </nav>

    <!-- Product List Area -->
    <main class="product-list-area">
        @php
            $daftarProduk = $produkTerpilih ? [$produkTerpilih] : $produk;
        @endphp

        <div id="searchEmpty" class="search-empty" style="display:none; text-align:center; padding:50px; color:var(--text-soft);">
            Produk tidak ditemukan.
        </div>

        @foreach($daftarProduk as $p)
            <form
                method="POST"
                action="{{ route('nazfram.keranjang.tambah') }}"
                class="produk-row"
                data-name="{{ $p->name }}"
                onsubmit="return handleAddToCart(this)"
            >
                @csrf
                <input type="hidden" name="id" value="{{ $p->id }}">
                <input type="hidden" name="jumlah" class="qty-input-hidden" value="0">

                <!-- Foto Produk (Kiri) -->
                <div class="photo-cell">
                    @if($p->image)
                        <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}">
                    @else
                        <div class="d-flex justify-content-center align-items-center h-100 opacity-25">
                            <i class="fas fa-leaf fa-2x"></i>
                        </div>
                    @endif
                </div>

                <!-- Info Group (Tengah) -->
                <div class="info-group">
                    <div class="item-name">{{ $p->name }}</div>
                    <div class="item-stock">Stok: {{ $p->stock }}</div>
                    <div class="item-price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                </div>

                <!-- Kontrol Kuantitas Interaktif (Start from 0) -->
                <div class="qty-control">
                    <button type="button" class="btn-qty btn-minus" onclick="changeQty(this, -1)" disabled>-</button>
                    <span class="qty-display">0</span> <span style="font-size:0.8rem; color:#666;">kg</span>
                    <button type="button" class="btn-qty btn-plus" onclick="changeQty(this, 1)" {{ $p->stock <= 0 ? 'disabled' : '' }}>+</button>
                </div>

                <!-- Tombol ADD (Disabled if qty 0) -->
                <button class="btn-add" type="submit" disabled>
                    {{ $p->stock <= 0 ? 'Habis' : 'ADD' }}
                </button>
            </form>
        @endforeach
    </main>

    <script>
        // Fungsi untuk mengubah jumlah pesanan (Plus/Minus)
        function changeQty(btn, delta) {
            const form = btn.closest('form');
            const display = form.querySelector('.qty-display');
            const hiddenInput = form.querySelector('.qty-input-hidden');
            const btnMinus = form.querySelector('.btn-minus');
            const btnPlus = form.querySelector('.btn-plus');
            const btnAdd = form.querySelector('.btn-add');
            const stockStr = form.querySelector('.item-stock').innerText.replace('Stok: ', '');
            const stock = parseInt(stockStr);

            let currentQty = parseInt(display.innerText);
            let newQty = currentQty + delta;

            if (newQty >= 0 && newQty <= stock) {
                display.innerText = newQty;
                hiddenInput.value = newQty;
            }

            // Update status tombol
            btnMinus.disabled = (newQty <= 0);
            btnPlus.disabled = (newQty >= stock);
            btnAdd.disabled = (newQty <= 0);
        }

        // Fungsi saat submit (ADD) - Menggunakan Fetch agar tidak reload & reset ke 0
        function handleAddToCart(form) {
            const btnAdd = form.querySelector('.btn-add');
            const display = form.querySelector('.qty-display');
            const hiddenInput = form.querySelector('.qty-input-hidden');
            const btnMinus = form.querySelector('.btn-minus');
            const btnPlus = form.querySelector('.btn-plus');

            // Simpan data
            const formData = new FormData(form);

            btnAdd.disabled = true;
            btnAdd.innerText = 'adding...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update sisa stok di DOM
                if (data.updated_stock !== undefined) {
                    const stockDisplay = form.querySelector('.item-stock');
                    if (stockDisplay) {
                        stockDisplay.innerText = 'Stok: ' + data.updated_stock;
                    }
                }

                // Reset form inputs
                display.innerText = '0';
                hiddenInput.value = '0';
                btnMinus.disabled = true;

                // Disable Plus button if no stock left
                const currentStock = data.updated_stock !== undefined ? data.updated_stock : 0;
                btnPlus.disabled = (currentStock <= 0);

                btnAdd.disabled = true;
                btnAdd.innerText = currentStock <= 0 ? 'Habis' : 'ADD';

                // Update badge
                if (data.cartCount !== undefined) {
                    const badge = document.getElementById('cartBadge');
                    if (badge) {
                        badge.innerText = data.cartCount;
                        badge.style.display = data.cartCount > 0 ? 'flex' : 'none';
                    }
                }

                // Tampilkan SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Produk ditambahkan ke keranjang.',
                    showConfirmButton: false,
                    timer: 2500,
                    width: '350px',
                    background: '#ffffff',
                    iconColor: '#2d5a27',
                    padding: '1.5rem',
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menambah ke keranjang.',
                    showConfirmButton: true,
                    confirmButtonColor: '#2d5a27',
                    width: '350px',
                    background: '#ffffff',
                });
                btnAdd.disabled = false;
                btnAdd.innerText = 'ADD';
            });

            return false; // Mencegah reload halaman
        }

        // Fungsi Pencarian
        (function () {
            const input = document.getElementById('searchProduk');
            const rows = Array.from(document.querySelectorAll('.produk-row'));
            const empty = document.getElementById('searchEmpty');

            if (!input) return;

            input.addEventListener('input', () => {
                const q = input.value.trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach((row) => {
                    const name = (row.getAttribute('data-name') || '').toLowerCase();
                    const match = !q || name.includes(q);
                    row.style.display = match ? 'flex' : 'none';
                    if (match) visibleCount++;
                });

                if (empty) empty.style.display = visibleCount === 0 ? 'block' : 'none';
            });
        })();
    </script>
</div>
@endsection
