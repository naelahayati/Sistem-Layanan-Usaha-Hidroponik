@extends('admin.Theme.defualt')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ isset($kunjungan) ? 'Edit Kunjungan' : 'Tambah Kunjungan Baru' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.kunjungan-admin') }}">Kelola Kunjungan</a></li>
                    <li class="breadcrumb-item active">{{ isset($kunjungan) ? 'Edit Kunjungan' : 'Tambah Kunjungan' }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm" style="border-radius: 12px; border: none;">
            <div class="card-body">
                <form action="{{ isset($kunjungan) ? route('admin.kunjungan.edit', $kunjungan->id) : route('admin.kunjungan.add') }}" method="POST" enctype="multipart/form-data">
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
                                <label for="name">Nama Kunjungan</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $kunjungan->name ?? '') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Harga</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $kunjungan->price ?? '') }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="min_people">Minimal Orang</label>
                                        <input type="number" class="form-control" id="min_people" name="min_people" value="{{ old('min_people', $kunjungan->min_people ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="max_people">Maksimal Orang</label>
                                        <input type="number" class="form-control" id="max_people" name="max_people" value="{{ old('max_people', $kunjungan->max_people ?? '') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">{{ isset($kunjungan) ? 'Ganti Gambar Kunjungan (Kosong jika tidak ganti)' : 'Gambar Kunjungan' }}</label>
                                @if(isset($kunjungan) && $kunjungan->image)
                                    <div class="mb-2">
                                        <img src="{{ $kunjungan->image_url }}" class="rounded shadow-sm" style="width: 150px; height: auto; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" {{ isset($kunjungan) ? '' : 'required' }}>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label for="description">Deskripsi (Materi, Fasilitas, dll)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi kunjungan..." required>{{ old('description', $kunjungan->description ?? '') }}</textarea>
                    </div>

                    <div class="mt-4 text-right">
                        <a href="{{ route('admin.kunjungan-admin') }}" class="btn btn-secondary px-4 mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
