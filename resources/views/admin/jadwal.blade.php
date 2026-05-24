@extends('admin.Theme.defualt')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/jadwal.css') }}">
@endpush

@section('content')

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<div class="content-header">
    <div class="container-fluid">
        <div class="mb-3">
            <h1 class="m-0 font-weight-bold" style="color: #1a202c;">Kelola Jadwal</h1>
            <p class="text-muted mb-0">Atur hari libur dan pantau aktivitas harian Naz Hidrofarm.</p>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Bagian Kalender -->
            <div class="col-lg-8 mb-4">
                <div id='calendar'></div>
                <div class="mt-4 p-3 bg-white rounded shadow-sm border d-flex flex-wrap gap-4 align-items-center">
                    <span class="mr-3"><div class="legend-dot" style="background: #ffe3e3; border: 1px solid #d9534f; border-radius: 2px; width: 16px; height: 16px;"></div> Libur (Hari Merah)</span>
                    <span class="mr-3"><div class="legend-dot" style="background: var(--kunjungan-green);"></div> Kunjungan</span>
                    <span class="mr-3"><div class="legend-dot" style="background: var(--pkl-blue);"></div> Magang PKL</span>
                    <span><div class="legend-dot" style="background: var(--umum-blue);"></div> Magang Umum</span>
                </div>
            </div>

            <!-- Bagian Samping (Detail & Form) -->
            <div class="col-md-4">
                
                <!-- Panel Detail Kegiatan -->
                <div class="card card-premium d-none mb-4" id="detailPanel">
                    <div class="card-header bg-white">
                        <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-info-circle mr-2 text-primary"></i> Detail Jadwal</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="closeDetailBtn"><i class="fas fa-times text-muted"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 id="detailTitle" class="font-weight-bold mb-1 text-dark"></h5>
                        <div class="mb-3">
                            <span id="detailKategori" class="badge"></span>
                        </div>
                        <div class="bg-light p-3 rounded mb-3">
                            <p class="text-muted mb-1" style="font-size: 0.85rem;"><i class="far fa-calendar-alt mr-1"></i> <span id="detailDate"></span></p>
                        </div>
                        <h6 class="font-weight-bold" style="font-size: 0.85rem; color: #666;">DESKRIPSI / DAFTAR PESERTA:</h6>
                        <div id="detailDeskripsi" class="p-2" style="white-space: pre-line; background: #fff; border-radius: 8px; border: 1px dashed #ccc; font-size: 0.9rem;"></div>
                    </div>
                    <div class="card-footer bg-white text-right border-0 pb-3">
                        <button class="btn btn-warning btn-sm px-3" id="btnEditJadwal"><i class="fas fa-edit mr-1"></i> Edit</button>
                        <button class="btn btn-danger btn-sm px-3" id="btnDeleteJadwal"><i class="fas fa-trash mr-1"></i> Hapus</button>
                    </div>
                </div>

                <!-- Panel Form Jadwal -->
                <div class="card card-premium" id="formPanel">
                    <div class="card-header bg-white">
                        <h3 class="card-title font-weight-bold text-dark" id="formTitle"><i class="fas fa-plus-circle mr-2 text-success"></i> Kelola Libur</h3>
                    </div>
                    <div class="card-body">
                        <form id="jadwalForm">
                            @csrf
                            <input type="hidden" id="jadwalId" name="id">
                            
                            <div class="form-group mb-3">
                                <label class="fw-bold mb-1" style="font-size: 0.85rem;">Nama Libur / Hari Raya</label>
                                <input type="text" class="custom-box form-control" id="jadwalTitle" name="title" placeholder="Contoh: Idul Fitri" required>
                            </div>
                            
                            <div class="form-group d-none">
                                <label>Kategori</label>
                                <select class="custom-box form-control" id="jadwalKategori" name="kategori" required>
                                    <option value="libur" selected>Libur (Merah)</option>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold mb-1" style="font-size: 0.85rem;">Mulai</label>
                                    <input type="date" class="custom-box form-control" id="jadwalStart" name="start_date" required>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold mb-1" style="font-size: 0.85rem;">Selesai</label>
                                    <input type="date" class="custom-box form-control" id="jadwalEnd" name="end_date">
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="fw-bold mb-1" style="font-size: 0.85rem;">Catatan Libur</label>
                                <textarea class="custom-box form-control" id="jadwalDeskripsi" name="deskripsi" rows="3" placeholder="Info tambahan..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 font-weight-bold" style="border-radius: 10px;" id="btnSaveJadwal">Simpan Hari Libur</button>
                            <button type="button" class="btn btn-light w-100 mt-2 d-none" id="btnCancelEdit" style="border-radius: 10px;">Batal</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script src="{{ asset('js/admin/jadwal_admin.js') }}?v={{ time() }}"></script>

@endsection
