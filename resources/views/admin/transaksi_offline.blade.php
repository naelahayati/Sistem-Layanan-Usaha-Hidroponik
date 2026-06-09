@extends('admin.Theme.defualt')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/transaksi_offline.css') }}?v={{ time() }}">
@endpush

@section('content')

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-cash-register text-primary mr-2"></i>Transaksi Offline</h1>
                <p class="text-muted mt-1">Pencatatan transaksi langsung di tempat untuk produk, kunjungan, dan magang.</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Transaksi Offline</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content page-transaksi-offline">
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-body p-4 p-md-5">

                <ul class="nav nav-pills nav-pills-custom" id="offlineTab" role="tablist">
                    <li class="nav-item flex-fill">
                        <a class="nav-link active" id="produk-tab" data-toggle="pill" href="#produk" role="tab" aria-controls="produk" aria-selected="true">
                            <i class="fas fa-shopping-bag mr-2"></i>Pesanan Produk
                        </a>
                    </li>
                    <li class="nav-item flex-fill">
                        <a class="nav-link" id="kunjungan-tab" data-toggle="pill" href="#kunjungan" role="tab" aria-controls="kunjungan" aria-selected="false">
                            <i class="fas fa-calendar-alt mr-2"></i>Reservasi Kunjungan
                        </a>
                    </li>
                    <li class="nav-item flex-fill">
                        <a class="nav-link" id="magang-tab" data-toggle="pill" href="#magang" role="tab" aria-controls="magang" aria-selected="false">
                            <i class="fas fa-user-graduate mr-2"></i>Pendaftaran Magang
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="offlineTabContent">

                    <div class="tab-pane fade show active" id="produk" role="tabpanel" aria-labelledby="produk-tab">
                        <form id="formProdukOffline">
                            @csrf
                            <div class="row">
                                <div class="col-md-7">

                                    {{-- Baris 1: Tipe Pembeli (full width) --}}
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom d-block">Tipe Pembeli</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="tipe_online_prod" name="tipe_pembeli_prod" class="custom-control-input" value="online">
                                            <label class="custom-control-label" for="tipe_online_prod">Punya Akun (Online)</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="tipe_offline_prod" name="tipe_pembeli_prod" class="custom-control-input" value="offline" checked>
                                            <label class="custom-control-label" for="tipe_offline_prod">Belum Akun (Offline)</label>
                                        </div>
                                    </div>

                                    {{-- Baris 2: Nama Pembeli (full width, atas-bawah dari Tipe) --}}
                                    <div id="wrapper_online_prod" style="display:none;" class="mb-3">
                                        <div class="form-group mb-0">
                                            <label class="form-label-custom">Nama Akun Online</label>
                                            <input type="hidden" name="user_id_online_prod" id="user_id_online_prod_val">
                                            <div class="autocomplete-wrapper">
                                                <input type="text" id="input_online_prod" class="input-nama-direct" placeholder="Ketik nama atau username akun online..." autocomplete="off">
                                                <div class="autocomplete-list" id="aclist_online_prod"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="wrapper_offline_prod" class="mb-3">
                                        <div class="form-group mb-0">
                                            <label class="form-label-custom">Nama Pembeli Offline</label>
                                            <input type="hidden" name="nama_pembeli_offline_prod" id="nama_pembeli_offline_prod_val">
                                            <div class="autocomplete-wrapper">
                                                <input type="text" id="input_offline_prod" class="input-nama-direct" placeholder="Ketik nama pembeli..." autocomplete="off">
                                                <div class="autocomplete-list" id="aclist_offline_prod"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Baris 3: Metode Pembayaran + No WA sampingan --}}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Metode Pembayaran</label>
                                                <select name="metode_pembayaran" class="form-control form-control-custom" required>
                                                    <option value="tunai" selected>Tunai (Cash)</option>
                                                    <option value="qris">QR (QRIS)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="wrapper_nohp_prod">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">No. WhatsApp</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text input-group-text-custom"><i class="fab fa-whatsapp"></i></span>
                                                    </div>
                                                    <input type="text" name="no_hp_prod" id="nohp_prod" class="form-control form-control-custom input-radius-addon" placeholder="Contoh: 08123456789">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card p-3 mb-4 shadow-sm" style="border-radius: 16px; background: rgba(0,123,255,0.03); border: 1px dashed rgba(0,123,255,0.2);">
                                        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-plus-circle mr-2"></i>Tambah Produk ke Keranjang</h6>
                                        <div class="row align-items-end">
                                            <div class="col-md-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label-custom" style="font-size: 0.75rem;">Pilih Produk</label>
                                                    <select id="prod_select" class="form-control form-control-custom">
                                                        <option value="" disabled selected>-- Pilih Produk --</option>
                                                        @foreach($products as $p)
                                                            <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->price }}" data-stock="{{ $p->stock }}">
                                                                {{ $p->name }} (Stok: {{ $p->stock }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-0">
                                                    <label class="form-label-custom" style="font-size: 0.75rem;">Jumlah (kg)</label>
                                                    <input type="number" id="prod_qty" placeholder="0" min="1" class="form-control form-control-custom">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" id="btn_add_product" class="btn btn-primary btn-block" style="padding: 10px 15px; font-size: 0.85rem; border-radius: 12px; font-weight: 700; height: 48px; box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15); transition: all 0.2s ease;">
                                                    <i class="fas fa-cart-plus mr-1"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-shopping-cart text-secondary mr-2"></i>Daftar Belanja</h6>
                                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 12px; background: white;">
                                        <table class="table table-hover align-middle mb-0" id="table_cart_products">
                                            <thead class="bg-light sticky-top">
                                                <tr>
                                                    <th class="py-2 pl-3" style="font-size: 0.8rem; border-top: none;">Produk</th>
                                                    <th class="py-2 text-right" style="font-size: 0.8rem; border-top: none;">Harga</th>
                                                    <th class="py-2 text-center" style="font-size: 0.8rem; border-top: none; width: 80px;">Qty</th>
                                                    <th class="py-2 text-right" style="font-size: 0.8rem; border-top: none;">Subtotal</th>
                                                    <th class="py-2 text-center" style="font-size: 0.8rem; border-top: none; width: 60px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cart_tbody">
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted" id="cart_empty_msg">
                                                        <i class="fas fa-shopping-basket fa-2x mb-2 d-block text-secondary" style="opacity: 0.5;"></i>
                                                        Belum ada produk yang ditambahkan.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="info-box-custom h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-info-circle text-info mr-2"></i>Ringkasan Pembayaran</h5>
                                            <hr>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Total Barang</span>
                                                <span class="font-weight-bold text-dark" id="prod_total_items_txt">0 Barang</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Subtotal</span>
                                                <span class="font-weight-bold text-dark" id="prod_subtotal_txt">Rp 0</span>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <hr>
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <span class="font-weight-bold text-dark">TOTAL HARGA</span>
                                                <span class="price-total-text" id="prod_total_txt">Rp 0</span>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block btn-submit-custom" id="btn_submit_order" disabled>
                                                <i class="fas fa-check-circle mr-2"></i>Transaksi Selesai
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="kunjungan" role="tabpanel" aria-labelledby="kunjungan-tab">
                        <form id="formKunjunganOffline">
                            @csrf
                            <div class="row">
                                <div class="col-md-7">

                                    {{-- Baris 1: Tipe Pengunjung (full width) --}}
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom d-block">Tipe Pengunjung</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="tipe_online_kun" name="tipe_pembeli_kun" class="custom-control-input" value="online">
                                            <label class="custom-control-label" for="tipe_online_kun">Punya Akun (Online)</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="tipe_offline_kun" name="tipe_pembeli_kun" class="custom-control-input" value="offline" checked>
                                            <label class="custom-control-label" for="tipe_offline_kun">Belum Akun (Offline)</label>
                                        </div>
                                    </div>

                                    {{-- Baris 2: Nama (full width, atas-bawah) --}}
                                    <div id="wrapper_online_kun" style="display:none;" class="mb-3">
                                        <div class="form-group mb-0">
                                            <label class="form-label-custom">Nama Akun Online</label>
                                            <input type="hidden" name="user_id_online_kun" id="user_id_online_kun_val">
                                            <div class="autocomplete-wrapper">
                                                <input type="text" id="input_online_kun" class="input-nama-direct" placeholder="Ketik nama atau username akun online..." autocomplete="off">
                                                <div class="autocomplete-list" id="aclist_online_kun"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="wrapper_offline_kun" class="mb-3">
                                        <div class="form-group mb-0">
                                            <label class="form-label-custom">Nama Penanggung Jawab Offline</label>
                                            <input type="hidden" name="nama_pembeli_offline_kun" id="nama_pembeli_offline_kun_val">
                                            <div class="autocomplete-wrapper">
                                                <input type="text" id="input_offline_kun" class="input-nama-direct" placeholder="Ketik nama penanggung jawab..." autocomplete="off">
                                                <div class="autocomplete-list" id="aclist_offline_kun"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Baris 3: No WA (full width untuk kunjungan) --}}
                                    <div id="wrapper_nohp_kun" class="mb-4">
                                        <div class="form-group mb-0">
                                            <label class="form-label-custom">No. WhatsApp</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text input-group-text-custom"><i class="fab fa-whatsapp"></i></span>
                                                </div>
                                                <input type="text" name="no_wa" id="nohp_kun" class="form-control form-control-custom input-radius-addon" placeholder="Contoh: 08123456789">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Nama Instansi / Lembaga</label>
                                        <input type="text" name="instansi" class="form-control form-control-custom" placeholder="Contoh: SMA Negeri 1 Bandung" required>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Pilih Paket Kunjungan</label>
                                        <select name="id_kunjungan" id="kun_select" class="form-control form-control-custom" required>
                                            <option value="" disabled selected>-- Pilih Paket Kunjungan --</option>
                                            @foreach($kunjungans as $k)
                                                <option value="{{ $k->id }}" data-price="{{ $k->price }}" data-min="{{ $k->min_people }}" data-max="{{ $k->max_people }}">
                                                    {{ $k->name }} (Rp {{ number_format($k->price, 0, ',', '.') }}/org)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Jumlah Peserta</label>
                                                <input type="number" name="jumlah_peserta" id="kun_qty" value="1" min="1" class="form-control form-control-custom" required>
                                                <small class="text-info font-weight-bold" id="kun_limit_txt"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Harga per Orang</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text input-group-text-custom">Rp</span>
                                                    </div>
                                                    <input type="text" id="kun_price" class="form-control form-control-custom input-radius-addon" readonly value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Metode Pembayaran</label>
                                        <select name="metode_pembayaran" class="form-control form-control-custom" required>
                                            <option value="tunai" selected>Tunai (Cash)</option>
                                            <option value="qris">QR (QRIS)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Jadwal Kunjungan</label>
                                        <p style="color: #555; font-size: 0.8rem; background: #fff8e1; padding: 8px 12px; border-left: 4px solid #ffc107; border-radius: 4px; margin-bottom: 10px; line-height: 1.4;">
                                           <i class="fas fa-info-circle text-warning"></i> Jadwal kunjungan minimal <b>H+3</b> dari hari ini. Tanggal merah dan jadwal penuh tidak bisa dipilih.
                                        </p>
                                        <div id="calendar-kunjungan-offline"></div>
                                        <input type="hidden" name="tanggal_reservasi" id="tanggal_reservasi_input" required>
                                    </div>

                                    <div class="info-box-custom">
                                        <div>
                                            <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-info-circle text-info mr-2"></i>Ringkasan Kunjungan</h5>
                                            <hr>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Harga Satuan Paket</span>
                                                <span class="font-weight-bold text-dark" id="kun_item_price_txt">Rp 0</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Jumlah Peserta</span>
                                                <span class="font-weight-bold text-dark" id="kun_qty_txt">0 Orang</span>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <hr>
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <span class="font-weight-bold text-dark">TOTAL HARGA</span>
                                                <span class="price-total-text" id="kun_total_txt">Rp 0</span>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block btn-submit-custom">
                                                <i class="fas fa-check-circle mr-2"></i>Transaksi Selesai
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="magang" role="tabpanel" aria-labelledby="magang-tab">
                        <form id="formMagangOffline">
                            @csrf
                            <div class="row">
                                <div class="col-md-7">
                                    {{-- Baris 1: Tipe Peserta (full width) --}}
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom d-block">Tipe Peserta</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="tipe_online_mag" name="tipe_pembeli_mag" class="custom-control-input" value="online">
                                            <label class="custom-control-label" for="tipe_online_mag">Punya Akun (Online)</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="tipe_offline_mag" name="tipe_pembeli_mag" class="custom-control-input" value="offline" checked>
                                            <label class="custom-control-label" for="tipe_offline_mag">Belum Akun (Offline)</label>
                                        </div>
                                    </div>

                                    {{-- Baris 2: Nama (full width, atas-bawah) --}}
                                    <div id="wrapper_online_mag" style="display:none;" class="mb-3">
                                        <div class="form-group mb-0">
                                            <label class="form-label-custom">Nama Akun Online</label>
                                            <input type="hidden" name="user_id_online_mag" id="user_id_online_mag_val">
                                            <div class="autocomplete-wrapper">
                                                <input type="text" id="input_online_mag" class="input-nama-direct" placeholder="Ketik nama atau username akun online..." autocomplete="off">
                                                <div class="autocomplete-list" id="aclist_online_mag"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="wrapper_offline_mag" class="mb-3">
                                        <div class="form-group mb-0">
                                            <label class="form-label-custom">Nama Peserta Offline</label>
                                            <input type="hidden" name="nama_pembeli_offline_mag" id="nama_pembeli_offline_mag_val">
                                            <div class="autocomplete-wrapper">
                                                <input type="text" id="input_offline_mag" class="input-nama-direct" placeholder="Ketik nama peserta magang..." autocomplete="off">
                                                <div class="autocomplete-list" id="aclist_offline_mag"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Baris 3: No WA (full width untuk magang) --}}
                                    <div id="wrapper_nohp_mag" class="mb-4">
                                        <div class="form-group mb-0">
                                            <label class="form-label-custom">No. WhatsApp</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text input-group-text-custom"><i class="fab fa-whatsapp"></i></span>
                                                </div>
                                                <input type="text" name="no_wa" id="nohp_mag" class="form-control form-control-custom input-radius-addon" placeholder="Contoh: 08123456789">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Instansi Asal</label>
                                        <input type="text" name="instansi" class="form-control form-control-custom" placeholder="Contoh: Universitas Gadjah Mada" required>
                                    </div>



                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Pilih Paket Magang</label>
                                        <select name="id_magang" id="mag_select" class="form-control form-control-custom" required>
                                            <option value="" disabled selected>-- Pilih Paket Magang --</option>
                                            @foreach($magangs as $m)
                                                <option value="{{ $m->id }}" data-price="{{ $m->price }}">
                                                    {{ $m->name }} (Rp {{ number_format($m->price, 0, ',', '.') }}/bulan)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Durasi Magang (Bulan)</label>
                                                <input type="number" name="durasi_magang" id="mag_duration" value="1" min="1" class="form-control form-control-custom" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label-custom">Harga Paket per Bulan</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text input-group-text-custom">Rp</span>
                                                    </div>
                                                    <input type="text" id="mag_price" class="form-control form-control-custom input-radius-addon" readonly value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Metode Pembayaran</label>
                                        <select name="metode_pembayaran" class="form-control form-control-custom" required>
                                            <option value="tunai" selected>Tunai (Cash)</option>
                                            <option value="qris">QR (QRIS)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Tanggal Mulai Magang</label>
                                        <p style="color: #555; font-size: 0.8rem; background: #fff8e1; padding: 8px 12px; border-left: 4px solid #ffc107; border-radius: 4px; margin-bottom: 10px; line-height: 1.4;">
                                           <i class="fas fa-info-circle text-warning"></i> Pilih tanggal mulai magang. Tanggal merah adalah hari libur dan tidak bisa dipilih.
                                        </p>
                                        <div id="calendar-magang-offline"></div>
                                        <input type="hidden" name="tanggal_magang" id="tanggal_magang_input" required>
                                    </div>

                                    <div class="info-box-custom">
                                        <div>
                                            <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-info-circle text-info mr-2"></i>Ringkasan Magang</h5>
                                            <hr>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Biaya per Bulan</span>
                                                <span class="font-weight-bold text-dark" id="mag_item_price_txt">Rp 0</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Durasi Magang</span>
                                                <span class="font-weight-bold text-dark" id="mag_dur_txt">0 Bulan</span>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <hr>
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <span class="font-weight-bold text-dark">TOTAL HARGA</span>
                                                <span class="price-total-text" id="mag_total_txt">Rp 0</span>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block btn-submit-custom">
                                                <i class="fas fa-check-circle mr-2"></i>Transaksi Selesai
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
window.NazframTransaksiOffline = {
    routes: {
        users: "{{ route('admin.transaksi-offline.users') }}",
        produk: "{{ route('admin.transaksi-offline.produk') }}",
        kunjungan: "{{ route('admin.transaksi-offline.kunjungan') }}",
        magang: "{{ route('admin.transaksi-offline.magang') }}",
        jadwalEvents: "{{ route('admin.jadwal.events') }}",
        redirectTransaksi: "{{ route('admin.transaksi') }}?status=offline",
        redirectKunjungan: "{{ route('admin.kunjungan-manajemen') }}?status=offline",
        redirectMagang: "{{ route('admin.magang-manajemen') }}?status=offline"
    }
};
</script>
<script src="{{ asset('js/admin/transaksi_offline.js') }}"></script>
@endpush
