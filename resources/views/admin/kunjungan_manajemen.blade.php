@extends('admin.Theme.defualt')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kunjungan_manajemen.css') }}">
@endpush

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">Manajemen Kunjungan</h1>
                @if($date)
                    <p class="text-primary mt-1">
                        <i class="fas fa-filter mr-1"></i> Menampilkan kunjungan tanggal: <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>
                        <a href="{{ route('admin.kunjungan-manajemen') }}" class="ml-2 btn btn-xs btn-outline-danger" style="border-radius: 20px;">Hapus Filter</a>
                    </p>
                @endif
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Manajemen Kunjungan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content page-manajemen">
    <div class="container-fluid">
        <!-- Search di Atas Filter -->
        <div class="mb-3 w-100">
            <form action="{{ route('admin.kunjungan-manajemen') }}" method="GET">
                @if($statusFilter)
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif
                <div class="input-group shadow-sm" style="border-radius: 20px;">
                    <input type="text" name="search" class="form-control border-0" placeholder="Cari ID / Nama / Paket..." value="{{ request('search') }}" style="border-radius: 20px 0 0 20px; background: #fff; padding-left: 20px;">
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
                    <a class="nav-link {{ !$statusFilter ? 'active' : '' }}" href="{{ route('admin.kunjungan-manajemen') }}">Semua Status</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter == 'Aktif' ? 'active' : '' }}" href="{{ route('admin.kunjungan-manajemen', ['status' => 'Aktif']) }}">Belum Dilakukan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter == 'Diterima' ? 'active' : '' }}" href="{{ route('admin.kunjungan-manajemen', ['status' => 'Diterima']) }}">Diterima</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter == 'Selesai' ? 'active' : '' }}" href="{{ route('admin.kunjungan-manajemen', ['status' => 'Selesai']) }}">Sudah Dilakukan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter == 'Dibatalkan' ? 'active' : '' }}" href="{{ route('admin.kunjungan-manajemen', ['status' => 'Dibatalkan']) }}">Dibatalkan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter == 'offline' ? 'active' : '' }}" href="{{ route('admin.kunjungan-manajemen', ['status' => 'offline']) }}">Transaksi Offline</a>
                </li>
            </ul>
            
            
        </div>

        <div class="card shadow-sm border-0" style="border-radius:20px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 py-3">ID</th>
                                <th class="py-3">User</th>
                                <th class="py-3">Paket</th>
                                <th class="py-3">Tanggal Kunjungan</th>
                                <th class="py-3">Keterangan Waktu</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-center">Aksi Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kunjungans as $k)
                            @php
                                $visitDate = \Carbon\Carbon::parse($k->tanggal_reservasi);
                                $today = \Carbon\Carbon::today();
                                
                                $timeInfo = '';
                                $timeClass = '';
                                
                                if ($k->status_pembayaran == 'Dibatalkan' || $k->status_pembayaran == 'Tidak Diterima') {
                                    $timeInfo = "Batal";
                                    $timeClass = "badge-danger opacity-75";
                                } elseif ($today->lt($visitDate)) {
                                    $diff = $today->diffInDays($visitDate);
                                    $timeInfo = $diff . " Hari Lagi";
                                    $timeClass = "badge-info";
                                } elseif ($today->eq($visitDate)) {
                                    $timeInfo = "Hari Ini";
                                    $timeClass = "badge-warning";
                                } else {
                                    $timeInfo = "Sudah Selesai";
                                    $timeClass = "badge-light text-muted";
                                }
                            @endphp
                            <tr>
                                <td class="pl-4">
                                    <span class="text-primary font-weight-bold">#KUN-{{ str_pad($k->id_reservasi, 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <strong>{{ $k->user_name }}</strong>
                                </td>
                                <td>{{ $k->paket_name }}</td>
                                <td>{{ $visitDate->format('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $timeClass }} p-2" style="border-radius: 8px; font-size: 0.8rem;">
                                        <i class="fas fa-clock mr-1"></i> {{ $timeInfo }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $bayarClass = 'badge-secondary';
                                        if($k->status_pembayaran == 'Lunas' || $k->status_pembayaran == 'Diterima') $bayarClass = 'badge-success';
                                        elseif($k->status_pembayaran == 'Dibatalkan' || $k->status_pembayaran == 'Tidak Diterima') $bayarClass = 'badge-danger';
                                        elseif($k->status_pembayaran == 'Menunggu Pembayaran' || $k->status_pembayaran == 'Pending') $bayarClass = 'badge-warning';
                                        elseif($k->status_pembayaran == 'Selesai') $bayarClass = 'badge-info';
                                    @endphp
                                    <span class="badge {{ $bayarClass }} p-2" style="border-radius: 10px;">
                                        {{ $k->status_pembayaran }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary btn-detail-kunjungan" 
                                            data-id="{{ $k->id_reservasi }}" 
                                            data-all="{{ json_encode($k) }}"
                                            data-time="{{ $timeInfo }}"
                                            style="border-radius: 10px; padding: 6px 15px;">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <p class="text-muted">Belum ada data kunjungan.</p>
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

<!-- Modal Detail & Update Status -->
<div class="modal fade" id="modalDetailKunjungan" tabindex="-1" role="dialog" aria-labelledby="modalDetailKunjunganLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 25px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 p-4">
                <h5 class="modal-title font-weight-bold" id="modalDetailKunjunganLabel"><i class="fas fa-calendar-check mr-2"></i> Detail Reservasi Kunjungan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <!-- Sisi Kiri: Informasi Utama -->
                    <div class="col-md-7 border-right">
                        <h6 class="text-uppercase text-muted font-weight-bold small mb-3">Informasi Pendaftar</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%" class="text-muted">Nama Lengkap</td>
                                <td class="font-weight-bold">: <span id="det-nama"></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Username</td>
                                <td class="font-weight-bold">: <span id="det-username"></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Instansi/Lembaga</td>
                                <td class="font-weight-bold">: <span id="det-instansi"></span></td>
                            </tr>
                        </table>

                        <h6 class="text-uppercase text-muted font-weight-bold small mt-4 mb-3">Rencana Kunjungan</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%" class="text-muted">Paket Kunjungan</td>
                                <td class="font-weight-bold text-primary">: <span id="det-paket"></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Kunjungan</td>
                                <td class="font-weight-bold">: <span id="det-tanggal"></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jumlah Peserta</td>
                                <td class="font-weight-bold">: <span id="det-peserta"></span> Orang</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Keterangan</td>
                                <td class="font-weight-bold">: <span id="det-time-info" class="badge badge-warning p-1 px-2" style="border-radius: 6px;"></span></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Sisi Kanan: Pembayaran & Status -->
                    <div class="col-md-5">
                        <h6 class="text-uppercase text-muted font-weight-bold small mb-3">Pembayaran</h6>
                        <div class="p-3 rounded-lg mb-3" style="background: #f8f9fa; border: 1px dashed #ddd;">
                            <div class="small text-muted mb-1">Metode Pembayaran: <span id="det-metode" class="text-dark font-weight-bold"></span></div>
                            <div class="h5 font-weight-bold text-success mb-2" id="det-total"></div>
                            <div class="small text-muted">Indikator Bayar: <span id="det-bayar-status" class="badge badge-secondary p-1 px-2" style="border-radius: 6px;"></span></div>
                            <div id="det-bukti-container" style="display:none; margin-top: 10px;">
                                <a href="#" target="_blank" id="det-bukti-link" class="btn btn-sm btn-outline-info btn-block"><i class="fas fa-image mr-1"></i> Lihat Bukti Pembayaran</a>
                            </div>
                        </div>

                        <div class="form-group mt-4 pt-2">
                            <label class="text-uppercase text-muted font-weight-bold small">Status Saat Ini</label>
                            <input type="text" id="det-status-skrg" class="form-control mb-3" readonly style="border-radius: 12px; background: #e9ecef; font-weight: bold; color: #495057; border: 1px solid #ced4da;">
                            
                            <label class="text-uppercase text-muted font-weight-bold small">Ubah Status Kunjungan</label>
                            <form id="formUpdateStatus">
                                @csrf
                                <input type="hidden" id="reservasi_id">
                                <select class="form-control form-control-lg mb-3" id="selectStatus" style="border-radius: 12px; font-size: 1rem; border: 2px solid #eee;">
                                    <option value="Diterima">Diterima</option>
                                    <option value="Dibatalkan">Dibatalkan / Tidak Diterima</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold" style="border-radius: 12px; box-shadow: 0 5px 15px rgba(0,123,255,0.2);">
                                    Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
function runScript() {
    if (window.jQuery) {
        $(document).ready(function() {
            // Tombol Detail Kunjungan
            $(document).on('click', '.btn-detail-kunjungan', function() {
                const data = $(this).data('all');
                const timeInfo = $(this).data('time');
                
                $('#reservasi_id').val(data.id_reservasi);
                $('#det-nama').text(data.user_name);
                $('#det-username').text(data.user_username);
                $('#det-instansi').text(data.instansi || '-');
                $('#det-paket').text(data.paket_name);
                $('#det-tanggal').text(moment(data.tanggal_reservasi).format('DD MMMM YYYY'));
                $('#det-peserta').text(data.jumlah_peserta);
                $('#det-time-info').text(timeInfo);
                $('#det-metode').text((data.metode_pembayaran || 'tunai').toUpperCase());
                $('#det-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.total_harga));
                $('#det-status-skrg').val(data.status_pembayaran);
                $('#selectStatus').val(data.status_pembayaran);

                // Logika Indikator Bayar Khusus Admin
                let bayarLabel = 'Belum Bayar';
                let bayarClass = 'badge-danger';
                
                // Jika QRIS atau statusnya sudah Diterima/Lunas/Selesai/Menunggu Konfirmasi, maka dianggap Sudah Bayar atau Menunggu
                if (['Menunggu Konfirmasi'].includes(data.status_pembayaran)) {
                    bayarLabel = 'Menunggu Konfirmasi';
                    bayarClass = 'badge-warning';
                } else if (data.metode_pembayaran.toLowerCase() == 'qris' || 
                    ['Lunas', 'Diterima', 'Selesai'].includes(data.status_pembayaran)) {
                    bayarLabel = 'Sudah Bayar';
                    bayarClass = 'badge-success';
                }
                $('#det-bayar-status').text(bayarLabel).attr('class', 'badge ' + bayarClass + ' p-1 px-2');

                if (data.bukti_pembayaran) {
                    $('#det-bukti-container').show();
                    let buktiUrl = data.bukti_pembayaran;
                    if (!buktiUrl.startsWith('http')) {
                        buktiUrl = '/storage/' + buktiUrl;
                    }
                    $('#det-bukti-link').attr('href', buktiUrl);
                } else {
                    $('#det-bukti-container').hide();
                }
                
                $('#modalDetailKunjungan').modal('show');
            });

            // Form Update Status
            $('#formUpdateStatus').on('submit', function(e) {
                e.preventDefault();
                const id = $('#reservasi_id').val();
                const status = $('#selectStatus').val();
                const token = $('input[name="_token"]').val();

                $.ajax({
                    url: `/admin/kunjungan/update-status/${id}`,
                    method: 'POST',
                    data: {
                        _token: token,
                        status: status
                    },
                    success: function(response) {
                        $('#modalDetailKunjungan').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat memperbarui status.'
                        });
                    }
                });
            });
        });
    } else {
        setTimeout(runScript, 50);
    }
}
runScript();
</script>
@endsection
