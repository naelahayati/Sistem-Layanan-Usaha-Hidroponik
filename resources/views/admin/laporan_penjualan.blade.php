@extends('admin.Theme.defualt')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/laporan_penjualan.css') }}">
@endpush

@section('title', 'Laporan Pendapatan')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">Laporan Pendapatan Terpadu</h1>
                <p class="text-muted small">Rekapitulasi otomatis aktivitas bisnis Naz Hidrofarm.</p>
            </div>
            <div class="col-sm-6 text-right d-print-none">
                <button type="button" class="btn btn-success shadow-sm px-4 py-2" style="border-radius: 12px; font-weight: bold; background: linear-gradient(135deg, #28a745, #218838); border: none;" onclick="window.print()">
                    <i class="fas fa-print mr-2"></i> Cetak Laporan
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content page-laporan">
    <div class="container-fluid">

        <!-- Filter Card (Hides on Print) -->
        <div class="card shadow-sm border-0 d-print-none" style="border-radius: 15px;">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.laporan') }}" id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted uppercase">Dari Tanggal</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control border-0 bg-light px-3" style="border-radius: 10px; height: 45px;" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted uppercase">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control border-0 bg-light px-3" style="border-radius: 10px; height: 45px;" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted uppercase">Kategori</label>
                            <select name="kategori" class="form-control border-0 bg-light px-3" style="border-radius: 10px; height: 45px;" onchange="this.form.submit()">
                                <option value="Semua" {{ $kategoriFilter == 'Semua' ? 'selected' : '' }}>Semua Laporan</option>
                                <option value="Produk" {{ $kategoriFilter == 'Produk' ? 'selected' : '' }}>Penjualan Produk</option>
                                <option value="Kunjungan" {{ $kategoriFilter == 'Kunjungan' ? 'selected' : '' }}>Kunjungan</option>
                                <option value="Magang" {{ $kategoriFilter == 'Magang' ? 'selected' : '' }}>Magang / PKL</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted uppercase">Metode Pembayaran</label>
                            <select name="metode_bayar" class="form-control border-0 bg-light px-3" style="border-radius: 10px; height: 45px;" onchange="this.form.submit()">
                                <option value="Semua" {{ $metodeBayarFilter == 'Semua' ? 'selected' : '' }}>Semua Metode</option>
                                <option value="Tunai" {{ $metodeBayarFilter == 'Tunai' ? 'selected' : '' }}>Tunai (Cash)</option>
                                <option value="QRIS" {{ $metodeBayarFilter == 'QRIS' ? 'selected' : '' }}>QR (QRIS)</option>
                            </select>
                        </div>


                        @if($kategoriFilter == 'Magang')
                        <div class="col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted uppercase">Tipe Magang</label>
                            <select name="sub_magang" class="form-control border-0 bg-light px-3" style="border-radius: 10px; height: 45px;" onchange="this.form.submit()">
                                <option value="Semua" {{ $subMagangFilter == 'Semua' ? 'selected' : '' }}>Semua Tipe</option>
                                <option value="PKL" {{ $subMagangFilter == 'PKL' ? 'selected' : '' }}>Khusus PKL</option>
                                <option value="Magang Umum" {{ $subMagangFilter == 'Magang Umum' ? 'selected' : '' }}>Magang Umum</option>
                            </select>
                        </div>
                        @endif

                        <div class="col-md-{{ $kategoriFilter == 'Magang' ? '9' : '12' }} mb-3">
                            <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <input type="text" name="search" value="{{ $search }}" class="form-control border-0 bg-light px-3" placeholder="Cari transaksi, nama pelanggan, atau tanggal..." style="height: 45px;">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary px-4" style="background: #4e73df; border: none;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Table Section -->
        <div class="card shadow-sm border-0 mb-5" style="border-radius: 15px; overflow: hidden;">
            <div class="card-body p-0">
                <!-- Kop Surat untuk Print -->
                <div class="d-none d-print-block text-center py-4 border-bottom mb-4">
                    <h2 class="font-weight-bold mb-1">NAZ HIDROFARM</h2>
                    <p class="mb-0">Laporan Pendapatan Aktual - Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle">
                        <thead style="background-color: #f8f9fc;">
                            <tr class="text-muted small text-uppercase">
                                <th class="py-3" width="5%">No</th>
                                <th class="py-3" width="15%">ID Transaksi</th>
                                <th class="py-3" width="12%">Tanggal</th>
                                <th class="py-3 text-left" width="35%">Keterangan Transaksi</th>

                                <th class="py-3 text-right pr-4" width="20%">Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporans as $index => $item)
                            <tr style="transition: background 0.2s;">
                                <td class="py-3 text-muted">{{ $index + 1 }}</td>
                                <td class="py-3">{{ $item->id_transaksi }}</td>
                                <td class="py-3 font-weight-bold">{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                                <td class="py-3 text-left">
                                    <div class="font-weight-600 text-dark" style="font-size: 0.95rem;">{!! $item->keterangan !!}</div>
                                </td>

                                <td class="py-3 text-right pr-4 font-weight-bold text-dark" style="font-size: 1rem;">
                                    {{ number_format($item->harga, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-5 text-muted">
                                    <div class="mb-3"><i class="fas fa-folder-open fa-3x opacity-20"></i></div>
                                    Tidak ada data transaksi ditemukan untuk periode ini.
                                </td>
                            </tr>
                            @endforelse
                            {{-- Tampilan Layar (ada kolom Metode, total colspan=4) --}}
                            <tr class="d-print-none" style="background-color: #f8f9fc;">
                                <th colspan="4" class="py-4 text-right pr-4 text-uppercase text-muted">Total Pendapatan Terakumulasi</th>
                                <th class="py-4 text-right pr-4 text-primary" style="font-size: 1.4rem; font-weight: 800;">
                                    Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                                </th>
                            </tr>
                            {{-- Tampilan Cetak (kolom Metode hilang, total colspan=4) --}}
                            <tr class="d-none d-print-table-row" style="background-color: #f8f9fc;">
                                <th colspan="4" class="py-4 text-right pr-4 text-uppercase text-muted">Total Pendapatan Terakumulasi</th>
                                <th class="py-4 text-right pr-4 text-dark" style="font-size: 1.4rem; font-weight: 800;">
                                    Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>
@push('scripts')
<script src="{{ asset('js/admin/laporan_penjualan.js') }}"></script>
@endpush
@endsection
