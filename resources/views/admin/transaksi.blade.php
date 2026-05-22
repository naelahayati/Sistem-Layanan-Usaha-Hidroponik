@extends('admin.Theme.defualt')

@section('content')
<style>
    .status-badge {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: bold;
        display: inline-block;
    }
    .status-menunggu { background: #fff3cd; color: #856404; }
    .status-diproses { background: #d1ecf1; color: #0c5460; }
    .status-dikemas { background: #e2e3e5; color: #383d41; }
    .status-dikirim { background: #cce5ff; color: #004085; }
    .status-siap-diambil { background: #ebd8fc; color: #6a1b9a; }
    .status-selesai { background: #d4edda; color: #155724; }
    .status-dibatalkan { background: #f8d7da; color: #721c24; }
    
    .table thead th {
        border-top: none;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        color: #666;
    }
    .btn-detail {
        border-radius: 8px;
        padding: 6px 15px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-detail:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .nav-tabs-custom {
        border-bottom: 2px solid #eee;
        margin-bottom: 20px;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        color: #000000 !important;
        font-weight: 700;
        padding: 10px 20px;
        position: relative;
        transition: 0.3s;
    }
    .nav-tabs-custom .nav-link.active {
        color: #007bff !important;
        background: transparent;
    }
    .nav-tabs-custom .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: #007bff;
        border-radius: 3px 3px 0 0;
    }
    .nav-tabs-custom .nav-link:hover:not(.active) {
        color: #007bff;
    }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">Manajemen Transaksi</h1>
                @if($date)
                    <p class="text-primary mt-1">
                        <i class="fas fa-filter mr-1"></i> Menampilkan pesanan tanggal: <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>
                        <a href="{{ route('admin.transaksi') }}" class="ml-2 btn btn-xs btn-outline-danger" style="border-radius: 20px;">Hapus Filter</a>
                    </p>
                @endif
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Manajemen Transaksi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filter Status Tabs and Search -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <ul class="nav nav-tabs nav-tabs-custom mb-0 border-0 flex-grow-1">
                <li class="nav-item">
                    <a class="nav-link {{ !$status ? 'active' : '' }}" href="{{ route('admin.transaksi') }}">Semua</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $status == 'Diproses' ? 'active' : '' }}" href="{{ route('admin.transaksi', ['status' => 'Diproses']) }}">Diproses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'Sedang Dikemas' ? 'active' : '' }}" href="{{ route('admin.transaksi', ['status' => 'Sedang Dikemas']) }}">Dikemas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'Dikirim' ? 'active' : '' }}" href="{{ route('admin.transaksi', ['status' => 'Dikirim']) }}">Dikirim</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'Pesanan Siap Diambil' ? 'active' : '' }}" href="{{ route('admin.transaksi', ['status' => 'Pesanan Siap Diambil']) }}">Siap Diambil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'Selesai' ? 'active' : '' }}" href="{{ route('admin.transaksi', ['status' => 'Selesai']) }}">Selesai</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'Dibatalkan' ? 'active' : '' }}" href="{{ route('admin.transaksi', ['status' => 'Dibatalkan']) }}">Dibatalkan</a>
                </li>
            </ul>
            
            <form action="{{ route('admin.transaksi') }}" method="GET" class="d-flex mt-2 mt-md-0" style="min-width: 250px;">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="input-group shadow-sm" style="border-radius: 20px;">
                    <input type="text" name="search" class="form-control border-0" placeholder="Cari ID / Nama Pembeli..." value="{{ request('search') }}" style="border-radius: 20px 0 0 20px; background: #f8f9fa; padding-left: 20px;">
                    <div class="input-group-append">
                        <button class="btn btn-primary border-0" type="submit" style="border-radius: 0 20px 20px 0; padding: 0 20px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card shadow-sm border-0" style="border-radius:20px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 py-3">ID Pesanan</th>
                                <th class="py-3">Pembeli</th>
                                <th class="py-3">Tanggal Pesan</th>
                                <th class="py-3">Total Bayar</th>
                                <th class="py-3">Status</th>
                                <th class="text-center pr-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td class="pl-4">
                                    <span class="text-primary font-weight-bold">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <strong>{{ $order->user->name }}</strong>
                                </td>
                                <td>{{ $order->created_at->format('d M Y') }}<br><small>{{ $order->created_at->format('H:i') }} WIB</small></td>
                                <td><span class="text-success font-weight-bold" style="font-size: 1.05rem;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span></td>
                                <td>
                                    @php
                                        $statusClass = 'status-menunggu';
                                        if($order->status == 'Menunggu Konfirmasi') $statusClass = 'status-menunggu';
                                        if($order->status == 'Diproses') $statusClass = 'status-diproses';
                                        if($order->status == 'Sedang Dikemas') $statusClass = 'status-dikemas';
                                        if($order->status == 'Dikirim') $statusClass = 'status-dikirim';
                                        if($order->status == 'Pesanan Siap Diambil') $statusClass = 'status-siap-diambil';
                                        if($order->status == 'Selesai') $statusClass = 'status-selesai';
                                        if($order->status == 'Dibatalkan') $statusClass = 'status-dibatalkan';
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">{{ $order->status }}</span>
                                </td>
                                <td class="text-center pr-4">
                                    <button class="btn btn-info btn-detail" data-id="{{ $order->id }}">
                                        <i class="fas fa-search-plus mr-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="{{ asset('image/no-transaction.png') }}" alt="" style="width: 150px; opacity:0.5;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/4076/4076432.png'">
                                    <p class="text-muted mt-3">Belum ada transaksi masuk.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow" style="border-radius:20px;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius:20px 20px 0 0;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-file-invoice mr-2"></i> Rincian Transaksi <span id="det-id"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6 border-right">
                        <h6 class="font-weight-bold text-uppercase text-secondary mb-3" style="font-size: 0.75rem;"><i class="fas fa-user mr-2"></i> Data Pembeli</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td width="100">Nama</td><td>: <span id="det-nama" class="font-weight-bold"></span></td></tr>
                            <tr><td>User</td><td>: <span id="det-user"></span></td></tr>
                            <tr><td>No. WA</td><td>: <a id="det-wa-link" href="#" target="_blank" class="btn btn-xs btn-success ml-1" style="border-radius: 20px; font-size: 0.75rem; padding: 2px 10px;"><i class="fab fa-whatsapp mr-1"></i> <span id="det-nohp"></span></a></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6 pl-md-4">
                        <h6 class="font-weight-bold text-uppercase text-secondary mb-3" style="font-size: 0.75rem;"><i class="fas fa-shipping-fast mr-2"></i> Pengiriman & Bayar</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td width="120">Metode Kirim</td><td>: <span id="det-pengiriman" class="badge badge-secondary text-capitalize"></span></td></tr>
                            <tr><td>Metode Bayar</td><td>: <span id="det-pembayaran" class="badge badge-success text-uppercase"></span></td></tr>
                            <tr><td>Status Bayar</td><td>: <span id="det-bayar-status" class="badge badge-secondary p-1 px-2" style="border-radius: 6px;"></span></td></tr>
                            <tr id="det-bukti-row" style="display:none;"><td>Bukti Bayar</td><td>: <a href="#" target="_blank" id="det-bukti-link" class="btn btn-xs btn-outline-info">Lihat Bukti</a></td></tr>
                            <tr><td>Jarak</td><td>: <span id="det-jarak" class="font-weight-bold"></span> KM</td></tr>
                        </table>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="font-weight-bold text-uppercase text-secondary mb-0" style="font-size: 0.75rem;"><i class="fas fa-map-marker-alt mr-2"></i> Alamat & Lokasi Kurir</h6>
                        <a id="det-map-btn" href="#" target="_blank" class="btn btn-xs btn-outline-primary" style="border-radius: 20px; font-size: 0.7rem;"><i class="fas fa-external-link-alt mr-1"></i> Buka di Google Maps</a>
                    </div>
                    <div id="det-alamat" class="bg-light p-3 rounded mb-3" style="border-left: 4px solid #72a8d8; font-size: 0.9rem; color: #555;"></div>
                    <div id="map-container" class="rounded overflow-hidden shadow-sm" style="height: 200px; background: #eee; display: flex; align-items: center; justify-content: center;">
                        <span class="text-muted" id="map-placeholder">Titik koordinat tidak tersedia</span>
                        <iframe id="map-iframe" width="100%" height="100%" frameborder="0" style="border:0; display:none;" allowfullscreen></iframe>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="font-weight-bold text-uppercase text-secondary mb-3" style="font-size: 0.75rem;"><i class="fas fa-shopping-basket mr-2"></i> Produk Yang Dibeli</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="det-items">
                                <!-- JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Total Harga Produk</th>
                                    <th class="text-right text-dark" id="det-total-produk"></th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Ongkos Kirim</th>
                                    <th class="text-right text-dark" id="det-ongkir"></th>
                                </tr>
                                <tr class="bg-light">
                                    <th colspan="3" class="text-right font-weight-bold" style="font-size: 1rem;">GRAND TOTAL</th>
                                    <th class="text-right text-success font-weight-bold" id="det-grand-total" style="font-size: 1.1rem;"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mt-4 pt-2 border-top">
                    <h6 class="font-weight-bold text-uppercase text-secondary mb-3" style="font-size: 0.75rem;"><i class="fas fa-tasks mr-2"></i> Pengelolaan Status</h6>
                    
                    <div class="form-group mb-3">
                        <label class="text-uppercase text-muted font-weight-bold small">Status Pesanan Saat Ini</label>
                        <input type="text" id="det-status-skrg" class="form-control" readonly style="border-radius: 12px; background: #f1f3f5; font-weight: bold; color: #495057; border: 1px solid #dee2e6; height: 45px;">
                    </div>

                    <div class="bg-light p-3 rounded-lg border">
                        <label class="font-weight-bold text-dark small text-uppercase">Ubah Status Pesanan</label>
                        <div class="input-group">
                            <select id="status-select" class="form-control" style="border-radius: 10px 0 0 10px; height: 45px;">
                                <option value="Diproses">Diproses</option>
                                <option value="Sedang Dikemas">Sedang Dikemas</option>
                                <option value="Dikirim">Dikirim</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-primary font-weight-bold px-4" id="btn-update-status" style="border-radius: 0 10px 10px 0;">
                                    <i class="fas fa-save mr-1"></i> Update
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let currentOrderId = null;

        $('.btn-detail').click(function() {
            const id = $(this).data('id');
            currentOrderId = id;

            $.get(`/admin/transaksi/get/${id}`, function(data) {
                $('#det-id').text(`#ORD-${String(data.id).padStart(5, '0')}`);
                $('#det-nama').text(data.user.name);
                $('#det-user').text('@' + data.user.username);
                
                // WhatsApp Link
                const nohp = data.user.nohp || '';
                $('#det-nohp').text(nohp);
                if (nohp) {
                    let waNumber = nohp.replace(/[^0-9]/g, '');
                    if (waNumber.startsWith('0')) waNumber = '62' + waNumber.slice(1);
                    $('#det-wa-link').attr('href', `https://wa.me/${waNumber}`).show();
                } else {
                    $('#det-wa-link').hide();
                }

                $('#det-pengiriman').text(data.metode_pengiriman);
                $('#det-pembayaran').text(data.metode_pembayaran);
                $('#det-jarak').text(data.jarak || '0');
                $('#det-alamat').text(data.alamat);
                
                // Maps Integration
                const lat = data.user.latitude;
                const lng = data.user.longitude;
                if (lat && lng) {
                    const mapUrl = `https://www.google.com/maps?q=${lat},${lng}&output=embed`;
                    const gmapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
                    $('#map-iframe').attr('src', mapUrl).show();
                    $('#map-placeholder').hide();
                    $('#det-map-btn').attr('href', gmapsUrl).show();
                } else {
                    $('#map-iframe').hide();
                    $('#map-placeholder').show();
                    const addressUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(data.alamat)}`;
                    $('#det-map-btn').attr('href', addressUrl).show();
                }

                // Logika Indikator Bayar Khusus Admin
                let bayarLabel = 'Belum Bayar';
                let bayarClass = 'badge-danger';
                
                // Jika QRIS atau statusnya sudah Lunas/Selesai/Dikirim/Dikemas/Diproses, maka dianggap Sudah Bayar
                if (data.status === 'Menunggu Konfirmasi') {
                    bayarLabel = 'Menunggu Konfirmasi';
                    bayarClass = 'badge-warning';
                } else if (data.metode_pembayaran.toLowerCase() == 'qris' || 
                    ['Lunas', 'Selesai', 'Dikirim', 'Pesanan Siap Diambil', 'Sedang Dikemas', 'Diproses'].includes(data.status)) {
                    bayarLabel = 'Sudah Bayar';
                    bayarClass = 'badge-success';
                }
                $('#det-bayar-status').text(bayarLabel).attr('class', 'badge ' + bayarClass + ' p-1 px-2');

                if (data.bukti_pembayaran) {
                    $('#det-bukti-row').show();
                    $('#det-bukti-link').attr('href', '/storage/' + data.bukti_pembayaran);
                } else {
                    $('#det-bukti-row').hide();
                }

                $('#det-grand-total').text(formatRupiah(data.grand_total));
                
                // Isi Status Saat Ini
                $('#det-status-skrg').val(data.status);
                
                // Dynamic select option list based on shipment method
                let options = ['Diproses', 'Sedang Dikemas'];
                if (data.metode_pengiriman === 'pengantaran') {
                    options.push('Dikirim');
                } else {
                    options.push('Pesanan Siap Diambil');
                }
                options.push('Selesai', 'Dibatalkan');
                
                // If current status is not in the options list, add it at the beginning
                if (!options.includes(data.status)) {
                    options.unshift(data.status);
                }
                
                let optionsHtml = '';
                options.forEach(opt => {
                    optionsHtml += `<option value="${opt}">${opt}</option>`;
                });
                
                $('#status-select').html(optionsHtml);
                $('#status-select').val(data.status);

                let itemsHtml = '';
                data.items.forEach(item => {
                    const subtotal = item.price * item.quantity;
                    itemsHtml += `
                        <tr>
                            <td class="pl-2">${item.product.name}</td>
                            <td class="text-center">${item.quantity} kg</td>
                            <td class="text-right pr-2">${formatRupiah(item.price)}</td>
                            <td class="text-right pr-2 font-weight-bold">${formatRupiah(subtotal)}</td>
                        </tr>
                    `;
                });
                $('#det-items').html(itemsHtml);
                $('#modalDetail').modal('show');
            });
        });

        $('#btn-update-status').click(function() {
            const newStatus = $('#status-select').val();
            
            Swal.fire({
                title: 'Konfirmasi',
                text: `Ubah status pesanan ke "${newStatus}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/admin/transaksi/update-status/${currentOrderId}`, {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    }, function(response) {
                        if(response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }
    });
</script>
@endsection
