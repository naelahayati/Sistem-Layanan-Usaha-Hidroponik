@extends('master')

@section('konten')
<link rel="stylesheet" href="/css/stylekeranjang.css">

<meta name="csrf-token" content="{{ csrf_token() }}">

<main class="produk-page">
    <!-- Header -->
    <header class="product-header">
        <h1>KERANJANG SAYA</h1>
    </header>

    <div class="cart-content" id="cartContent">
        @if(empty($items))
            <div class="cart-empty">
                <i class="fas fa-shopping-basket"></i>
                <p>Keranjang masih kosong. Silakan pilih produk terlebih dahulu.</p>
                <a href="{{ route('nazfram.beli-produk') }}" class="btn-checkout" style="display:inline-block; margin-top:20px;">Lihat Produk</a>
            </div>
        @else
            <div class="action-bar-keranjang">
                <a class="btn-back" href="{{ route('nazfram.beli-produk') }}" title="Kembali Ke Beli Produk">
                    <i class="fas fa-chevron-left"></i>
                    <span>Kembali ke Beli Produk</span>
                </a>
            </div>

            <div class="cart-notice-modern" style="background: #fff9e6; border-left: 4px solid #ffc107; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                <i class="fas fa-history" style="color: #ffc107; font-size: 1.5rem; margin-right: 15px;"></i>
                <div>
                    <strong style="display: block; color: #856404; font-size: 0.95rem;">Info Masa Simpan Keranjang</strong>
                    <span style="color: #856404; font-size: 0.85rem;">Produk di keranjang akan otomatis dikembalikan ke stok utama jika tidak di-checkout dalam <strong>24 jam</strong>.</span>
                </div>
            </div>

            <div class="cart-frame">
                <div class="cart-frame-header">
                    <label class="custom-checkbox">
                        <input type="checkbox" id="selectAllCheckbox">
                        <span class="checkmark"></span>
                        <span style="margin-left: 10px;">Pilih Semua</span>
                    </label>
                    <button class="btn-delete-selected" id="btnHapusTerpilih" onclick="hapusTerpilih()" disabled>
                        <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                </div>

                <div class="cart-items">
                    @foreach($items as $item)
                        <div class="cart-item" data-id="{{ $item['cart_item_id'] }}">
                            <!-- Checkbox Item -->
                            <label class="custom-checkbox">
                                <input type="checkbox" class="item-checkbox" data-id="{{ $item['cart_item_id'] }}" onchange="hitungTotalTerpilih()">
                                <span class="checkmark"></span>
                            </label>

                            <!-- Foto Produk -->
                            <div class="cart-photo">
                                <img src="{{ $item['gambar'] }}" alt="{{ $item['nama'] }}" onload="this.classList.add('loaded')">
                            </div>

                            <!-- Detail & Controls -->
                            <div class="cart-details">
                                <div class="cart-info-main">
                                    <div class="cart-name">{{ $item['nama'] }}</div>
                                    <div class="cart-price-info">Harga: Rp {{ number_format($item['harga'], 0, ',', '.') }}</div>
                                    <div class="cart-subtotal" style="display:none;">Subtotal: Rp <span class="subtotal-value">{{ number_format($item['subtotal'], 0, ',', '.') }}</span></div>
                                </div>

                                <div class="cart-quantity-wrapper">
                                    <div class="cart-quantity-control">
                                        <button class="btn-qty" onclick="updateQty({{ $item['cart_item_id'] }}, 'decrement')">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="qty-value">{{ $item['jumlah'] }}</span> <span style="font-size:0.8rem; color:#666;">kg</span>
                                        <button class="btn-qty" onclick="updateQty({{ $item['cart_item_id'] }}, 'increment')" {{ $item['stok'] <= 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div style="font-size: 0.75rem; color: #999; text-align: center; margin-top: 8px;">
                                        Stok: <span class="stok-value">{{ $item['stok'] }}</span>
                                    </div>
                                </div>

                                <div class="cart-subtotal-display">
                                    <span class="subtotal-label-inline">Subtotal:</span>
                                    <span class="subtotal-currency">Rp</span>
                                    <span class="subtotal-display-value">{{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Summary Footer (Inside Card) -->
                <div class="cart-summary-internal">
                    <div class="summary-container">
                        <div class="total-info">
                            <span class="total-label">Total Harga (Terpilih)</span>
                            <span class="total-value">Rp <span id="grandTotal">0</span></span>
                        </div>
                        
                        <form action="{{ route('nazfram.pesanan') }}" method="GET" id="formCheckout">
                            <button type="submit" class="btn-checkout" id="btnCheckout" disabled>Pesan Sekarang</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');

        // Pilih Semua
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                hitungTotalTerpilih();
            });
        }
    });

    function hitungTotalTerpilih() {
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const btnHapus = document.getElementById('btnHapusTerpilih');
        const btnCheckout = document.getElementById('btnCheckout');
        let total = 0;
        let checkedCount = 0;

        const formCheckout = document.getElementById('formCheckout');
        formCheckout.querySelectorAll('input[name="selected_items[]"]').forEach(el => el.remove());

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                checkedCount++;
                const itemRow = cb.closest('.cart-item');
                // parse subtotal
                const subtotalText = itemRow.querySelector('.subtotal-display-value').innerText.replace(/\./g, '');
                total += parseInt(subtotalText) || 0;

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_items[]';
                hiddenInput.value = cb.getAttribute('data-id');
                formCheckout.appendChild(hiddenInput);
            }
        });

        // Update Total
        document.getElementById('grandTotal').innerText = new Intl.NumberFormat('id-ID').format(total);

        // Update Select All Checkbox State
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        if (selectAllCheckbox && itemCheckboxes.length > 0) {
            selectAllCheckbox.checked = (checkedCount === itemCheckboxes.length);
        }

        // Enable/Disable buttons
        if(checkedCount > 0) {
            btnHapus.disabled = false;
            btnCheckout.disabled = false;
        } else {
            btnHapus.disabled = true;
            btnCheckout.disabled = true;
        }
    }

    function hapusTerpilih() {
        const selectedIds = [];
        document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
            selectedIds.push(cb.getAttribute('data-id'));
        });

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: 'Hapus Item Terpilih?',
            text: "Item yang dipilih akan dihapus dari keranjang",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#2d5a27',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                let deletePromises = selectedIds.map(id => {
                    return fetch("{{ route('nazfram.keranjang.hapus') }}", {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ cart_item_id: id })
                    }).then(r => r.json());
                });

                Promise.all(deletePromises).then(results => {
                    location.reload();
                }).catch(error => {
                    console.error("Error bulk delete:", error);
                    alert("Gagal menghapus beberapa item.");
                });
            }
        });
    }

    function updateQty(cartItemId, action) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('nazfram.keranjang.update') }}", {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                cart_item_id: cartItemId,
                action: action
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.removed) {
                    location.reload();
                } else {
                    const itemRow = document.querySelector(`.cart-item[data-id="${cartItemId}"]`);
                    itemRow.querySelector('.qty-value').innerText = data.quantity;
                    itemRow.querySelector('.stok-value').innerText = data.stok;
                    itemRow.querySelector('.subtotal-display-value').innerText = new Intl.NumberFormat('id-ID').format(data.subtotal);

                    const btnPlus = itemRow.querySelector('button[onclick*="increment"]');
                    btnPlus.disabled = (data.stok <= 0);

                    // Re-calculate
                    hitungTotalTerpilih();
                }
            } else {
                Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Oops!', 'Gagal memperbarui keranjang', 'error');
        });
    }
</script>
@endsection
