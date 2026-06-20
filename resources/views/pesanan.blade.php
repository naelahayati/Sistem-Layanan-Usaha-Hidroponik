@extends('master')

@section('konten')
<link rel="stylesheet" href="/css/stylepesanan.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

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
                            <a href="{{ route('nazfram.profil-saya') }}" style="font-size: 0.85rem; color: var(--blue-soft);">Ubah Alamat dan Jarak</a>
                        </div>

                        <input type="number" id="jarak_km_input" min="0" step="0.1" value="0" oninput="updateCost()" style="display:none;" {{ $user->latitude && $user->longitude ? 'readonly' : '' }}>
                        <button type="button" class="btn-lihat-rute" onclick="openMapModal()">
                            <i class="fas fa-map-marked-alt"></i> Lihat Rute & Estimasi Ongkir di Peta
                        </button>
                    </div>
                 </div>
                </div>

                @if($user->latitude && $user->longitude)
                <div id="modal-peta" style="position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:9999; align-items:center; justify-content:center; padding:16px;">
                    <div style="background:#fff; border-radius:20px; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.3);">

                        <div style="background:linear-gradient(135deg,#2D5A27,#4a8c3f); padding:20px 24px; border-radius:20px 20px 0 0; display:flex; align-items:center; justify-content:space-between;">
                            <div>
                                <div style="color:#fff; font-weight:700; font-size:1.1rem;"><i class="fas fa-route" style="margin-right:8px;"></i>Rute Pengantaran</div>
                                <div style="color:rgba(255,255,255,0.75); font-size:0.8rem; margin-top:2px;">Naz Hidrofarm → Alamat Anda</div>
                            </div>
                            <button type="button" onclick="closeMapModal()" style="background:rgba(255,255,255,0.2); border:none; color:#fff; width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:1.1rem; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; padding:18px 20px 0;">
                            <div style="text-align:center; background:#f0faf0; border-radius:12px; padding:14px 8px; border:1px solid #c8e6c9;">
                                <div style="font-size:1.4rem; color:#2D5A27; font-weight:800;" id="modal-jarak">—</div>
                                <div style="font-size:0.72rem; color:#666; margin-top:3px;"><i class="fas fa-road" style="color:#4a8c3f;"></i> Jarak Jalan</div>
                            </div>
                            <div style="text-align:center; background:#fff8e1; border-radius:12px; padding:14px 8px; border:1px solid #ffe082;">
                                <div style="font-size:1.1rem; color:#e65100; font-weight:800;" id="modal-ongkir">—</div>
                                <div style="font-size:0.72rem; color:#666; margin-top:3px;"><i class="fas fa-motorcycle" style="color:#e65100;"></i> Ongkos Kirim</div>
                            </div>
                        </div>

                        <div style="padding:14px 20px 0;">
                            <div id="map-leaflet" style="height:240px; border-radius:12px; overflow:hidden; border:1px solid #ddd; background:#e8e8e8; position:relative;">
                                <div id="map-loading" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#f5f5f5;z-index:10;border-radius:12px;">
                                    <div style="text-align:center; color:#888;">
                                        <i class="fas fa-spinner fa-spin" style="font-size:1.5rem; margin-bottom:8px;"></i>
                                        <div style="font-size:0.85rem;">Memuat peta...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="padding:12px 20px 0; display:flex; flex-direction:column; gap:6px;">
                            <div style="display:flex; align-items:center; gap:8px; font-size:0.82rem; color:#444;">
                                <span style="width:12px;height:12px;background:#2D5A27;border-radius:50%;flex-shrink:0;"></span>
                                <span><b>Naz Hidrofarm</b> — Kebun kami</span>
                            </div>
                            <div style="display:flex; align-items:center; gap:8px; font-size:0.82rem; color:#444;">
                                <span style="width:12px;height:12px;background:#e53935;border-radius:50%;flex-shrink:0;"></span>
                                <span><b>{{ $user->alamat ?? 'Alamat Anda' }}</b></span>
                            </div>
                        </div>

                        <div style="padding:16px 20px 20px; display:flex; flex-direction:column; gap:10px;">
                            <a id="btn-gmaps" href="#" target="_blank" style="display:flex; align-items:center; justify-content:center; gap:8px; background:#4285F4; color:#fff; padding:13px; border-radius:12px; text-decoration:none; font-weight:600; font-size:0.9rem;">
                                <img src="https://www.gstatic.com/images/branding/product/1x/maps_24dp.png" style="width:20px;height:20px;" onerror="this.style.display='none'">
                                Buka di Google Maps
                            </a>
                            <button type="button" onclick="closeMapModal()" style="background:#f5f5f5; border:1px solid #ddd; color:#444; padding:12px; border-radius:12px; font-size:0.9rem; font-weight:600; cursor:pointer;">
                                Tutup
                            </button>
                        </div>

                    </div>
                </div>
                @endif

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
                    <h3 class="section-title" style="font-size: 1rem; margin-bottom: 15px;"><i class="fas fa-wallet"></i> Metode Pembayaran</h3>
                    <div style="padding: 15px; background: #fff8e1; border-radius: 10px; border-left: 4px solid #ffc107; font-size: 0.9rem; color: #856404;">
                        <i class="fas fa-info-circle mr-1"></i> Pembayaran dapat dilakukan via <strong>Transfer Bank</strong> atau <strong>QRIS</strong> pada halaman selanjutnya.
                    </div>
                    <input type="hidden" name="metode_pembayaran" value="qris">
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
            // Gunakan API rute untuk hasil akurat sejak awal (Jarak Jalan Raya)
            fetch(`https://router.project-osrm.org/route/v1/driving/${userLng},${userLat};${farmLng},${farmLat}?overview=false`)
                .then(r => r.json())
                .then(data => {
                    if (data.routes && data.routes.length > 0) {
                        const distance = data.routes[0].distance / 1000;
                        document.getElementById('jarak_km_input').value = distance.toFixed(1);
                        updateCost();
                    } else {
                        // Fallback jika API rute gagal
                        const distance = haversineDistance(farmLat, farmLng, userLat, userLng);
                        document.getElementById('jarak_km_input').value = distance.toFixed(1);
                        updateCost();
                    }
                })
                .catch(() => {
                    const distance = haversineDistance(farmLat, farmLng, userLat, userLng);
                    document.getElementById('jarak_km_input').value = distance.toFixed(1);
                    updateCost();
                });
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

        // No qris-info toggle needed anymore
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

@if($user->latitude && $user->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let leafletMap = null;
    let mapInitialized = false;

    function openMapModal() {
        const modal = document.getElementById('modal-peta');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        if (!mapInitialized) {
            setTimeout(() => initMap(), 100);
        }
    }

    function closeMapModal() {
        document.getElementById('modal-peta').classList.remove('active');
        document.body.style.overflow = '';
    }

    document.getElementById('modal-peta').addEventListener('click', function(e) {
        if (e.target === this) closeMapModal();
    });

    function initMap() {
        mapInitialized = true;
        const uLat = {{ $user->latitude }};
        const uLng = {{ $user->longitude }};

        leafletMap = L.map('map-leaflet', { zoomControl: true, attributionControl: false });
        L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: '© Google Maps'
        }).addTo(leafletMap);

        const iconKebun = L.divIcon({
            html: '<div style="background:#2D5A27;width:14px;height:14px;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.4);"></div>',
            iconSize: [20,20], iconAnchor: [10,10], className: ''
        });
        const iconUser = L.divIcon({
            html: '<div style="background:#e53935;width:14px;height:14px;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.4);"></div>',
            iconSize: [20,20], iconAnchor: [10,10], className: ''
        });

        L.marker([farmLat, farmLng], { icon: iconKebun }).addTo(leafletMap).bindPopup('<b>Naz Hidrofarm</b>');
        L.marker([uLat, uLng], { icon: iconUser }).addTo(leafletMap).bindPopup('<b>Lokasi Anda</b>');

        document.getElementById('btn-gmaps').href =
            `https://www.google.com/maps/dir/${farmLat},${farmLng}/${uLat},${uLng}`;

        fetch(`https://router.project-osrm.org/route/v1/driving/${farmLng},${farmLat};${uLng},${uLat}?overview=full&geometries=geojson`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('map-loading').style.display = 'none';
                if (data.routes && data.routes.length > 0) {
                    const route = data.routes[0];
                    const jarakKm = route.distance / 1000;
                    const ongkirRute = calculateOngkir(jarakKm);

                    document.getElementById('modal-jarak').textContent = jarakKm.toFixed(1) + ' km';
                    document.getElementById('modal-ongkir').textContent = formatRupiah(ongkirRute);

                    // SINKRONISASI KE HALAMAN UTAMA
                    document.getElementById('jarak_km_input').value = jarakKm.toFixed(1);
                    updateCost();

                    const coords = route.geometry.coordinates.map(c => [c[1], c[0]]);
                    const polyline = L.polyline(coords, { color: '#1a73e8', weight: 4, opacity: 0.85 }).addTo(leafletMap);
                    leafletMap.fitBounds(polyline.getBounds(), { padding: [20, 20] });
                } else {
                    fallbackStraightLine(uLat, uLng);
                }
            })
            .catch(() => {
                document.getElementById('map-loading').style.display = 'none';
                fallbackStraightLine(uLat, uLng);
            });
    }

    function fallbackStraightLine(uLat, uLng) {
        const jarakKm = haversineDistance(farmLat, farmLng, uLat, uLng);
        document.getElementById('modal-jarak').textContent = '~' + jarakKm.toFixed(1) + ' km';
        document.getElementById('modal-ongkir').textContent = formatRupiah(calculateOngkir(jarakKm));

        const polyline = L.polyline([[farmLat, farmLng], [uLat, uLng]], {
            color: '#e53935', weight: 3, dashArray: '8, 8', opacity: 0.7
        }).addTo(leafletMap);
        leafletMap.fitBounds(polyline.getBounds(), { padding: [30, 30] });
    }
</script>
@endif
@endsection
