@extends('admin.Theme.defualt')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/transaksi.css') }}">
@endpush

@section('content')

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

<section class="content page-manajemen">
    <div class="container-fluid">
        <!-- Search di Atas Filter -->
        <div class="mb-3 w-100">
            <form action="{{ route('admin.transaksi') }}" method="GET">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="input-group shadow-sm" style="border-radius: 20px;">
                    <input type="text" name="search" class="form-control border-0" placeholder="Cari ID / Nama Pembeli..." value="{{ request('search') }}" style="border-radius: 20px 0 0 20px; background: #fff; padding-left: 20px;">
                    <div class="input-group-append">
                        <button class="btn btn-primary border-0" type="submit" style="border-radius: 0 20px 20px 0; padding: 0 20px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Filter Status Tabs -->
        <div class="d-flex flex-wrap mb-3">
            <ul class="nav nav-tabs nav-tabs-custom mb-0 border-0 w-100">
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
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'offline' ? 'active' : '' }}" href="{{ route('admin.transaksi', ['status' => 'offline']) }}">Transaksi Offline</a>
                </li>
            </ul>
            
            
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
                                    <button class="btn btn-sm btn-primary btn-detail" data-id="{{ $order->id }}">
                                        <i class="fas fa-eye mr-1"></i> Detail
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
            <div class="modal-header bg-primary text-white border-0" style="border-radius:20px 20px 0 0;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-file-invoice mr-2"></i> Rincian Transaksi <span id="det-id"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="row mb-3">
                    <div class="col-md-6 border-right">
                        <h6 class="font-weight-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem;"><i class="fas fa-user mr-2"></i> Data Pembeli</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td width="100">Nama</td><td>: <span id="det-nama" class="font-weight-bold"></span></td></tr>
                            <tr><td>User</td><td>: <span id="det-user"></span></td></tr>
                            <tr><td>No. WA</td><td>: <a id="det-wa-link" href="#" target="_blank" class="btn btn-xs btn-success ml-1" style="border-radius: 20px; font-size: 0.75rem; padding: 2px 10px;"><i class="fab fa-whatsapp mr-1"></i> <span id="det-nohp"></span></a></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6 pl-md-4">
                        <h6 class="font-weight-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem;"><i class="fas fa-shipping-fast mr-2"></i> Pengiriman & Bayar</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td width="120">Metode Kirim</td><td>: <span id="det-pengiriman" class="badge badge-secondary text-capitalize"></span></td></tr>
                            <tr><td>Metode Bayar</td><td>: <span id="det-pembayaran" class="badge badge-success text-uppercase"></span></td></tr>
                            <tr><td>Status Bayar</td><td>: <span id="det-bayar-status" class="badge badge-secondary p-1 px-2" style="border-radius: 6px;"></span></td></tr>
                            <tr id="det-bukti-row" style="display:none;"><td>Bukti Bayar</td><td>: <a href="#" target="_blank" id="det-bukti-link" class="btn btn-xs btn-outline-info">Lihat Bukti</a></td></tr>
                            <tr><td>Jarak</td><td>: <span id="det-jarak" class="font-weight-bold"></span> KM</td></tr>
                        </table>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="font-weight-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem;"><i class="fas fa-map-marker-alt mr-2"></i> Alamat & Lokasi Kurir</h6>
                    <div class="row no-gutters align-items-stretch">
                        <div class="col-md-8 pr-md-3">
                            <div id="det-alamat" class="bg-light p-3 rounded h-100" style="border-left: 4px solid #3b82f6; font-size: 0.9rem; color: #555; min-height: 70px;"></div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center justify-content-center bg-light rounded border p-3 mt-2 mt-md-0" style="min-height: 70px;">
                            <div class="text-center w-100">
                                <div class="small text-muted mb-2 font-weight-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Navigasi Rute</div>
                                <a id="det-map-btn" href="#" target="_blank" class="btn btn-sm btn-outline-primary btn-block" style="border-radius: 20px; font-size: 0.8rem; font-weight: 600; padding: 6px 12px;">
                                    <i class="fas fa-map-marked-alt mr-1"></i> Google Maps
                                </a>
                                <span class="text-muted font-weight-bold small" id="det-map-placeholder" style="display: none;"><i class="fas fa-exclamation-triangle mr-1"></i>Tidak Ada Maps</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="font-weight-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem;"><i class="fas fa-shopping-basket mr-2"></i> Produk Yang Dibeli</h6>
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

                <div class="mt-3 pt-2 border-top">
                    <h6 class="font-weight-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem;"><i class="fas fa-tasks mr-2"></i> Pengelolaan Status</h6>
                    <div class="row align-items-end">
                        <div class="col-md-5 mb-2 mb-md-0">
                            <div class="form-group mb-0">
                                <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem;">Status Saat Ini</label>
                                <input type="text" id="det-status-skrg" class="form-control" readonly style="border-radius: 10px; background: #f8fafc; font-weight: 700; color: #1e293b; border: 1px solid #cbd5e1; height: 38px; font-size: 0.85rem;">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-muted text-uppercase" style="font-size: 0.7rem;">Ubah Status Pesanan</label>
                                <div class="input-group">
                                    <select id="status-select" class="form-control" style="border-radius: 10px 0 0 10px; height: 38px; font-size: 0.85rem; border: 1px solid #cbd5e1;">
                                        <option value="Diproses">Diproses</option>
                                        <option value="Sedang Dikemas">Sedang Dikemas</option>
                                        <option value="Dikirim">Dikirim</option>
                                        <option value="Selesai">Selesai</option>
                                        <option value="Dibatalkan">Dibatalkan</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary font-weight-bold px-3" id="btn-update-status" style="border-radius: 0 10px 10px 0; height: 38px; font-size: 0.85rem;">
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
                    const gmapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
                    $('#det-map-btn').attr('href', gmapsUrl).show();
                    $('#det-map-placeholder').hide();
                } else if (data.alamat) {
                    const addressUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(data.alamat)}`;
                    $('#det-map-btn').attr('href', addressUrl).show();
                    $('#det-map-placeholder').hide();
                } else {
                    $('#det-map-btn').hide();
                    $('#det-map-placeholder').show();
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

                $('#det-total-produk').text(formatRupiah(data.total_produk));
                $('#det-ongkir').text(formatRupiah(data.ongkir));
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
