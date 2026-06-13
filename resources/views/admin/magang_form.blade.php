@extends('admin.Theme.defualt')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ isset($magang) ? 'Edit Magang' : 'Tambah Magang Baru' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.magang-admin') }}">Kelola Magang</a></li>
                    <li class="breadcrumb-item active">{{ isset($magang) ? 'Edit Magang' : 'Tambah Magang' }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm" style="border-radius: 12px; border: none;">
            <div class="card-body">
                <form action="{{ isset($magang) ? route('admin.magang.edit', $magang->id) : route('admin.magang.add') }}" method="POST" enctype="multipart/form-data" id="{{ isset($magang) ? 'editMagangForm' : 'addMagangForm' }}">
                    @if(isset($magang))
                        <input type="hidden" id="editMagangId" value="{{ $magang->id }}">
                    @endif
                    @csrf
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nama Program Magang</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $magang->name ?? '') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Biaya program</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $magang->price ?? '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">{{ isset($magang) ? 'Ganti Gambar Cover (Kosong jika tidak ganti)' : 'Gambar Cover Magang' }}</label>
                                @if(isset($magang) && $magang->image)
                                    <div class="mb-2">
                                        <img src="{{ $magang->image_url }}" class="rounded shadow-sm" style="width: 100px; height: auto; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" {{ isset($magang) ? '' : 'required' }}>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label for="description">Deskripsi Program</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi magang..." required>{{ old('description', $magang->description ?? '') }}</textarea>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="d-block">Metode Konfirmasi</label>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-success {{ (old('is_wa_confirmation', $magang->is_wa_confirmation ?? false)) ? 'active' : '' }}" style="border-radius: 8px 0 0 8px; font-weight: 600;">
                                        <input type="radio" name="is_wa_confirmation" value="1" {{ (old('is_wa_confirmation', $magang->is_wa_confirmation ?? false)) ? 'checked' : '' }}> 
                                        <i class="fab fa-whatsapp mr-1"></i> Konfirmasi WA
                                    </label>
                                    <label class="btn btn-outline-primary {{ !(old('is_wa_confirmation', $magang->is_wa_confirmation ?? false)) ? 'active' : '' }}" style="border-radius: 0 8px 8px 0; font-weight: 600;">
                                        <input type="radio" name="is_wa_confirmation" value="0" {{ !(old('is_wa_confirmation', $magang->is_wa_confirmation ?? false)) ? 'checked' : '' }}> 
                                        <i class="fas fa-bolt mr-1"></i> Non-Konfirmasi WA
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="d-block">Input Deskripsi Kemampuan</label>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-info {{ (old('show_skill_description', $magang->show_skill_description ?? false)) ? 'active' : '' }}" style="border-radius: 8px 0 0 8px; font-weight: 600;">
                                        <input type="radio" name="show_skill_description" value="1" {{ (old('show_skill_description', $magang->show_skill_description ?? false)) ? 'checked' : '' }}> 
                                        <i class="fas fa-eye mr-1"></i> Munculkan
                                    </label>
                                    <label class="btn btn-outline-secondary {{ !(old('show_skill_description', $magang->show_skill_description ?? false)) ? 'active' : '' }}" style="border-radius: 0 8px 8px 0; font-weight: 600;">
                                        <input type="radio" name="show_skill_description" value="0" {{ !(old('show_skill_description', $magang->show_skill_description ?? false)) ? 'checked' : '' }}> 
                                        <i class="fas fa-eye-slash mr-1"></i> Sembunyikan
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-right">
                        <a href="{{ route('admin.magang-admin') }}" class="btn btn-secondary px-4 mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

</section>

@push('scripts')
<script src="{{ asset('js/admin/magang_admin.js') }}"></script>
@endpush

@endsection
