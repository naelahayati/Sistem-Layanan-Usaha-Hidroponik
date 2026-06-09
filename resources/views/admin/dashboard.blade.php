@extends('admin.Theme.defualt')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush

@section('content')

<!-- Meta refresh setiap 300 detik (5 menit) untuk efek realtime -->
<meta http-equiv="refresh" content="300">

<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0 text-dark">Dashboard Overview</h1>
        <p class="text-muted small">Ringkasan aktivitas Naz Hidrofarm hari ini, {{ date('d F Y') }}</p>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Pesanan Hari Ini -->
            <div class="col-md-6 mb-4">
                <div class="card card-outline card-info shadow-sm" style="border-radius: 15px; height: 300px;">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-shopping-bag mr-2 text-info"></i> Pesanan Hari Ini (Realtime)</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th class="pl-3 py-2">ID</th>
                                        <th class="py-2">Nama Pembeli</th>
                                        <th class="py-2">Pengambilan</th>
                                        <th class="py-2 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $o)
                                    <tr>
                                        <td class="pl-3 py-2 font-weight-bold">
                                            #{{ $o->id }}
                                        </td>
                                        <td class="py-2 text-truncate" style="max-width: 150px;">
                                            {{ $o->user_name }}
                                        </td>
                                        <td class="py-2">
                                            <span class="badge badge-light text-capitalize" style="font-size: 0.7rem; border: 1px solid #eee;">{{ $o->metode_pengiriman }}</span>
                                        </td>
                                        <td class="py-2 text-center">
                                            <span class="badge {{ $o->status == 'Lunas' ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.7rem;">{{ $o->status }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small">
                                            <i class="fas fa-shopping-cart fa-2x mb-2 d-block opacity-50"></i>
                                            Belum ada pesanan masuk hari ini
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-2 text-center" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; border-top: 1px solid #f4f6f9;">
                        <a href="{{ route('admin.transaksi') }}" class="text-info font-weight-bold small">
                            MANAJEMEN TRANSAKSI <i class="fas fa-chevron-right ml-1" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Jadwal Kunjungan 3 Hari Kedepan -->
            <div class="col-md-6 mb-4">
                <div class="card card-outline card-success shadow-sm" style="border-radius: 15px; height: 300px;">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-calendar-alt mr-2 text-success"></i> Jadwal Kunjungan 3 Hari Kedepan</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th class="pl-3 py-2">Tgl</th>
                                        <th class="py-2">Instansi / User</th>
                                        <th class="py-2 text-center">Org</th>
                                        <th class="py-2">Paket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($detailKunjunganHariIni as $k)
                                    <tr>
                                        <td class="pl-3 py-2"><span class="badge badge-success" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($k->tanggal_reservasi)->format('d/m') }}</span></td>
                                        <td class="py-2">
                                            <div class="font-weight-bold text-truncate" style="max-width: 150px;">{{ $k->instansi ?: $k->user_name }}</div>
                                            <small class="text-muted">{{ $k->user_name }}</small>
                                        </td>
                                        <td class="py-2 text-center">{{ $k->jumlah_peserta }}</td>
                                        <td class="py-2 text-truncate" style="max-width: 100px;">{{ $k->paket_name }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small">
                                            <i class="fas fa-calendar-times fa-2x mb-2 d-block opacity-50"></i>
                                            Tidak ada kunjungan terjadwal 3 hari kedepan
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-2 text-center" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; border-top: 1px solid #f4f6f9;">
                        <a href="{{ route('admin.kunjungan-manajemen') }}" class="text-success font-weight-bold small">
                            MANAJEMEN KUNJUNGAN <i class="fas fa-chevron-right ml-1" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Detail Lists for Orders and Internships -->
        <div class="row">
            <!-- Jadwal Magang 3 Hari Kedepan -->
            <div class="col-md-6 mb-4">
                <div class="card card-outline card-warning shadow-sm" style="border-radius: 15px; height: 300px;">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-user-clock mr-2 text-warning"></i> Jadwal Magang 3 Hari Kedepan</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th class="pl-3 py-2">Tgl</th>
                                        <th class="py-2">Nama / Instansi</th>
                                        <th class="py-2">Paket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($detailMagangMulai as $m)
                                    <tr>
                                        <td class="pl-3 py-2"><span class="badge badge-warning text-white" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($m->tanggal_magang)->format('d/m') }}</span></td>
                                        <td class="py-2">
                                            <div class="font-weight-bold">{{ $m->user_name }}</div>
                                            <small class="text-muted">{{ $m->instansi ?: '-' }}</small>
                                        </td>
                                        <td class="py-2 small">{{ $m->paket_name }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted small">
                                            <i class="fas fa-user-graduate fa-2x mb-2 d-block opacity-50"></i>
                                            Tidak ada magang yang mulai 3 hari kedepan
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-2 text-center" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; border-top: 1px solid #f4f6f9;">
                        <a href="{{ route('admin.magang-manajemen') }}" class="text-warning font-weight-bold small">
                            MANAJEMEN MAGANG <i class="fas fa-chevron-right ml-1" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pendaftaran Baru Hari Ini -->
            <div class="col-md-6 mb-4">
                <div class="card card-outline card-primary shadow-sm" style="border-radius: 15px;">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-bell mr-2 text-primary"></i> Pendaftaran Baru Hari Ini</h3>
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="list-group list-group-unbordered mb-3">
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2" style="border-top: 0;">
                                <span class="text-muted"><i class="fas fa-chalkboard-teacher mr-2 text-primary"></i> Magang</span>
                                <span class="badge badge-primary px-2 py-1" style="border-radius: 6px;">{{ $daftarMagangHariIni }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span class="text-muted"><i class="fas fa-users-cog mr-2 text-success"></i> Kunjungan</span>
                                <span class="badge badge-success px-2 py-1" style="border-radius: 6px;">{{ $daftarKunjunganHariIni }}</span>
                            </div>
                        </div>
                        <div class="px-1">
                             <a href="{{ route('admin.laporan') }}" class="btn btn-block py-2 shadow-sm font-weight-bold" style="border-radius: 12px; background-color: #e7f3ff; color: #007bff; border: 1px solid #cce5ff; transition: all 0.3s;">
                                <i class="fas fa-file-invoice-dollar mr-2"></i> Laporan Pendapatan Hari Ini
                             </a>
                        </div>
                    </div>
                    <div class="card-footer bg-light py-2 text-center" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; font-size: 0.7rem;">
                        <span class="text-muted small"><i class="fas fa-sync-alt fa-spin mr-1"></i> Terakhir diperbarui: {{ date('H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
         <!-- Row: Rekapitulasi Status Hari Ini -->
        <div class="row">
            <!-- Status Pesanan -->
            <div class="col-md-4 mb-4">
                <div class="card card-outline card-info shadow-sm" style="border-radius:15px;">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-shopping-bag mr-2 text-info"></i> Status Pesanan Hari Ini
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        @foreach([
                            'Menunggu Konfirmasi'  => 'warning',
                            'Diproses'             => 'info',
                            'Sedang Dikemas'       => 'primary',
                            'Pesanan Siap Diambil' => 'success',
                            'dikirim'              => 'secondary',
                        ] as $status => $color)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">{{ $status }}</span>
                            <span class="badge badge-{{ $color }} px-2 py-1">{{ $statusPesanan[$status] ?? 0 }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-white py-2 text-center" style="border-radius:0 0 15px 15px;">
                        <a href="{{ route('admin.transaksi') }}" class="text-info font-weight-bold small">
                            LIHAT SEMUA <i class="fas fa-chevron-right ml-1" style="font-size:0.7rem;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Kunjungan -->
            <div class="col-md-4 mb-4">
                <div class="card card-outline card-success shadow-sm" style="border-radius:15px;">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-calendar-alt mr-2 text-success"></i> Status Kunjungan Hari Ini
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        @foreach([
                            'Menunggu Konfirmasi' => 'warning',
                            'Diterima'            => 'success',
                        ] as $status => $color)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">{{ $status }}</span>
                            <span class="badge badge-{{ $color }} px-2 py-1">{{ $statusKunjungan[$status] ?? 0 }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-white py-2 text-center" style="border-radius:0 0 15px 15px;">
                        <a href="{{ route('admin.kunjungan-manajemen') }}" class="text-success font-weight-bold small">
                            LIHAT SEMUA <i class="fas fa-chevron-right ml-1" style="font-size:0.7rem;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Magang -->
            <div class="col-md-4 mb-4">
                <div class="card card-outline card-warning shadow-sm" style="border-radius:15px;">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-user-graduate mr-2 text-warning"></i> Status Magang Aktif
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        @foreach([
                            'Menunggu Konfirmasi' => 'warning',
                            'Terkonfirmasi'       => 'info',
                            'Diterima'            => 'success',
                        ] as $status => $color)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">{{ $status }}</span>
                            <span class="badge badge-{{ $color }} px-2 py-1">{{ $statusMagang[$status] ?? 0 }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-white py-2 text-center" style="border-radius:0 0 15px 15px;">
                        <a href="{{ route('admin.magang-manajemen') }}" class="text-warning font-weight-bold small">
                            LIHAT SEMUA <i class="fas fa-chevron-right ml-1" style="font-size:0.7rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
    </div>
</section>
@endsection
