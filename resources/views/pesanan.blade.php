@extends('master')

@section('konten')
<link rel="stylesheet" href="/css/stylepesanan.css">

<div class="pesanan-page">
    <header class="product-header page-header-sub">
        <h1>CHECKOUT</h1>
    </header>

    <div class="container-pesanan">
        <div class="action-bar">
            <a href="{{ route('nazfram.keranjang') }}" class="btn-back">
                <i class="fas fa-chevron-left"></i> Kembali ke Keranjang
            </a>
        </div>
        <form id="form-pesanan" action="{{ route('nazfram.pesanan.proses') }}" method="POST">
            @csrf
            <!-- Hidden Inputs untuk Kalkulasi JS -->
            <input type="hidden" name="total_produk" id="in-total-produk" value="{{ $totalProduk }}">
            <input type="hidden" name="ongkir" id="in-ongkir" value="0">
            <input type="hidden" name="grand_total" id="in-grand-total" value="{{ $totalProduk }}">
            <input type="hidden" name="alamat" id="in-alamat" value="{{ $user->alamat ?? '' }}">
            <input type="hidden" name="jarak" id="in-jarak" value="0">
            @if(isset($selectedItems))
                @foreach($selectedItems as $itemId)
                    <input type="hidden" name="selected_items[]" value="{{ $itemId }}">
                @endforeach
            @endif
            
            <div class="checkout-main-frame">
                <!-- Opsi Pengiriman -->
                <div class="pesanan-section">
                    <h3 class="section-title"><i class="fas fa-truck"></i> Metode Pengantaran/Pengambilan</h3>
                    
                    <label class="radio-card" id="card-pengambilan">
                        <input type="radio" name="metode_pengiriman" value="pengambilan" checked onchange="updateCost()">
                        <div>
                            <strong>Pengambilan (Pickup)</strong>
                            <div style="font-size: 0.85rem; color: #666;">Ambil langsung di kebun Naz Hidrofarm.</div>
                        </div>
                    </label>

                    <label class="radio-card" id="card-pengantaran">
                        <input type="radio" name="metode_pengiriman" value="pengantaran" onchange="updateCost()">
                        <div>
                            <strong>Pengantaran (Delivery)</strong>
                            <div style="font-size: 0.85rem; color: #666;">Kirim ke alamat Anda (Min. 5kg Melon / Rp150rb Sayuran).</div>
                        </div>
                    </label>
                    <span id="warning-min-belanja" class="text-danger" style="display:none;">
                        <i class="fas fa-exclamation-circle"></i> Pesanan Anda belum memenuhi syarat minimal pengantaran (Beli min. 5kg Melon atau total Rp150.000 Sayuran Pakcoy/Selada).
                    </span>

                    <div id="pengantaran-details" style="display:none;">
                        <div class="address-box">
                            <strong>Alamat Tujuan:</strong>
                            <p>{{ $user->alamat ?? 'Alamat belum diatur.' }}</p>
                            <a href="{{ route('nazfram.profil-saya') }}" style="font-size: 0.85rem; color: var(--blue-soft);">Ubah Alamat</a>
                        </div>

                        <div class="form-group-jarak">
                            <label for="jarak_km">Jarak dari Kebun (KM)</label>
                            <input type="number" id="jarak_km_input" min="0" step="0.1" value="0" oninput="updateCost()" {{ $user->latitude && $user->longitude ? 'readonly' : '' }}>
                            <div style="font-size: 0.8rem; color: #888; margin-top:5px;" id="jarak-hint">
                                @if($user->latitude && $user->longitude)
                                    <span style="color: var(--primary-green); font-weight: bold;"><i class="fas fa-check-circle"></i> Jarak terdeteksi otomatis dari lokasi rumah Anda.</span>
                                @else
                                    * 0-10 km: Gratis | 10.1-15 km: Rp 15rb | 15.1-20 km: Rp 22rb | 20.1-25 km: Rp 30rb | > 25 km: +Rp 5rb/5km
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Belanja -->
                <div class="order-items">
                    <h3 class="section-title"><i class="fas fa-shopping-basket"></i> Produk yang dipesan</h3>
                    @foreach($items as $item)
                        <div class="order-item">
                            <div class="item-photo"><img src="{{ $item['gambar'] }}" alt=""></div>
                            <div class="item-details">
                                <div class="item-name">{{ $item['nama'] }}</div>
                                <div class="item-price">Rp {{ number_format($item['harga'], 0, ',', '.') }}</div>
                                <div class="item-qty">x{{ $item['jumlah'] }} kg</div>
                            </div>
                        </div>
                    @endforeach
                    <div style="font-size: 0.85rem; padding: 10px; background: #e8f5e9; border-radius: 8px; margin-top: 10px;">
                        <i class="fas fa-info-circle text-success"></i> Status Syarat Pengantaran:<br>
                        - Melon: <b>{{ $melonQty }} kg</b> (Butuh 5 kg)<br>
                        - Pakcoy & Selada: <b>Rp {{ number_format($vegTotal, 0, ',', '.') }}</b> (Butuh Rp 150.000)
                    </div>
                </div>

                <!-- Rincian Biaya -->
                <div class="cost-summary">
                    <h3 class="section-title">Ringkasan Pembayaran</h3>
                    <div class="summary-row">
                        <span>Total Harga Produk</span>
                        <span id="txt-total-produk" data-val="{{ $totalProduk }}">Rp {{ number_format($totalProduk, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row" id="row-ongkir">
                        <span>Total Ongkos Kirim</span>
                        <span id="txt-ongkir">Rp 0</span>
                    </div>
                    <div class="summary-row grand-total">
                        <span>TOTAL PEMBAYARAN</span>
                        <span id="txt-grand-total">Rp {{ number_format($totalProduk, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="pesanan-section">
                    <h3 class="section-title"><i class="fas fa-wallet"></i> Metode Pembayaran</h3>
                    
                    <label class="radio-card active" id="card-qris">
                        <input type="radio" name="metode_pembayaran" value="qris" onchange="updateUI()" checked>
                        <div>
                            <strong>QRIS</strong>
                            <div style="font-size: 0.85rem; color: #666;">Bayar instan via e-Wallet/M-Banking.</div>
                        </div>
                    </label>

                    <div id="qris-info" style="display:none; margin-top: 15px; padding: 15px; background: #fff8e1; border-radius: 10px; border-left: 4px solid #ffc107;">
                        <i class="fas fa-info-circle"></i> Anda akan diarahkan ke halaman pembayaran QRIS setelah menekan tombol pesan.
                    </div>
                </div>

                <button type="button" class="btn-submit-order" onclick="submitOrder()">PESAN SEKARANG</button>
            </div> <!-- End of checkout-main-frame -->
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const canDeliver = {{ $canDeliver ? 'true' : 'false' }};
    const totalProduk = parseInt(document.getElementById('txt-total-produk').getAttribute('data-val'));
    let ongkir = 0;
    let grandTotal = totalProduk;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }

    function calculateOngkir(jarak) {
        if (jarak <= 10) return 0;
        if (jarak <= 15) return 15000;
        if (jarak <= 20) return 22000;
        if (jarak <= 25) return 30000;
        // Di atas 25 km: 30000 + 5000 tiap 5 km berikutnya
        const extraJarak = Math.ceil((jarak - 25) / 5);
        return 30000 + (extraJarak * 5000);
    }

    // --- AUTOMATIC DISTANCE CALCULATION ---
    const farmLat = -6.391446647423662;
    const farmLng = 107.75575532459308;
    const userLat = {{ $user->latitude ?? 'null' }};
    const userLng = {{ $user->longitude ?? 'null' }};

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius bumi dalam km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function autoCalculateDistance() {
        if (userLat && userLng) {
            const distance = haversineDistance(farmLat, farmLng, userLat, userLng);
            document.getElementById('jarak_km_input').value = distance.toFixed(1);
            updateCost();
        }
    }

    function updateCost() {
        const isPengantaran = document.querySelector('input[name="metode_pengiriman"]:checked').value === 'pengantaran';
        
        if (isPengantaran) {
            const jarak = parseFloat(document.getElementById('jarak_km_input').value) || 0;
            ongkir = calculateOngkir(jarak);
            document.getElementById('row-ongkir').style.display = 'flex';
            document.getElementById('in-jarak').value = jarak;
        } else {
            ongkir = 0;
            document.getElementById('row-ongkir').style.display = 'flex'; // Tetap tampilkan ongkir walau 0
            document.getElementById('in-jarak').value = 0;
        }

        document.getElementById('txt-ongkir').innerText = formatRupiah(ongkir);
        document.getElementById('in-ongkir').value = ongkir;
        
        grandTotal = totalProduk + ongkir;
        document.getElementById('txt-grand-total').innerText = formatRupiah(grandTotal);
        document.getElementById('in-grand-total').value = grandTotal;

        updateUI(); // run updateUI to check tunai availability
    }

    function updateUI() {
        // Cek Minimal Belanja untuk Pengantaran
        const cardPengantaran = document.getElementById('card-pengantaran');
        const pengantaranRadio = document.querySelector('input[name="metode_pengiriman"][value="pengantaran"]');
        const warningMin = document.getElementById('warning-min-belanja');
        
        if (!canDeliver) {
            cardPengantaran.classList.add('disabled');
            pengantaranRadio.disabled = true;
            warningMin.style.display = 'block';
            
            if (pengantaranRadio.checked) {
                document.querySelector('input[name="metode_pengiriman"][value="pengambilan"]').checked = true;
            }
        } else {
            cardPengantaran.classList.remove('disabled');
            pengantaranRadio.disabled = false;
            warningMin.style.display = 'none';
        }

        const isPengantaran = document.querySelector('input[name="metode_pengiriman"]:checked').value === 'pengantaran';
        document.getElementById('pengantaran-details').style.display = isPengantaran ? 'block' : 'none';

        const isQris = document.querySelector('input[name="metode_pembayaran"]:checked').value === 'qris';
        document.getElementById('qris-info').style.display = isQris ? 'block' : 'none';
    }

    function submitOrder() {
    Swal.fire({
        title: 'Konfirmasi Pesanan',
        text: 'Apakah Anda yakin ingin mengirim pesanan ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2D5A27',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Pesan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = document.querySelector('.btn-submit-order');
            btn.disabled = true;
            btn.innerText = 'Memproses...';
            document.getElementById('form-pesanan').submit();
        }
    });
}

    // Init UI on load
    window.onload = function() {
        autoCalculateDistance();
        updateCost();
    };
</script>
@endsection
