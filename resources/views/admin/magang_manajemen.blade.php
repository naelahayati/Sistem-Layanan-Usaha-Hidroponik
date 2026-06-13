@extends('admin.Theme.defualt')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/magang_manajemen.css') }}">
@endpush

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">Manajemen Magang</h1>
                @if($date)
                    <p class="text-primary mt-1">
                        <i class="fas fa-filter mr-1"></i> Menampilkan magang aktif tanggal: <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>
                        <a href="{{ route('admin.magang-manajemen') }}" class="ml-2 btn btn-xs btn-outline-danger" style="border-radius: 20px;">Hapus Filter</a>
                    </p>
                @endif
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Manajemen Magang</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content page-manajemen">
    <div class="container-fluid">

        <!-- Search di Atas Filter -->
        <div class="mb-3 w-100">
            <form action="{{ route('admin.magang-manajemen') }}" method="GET">
                @if($statusFilter)
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif
                @if($subMagangFilter)
                    <input type="hidden" name="sub_magang" value="{{ $subMagangFilter }}">
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
        <div class="d-flex flex-wrap mb-0">
            <ul class="nav nav-tabs nav-tabs-custom mb-0 border-0 w-100">
                <li class="nav-item">
                    <a class="nav-link {{ !$statusFilter ? 'active' : '' }}" href="{{ route('admin.magang-manajemen', array_merge(request()->query(), ['status' => ''])) }}">Semua Status</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter == 'Diterima' ? 'active' : '' }}" href="{{ route('admin.magang-manajemen', array_merge(request()->query(), ['status' => 'Diterima'])) }}">Diterima</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter == 'Aktif' ? 'active' : '' }}" href="{{ route('admin.magang-manajemen', array_merge(request()->query(), ['status' => 'Aktif'])) }}">Magang Aktif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter == 'Selesai' ? 'active' : '' }}" href="{{ route('admin.magang-manajemen', array_merge(request()->query(), ['status' => 'Selesai'])) }}">Magang Selesai</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter == 'Dibatalkan' ? 'active' : '' }}" href="{{ route('admin.magang-manajemen', array_merge(request()->query(), ['status' => 'Dibatalkan'])) }}">Dibatalkan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter == 'offline' ? 'active' : '' }}" href="{{ route('admin.magang-manajemen', array_merge(request()->query(), ['status' => 'offline'])) }}">Transaksi Offline</a>
                </li>
            </ul>


        </div>

        <!-- Filter Sub Magang (PKL / Umum) -->
        <div class="mb-4 mt-2">
            <div class="btn-group btn-group-toggle shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #eee;">
                <a href="{{ route('admin.magang-manajemen', array_merge(request()->query(), ['sub_magang' => 'Semua'])) }}"
                   class="btn btn-sm {{ $subMagangFilter == 'Semua' ? 'btn-primary' : 'btn-white' }} px-4 py-2 font-weight-bold">
                   Semua Program
                </a>
                <a href="{{ route('admin.magang-manajemen', array_merge(request()->query(), ['sub_magang' => 'PKL'])) }}"
                   class="btn btn-sm {{ $subMagangFilter == 'PKL' ? 'btn-primary' : 'btn-white' }} px-4 py-2 font-weight-bold">
                   Program PKL
                </a>
                <a href="{{ route('admin.magang-manajemen', array_merge(request()->query(), ['sub_magang' => 'Magang Umum'])) }}"
                   class="btn btn-sm {{ $subMagangFilter == 'Magang Umum' ? 'btn-primary' : 'btn-white' }} px-4 py-2 font-weight-bold">
                   Magang Umum
                </a>
            </div>
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
                                <th class="py-3">Tanggal Mulai Magang</th>
                                <th class="py-3">Keterangan Waktu</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-center">Aksi Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($magangs as $m)
                            @php
                                $startDate = \Carbon\Carbon::parse($m->tanggal_magang);
                                $endDate = \Carbon\Carbon::parse($m->tanggal_magang)->addMonths($m->durasi_magang);
                                $today = \Carbon\Carbon::today();

                                $timeInfo = '';
                                $timeClass = '';
                                if ($m->status_pembayaran == 'Dibatalkan' || $m->status_pembayaran == 'Tidak Diterima') {
                                    $timeInfo = "Batal";
                                    $timeClass = "badge-danger opacity-75";
                                } elseif ($today < $startDate) {
                                    $diff = $today->diffInDays($startDate);
                                    $timeInfo = $diff . " Hari Lagi Mulai";
                                    $timeClass = "badge-info";
                                } elseif ($today <= $endDate) {
                                    $diff = $today->diffInDays($endDate);
                                    $timeInfo = $diff . " Hari Lagi Selesai";
                                    $timeClass = "badge-warning";
                                } else {
                                    $timeInfo = "Sudah Selesai";
                                    $timeClass = "badge-light text-muted";
                                }
                            @endphp
                            <tr>
                                <td class="pl-4">
                                    <span class="text-primary font-weight-bold">#MAG-{{ str_pad($m->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <strong>{{ $m->user_name }}</strong>
                                </td>
                                <td>{{ $m->paket_name }}</td>
                                <td>{{ $startDate->format('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $timeClass }} p-2" style="border-radius: 8px; font-size: 0.8rem;">
                                        <i class="fas fa-clock mr-1"></i> {{ $timeInfo }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'badge-secondary';
                                        if($m->status_pembayaran == 'Diterima' || $m->status_pembayaran == 'Lunas') $statusClass = 'badge-success';
                                        elseif($m->status_pembayaran == 'Aktif') $statusClass = 'status-aktif';
                                        elseif($m->status_pembayaran == 'Tidak Diterima' || $m->status_pembayaran == 'Dibatalkan') $statusClass = 'badge-danger';
                                        elseif($m->status_pembayaran == 'Menunggu Konfirmasi' || $m->status_pembayaran == 'Pending') $statusClass = 'badge-warning';
                                        elseif($m->status_pembayaran == 'Selesai') $statusClass = 'badge-info';
                                    @endphp
                                    <span class="badge {{ $statusClass }} p-2" style="border-radius: 10px;">
                                        {{ $m->status_pembayaran }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary btn-detail-magang"
                                            data-id="{{ $m->id }}"
                                            data-all="{{ json_encode($m) }}"
                                            data-time="{{ $timeInfo }}"
                                            style="border-radius: 10px; padding: 6px 15px;">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <p class="text-muted">Belum ada data magang.</p>
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

<!-- Modal Detail & Edit Status -->
<div class="modal fade" id="modalDetailMagang" tabindex="-1" role="dialog" aria-labelledby="modalDetailMagangLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 25px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 p-4">
                <h5 class="modal-title font-weight-bold" id="modalDetailMagangLabel"><i class="fas fa-user-graduate mr-2"></i> Detail Pendaftaran Magang</h5>
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
                                <td class="text-muted">Instansi/Sekolah</td>
                                <td class="font-weight-bold">: <span id="det-instansi"></span></td>
                            </tr>
                        </table>

                        <h6 class="text-uppercase text-muted font-weight-bold small mt-4 mb-3">Rencana Kegiatan</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%" class="text-muted">Paket</td>
                                <td class="font-weight-bold text-primary">: <span id="det-paket"></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Mulai Magang</td>
                                <td class="font-weight-bold">: <span id="det-mulai"></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Durasi</td>
                                <td class="font-weight-bold">: <span id="det-durasi"></span> Bulan</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Keterangan</td>
                                <td class="font-weight-bold">: <span id="det-time-info" class="badge badge-warning p-1 px-2" style="border-radius: 6px;"></span></td>
                            </tr>
                        </table>

                        <h6 class="text-uppercase text-muted font-weight-bold small mt-4 mb-3">Kemampuan Dasar</h6>
                        <div class="p-3 bg-light rounded" id="det-deskripsi" style="font-size: 0.9rem; min-height: 60px; white-space: pre-line;"></div>
                    </div>

                    <!-- Sisi Kanan: Pembayaran & Status -->
                    <div class="col-md-5">
                        <h6 class="text-uppercase text-muted font-weight-bold small mb-3">Status & Pembayaran</h6>
                        <div class="p-3 rounded-lg mb-3" style="background: #f8f9fa; border: 1px dashed #ddd;">
                            <div class="small text-muted mb-1">Metode: <span id="det-metode" class="text-dark font-weight-bold"></span></div>
                            <div class="h5 font-weight-bold text-success mb-2" id="det-total"></div>
                            <div class="small text-muted">Indikator Bayar: <span id="det-bayar-status" class="badge badge-secondary p-1 px-2" style="border-radius: 6px;"></span></div>
                            <div id="det-bukti-container" style="display:none; margin-top: 10px;">
                                <a href="#" target="_blank" id="det-bukti-link" class="btn btn-sm btn-outline-info btn-block"><i class="fas fa-image mr-1"></i> Lihat Bukti Pembayaran</a>
                            </div>
                        </div>

                        <div class="form-group mt-4 pt-2">
                            <label class="text-uppercase text-muted font-weight-bold small">Status Saat Ini</label>
                            <input type="text" id="det-status-skrg" class="form-control mb-3" readonly style="border-radius: 12px; background: #e9ecef; font-weight: bold; color: #495057; border: 1px solid #ced4da;">

                            <label class="text-uppercase text-muted font-weight-bold small">Ubah Status Konfirmasi</label>
                            <form id="formUpdateStatus">
                                @csrf
                                <input type="hidden" id="magang_id">
                                <select class="form-control form-control-lg mb-3" id="selectStatus" style="border-radius: 12px; font-size: 1rem; border: 2px solid #eee;">
                                    <option value="Menunggu Konfirmasi">Menunggu Konfirmasi</option>
                                    <option value="Terkonfirmasi">Terkonfirmasi</option>
                                    <option value="Diterima">Diterima</option>
                                    <option value="Tidak Diterima">Tidak Diterima</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold" style="border-radius: 12px; box-shadow: 0 5px 15px rgba(0,123,255,0.2);">
                                    Update Status
                                </button>
                            </form>
                        </div>

                        <div class="alert alert-info mt-3 p-2 small border-0" style="border-radius: 10px; background: #eef7ff; color: #3182ce;">
                            <i class="fas fa-info-circle mr-1"></i> Status <strong>Menunggu Konfirmasi</strong> diatur otomatis oleh sistem.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function runScript() {
    if (window.jQuery) {
        $(document).ready(function() {
            // Tombol Detail Diklik
            $(document).on('click', '.btn-detail-magang', function() {
                const data = $(this).data('all');
                const timeInfo = $(this).data('time');

                // Isi data ke modal
                $('#magang_id').val(data.id);
                $('#det-nama').text(data.user_name);
                $('#det-username').text(data.user_username);
                $('#det-instansi').text(data.pekerjaan || '-');
                $('#det-paket').text(data.paket_name);
                $('#det-mulai').text(moment(data.tanggal_magang).format('DD MMMM YYYY'));
                $('#det-durasi').text(data.durasi_magang);
                $('#det-time-info').text(timeInfo);
                $('#det-deskripsi').text(data.deskripsi_kemampuan || 'Tidak ada deskripsi.');
                $('#det-metode').text(data.metode_pembayaran.toUpperCase());
                $('#det-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.total_harga));

                // Logika Indikator Bayar Khusus Admin
                let bayarLabel = 'Belum Bayar';
                let bayarClass = 'badge-danger';

                if (['Menunggu Konfirmasi'].includes(data.status_pembayaran)) {
                    bayarLabel = 'Menunggu Konfirmasi';
                    bayarClass = 'badge-warning';
                } else if (data.metode_pembayaran.toLowerCase() == 'qris' || data.metode_pembayaran.toLowerCase() == 'tunai' ||
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

                // Isi Status Saat Ini (Text Box)
                $('#det-status-skrg').val(data.status_pembayaran);

                // Set value dropdown status
                $('#selectStatus').val(data.status_pembayaran);

                // Kunci jika Selesai
                if (data.status_pembayaran === 'Selesai') {
                    $('#selectStatus').attr('disabled', true);
                    $('#formUpdateStatus button[type="submit"]').attr('disabled', true).addClass('btn-secondary').removeClass('btn-primary');
                    $('#det-status-skrg').parent().find('small.text-danger').remove();
                    $('#det-status-skrg').parent().append('<small class="text-danger font-weight-bold mt-1"><i class="fas fa-lock mr-1"></i> Data Terkunci (Selesai)</small>');
                } else {
                    $('#selectStatus').attr('disabled', false);
                    $('#formUpdateStatus button[type="submit"]').attr('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                    $('#det-status-skrg').parent().find('small.text-danger').remove();
                }

                $('#modalDetailMagang').modal('show');
            });

            // Submit Update Status
            $('#formUpdateStatus').on('submit', function(e) {
                e.preventDefault();
                const id = $('#magang_id').val();
                const status = $('#selectStatus').val();
                const token = $('input[name="_token"]').val();

                let confirmText = `Ubah status magang ke "${status}"?`;
                let confirmIcon = 'question';
                
                if (status === 'Tidak Diterima' || status === 'Dibatalkan') {
                    confirmText = "Apakah Anda yakin ingin membatalkan/menolak pendaftaran magang ini?";
                    confirmIcon = 'warning';
                } else if (status === 'Selesai') {
                    confirmText = "Tandai pendaftaran ini sebagai Selesai? Setelah status menjadi 'Selesai', data akan DIKUNCI dan tidak bisa diubah lagi untuk keamanan laporan.";
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: confirmText,
                    icon: confirmIcon,
                    showCancelButton: true,
                    confirmButtonColor: (status === 'Tidak Diterima' || status === 'Dibatalkan') ? '#d33' : '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Update!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Harap tunggu sebentar, sedang memperbarui status.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });

                        $.ajax({
                            url: `/admin/magang/update-status/${id}`,
                            method: 'POST',
                            data: {
                                _token: token,
                                status: status
                            },
                            success: function(response) {
                                $('#modalDetailMagang').modal('hide');
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
                                let msg = 'Terjadi kesalahan saat memperbarui status.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: msg
                                });
                            }
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
