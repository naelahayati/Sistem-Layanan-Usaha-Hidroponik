@extends('admin.Theme.defualt')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ isset($product) ? 'Edit Produk' : 'Tambah Produk Baru' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.produk-admin') }}">Kelola Produk</a></li>
                    <li class="breadcrumb-item active">{{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm" style="border-radius: 12px; border: none;">
            <div class="card-body">
                <form action="{{ isset($product) ? route('admin.produk.edit', $product->id) : route('admin.produk.add') }}" method="POST" enctype="multipart/form-data">
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
                                <label for="name">Nama Produk</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price">Harga</label>
                                        <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $product->price ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-{{ isset($product) ? '12' : '6' }}">
                                    @if(isset($product))
                                        <div class="form-group">
                                            <label>Stok Fisik Saat Ini (Total)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-info text-white"><i class="fas fa-boxes"></i></span>
                                                </div>
                                                <input type="number" class="form-control bg-light font-weight-bold" value="{{ $product->stock + $product->cart_stock }}" readonly>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-info-circle text-primary"></i> 
                                                Tersedia di Toko: <strong>{{ $product->stock }}</strong> | 
                                                Di Keranjang Aktif Pengguna: <strong>{{ $product->cart_stock }}</strong>
                                            </small>
                                            <input type="hidden" name="stock" value="{{ $product->stock + $product->cart_stock }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="add_stock">Tambah Stok (+)</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-success text-white"><i class="fas fa-plus"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control font-weight-bold text-success" id="add_stock" name="add_stock" value="0" min="0">
                                                    </div>
                                                    <small class="text-muted text-xs">Masukkan jumlah yang ingin ditambahkan.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="reduce_stock">Kurangi Stok (-)</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-danger text-white"><i class="fas fa-minus"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control font-weight-bold text-danger" id="reduce_stock" name="reduce_stock" value="0" min="0">
                                                    </div>
                                                    <small class="text-muted text-xs">Masukkan jumlah yang ingin dikurangi.</small>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label for="stock">Stok Awal</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-info text-white"><i class="fas fa-boxes"></i></span>
                                                </div>
                                                <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">{{ isset($product) ? 'Ganti Gambar Produk (Kosong jika tidak ganti)' : 'Gambar Produk' }}</label>
                                @if(isset($product) && $product->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $product->image) }}" class="rounded shadow-sm" style="width: 150px; height: auto; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label for="description">Deskripsi Produk</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi produk...">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                    <div class="mt-4 text-right">
                        <a href="{{ route('admin.produk-admin') }}" class="btn btn-secondary px-4 mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
