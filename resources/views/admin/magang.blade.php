@extends('admin.Theme.defualt')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/magang.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/kelola-paket.css') }}">
@endpush

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Kelola Magang</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Magang</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row" id="magangContainer">
            <!-- Card Tambah Magang -->
            <div class="col-md-3 col-sm-4 col-6 mb-4">
                <a href="{{ route('admin.magang.create') }}" style="text-decoration: none;">
                    <div class="card h-100 d-flex justify-content-center align-items-center" style="cursor:pointer; background-color: #E3F2FD; border: 2px dashed #90CAF9; min-height: 250px;">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                            <i class="fas fa-plus fa-3x" style="color: #1565C0;"></i>
                            <h6 class="mt-3" style="color: #1565C0;">Tambah Magang</h6>
                        </div>
                    </div>
                </a>
            </div>

            <!-- List Magang -->
            @foreach($magangs as $magang)
            <div class="col-md-3 col-sm-4 col-6 mb-4">
                <div class="card h-100 position-relative shadow-sm viewMagangCard" style="cursor:pointer; border-radius: 10px; overflow: hidden;" data-magang="{{ json_encode($magang) }}">
                    @if($magang->image)
                    <img src="{{ $magang->image_url }}" class="card-img-top" alt="{{ $magang->name }}" style="height: 180px; width: 100%; object-fit: contain;">
                    @else
                    <div class="card-img-top d-flex justify-content-center align-items-center bg-light" style="height: 180px;">
                        <span class="text-muted"><i class="fas fa-image fa-2x"></i></span>
                    </div>
                    @endif
                    
                    <div class="card-body p-3">
                        <h6 class="card-title text-truncate w-100 mb-2 font-weight-bold" style="font-size: 1rem;">{{ $magang->name }}</h6>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="position-absolute" style="bottom: 10px; right: 10px; z-index: 10;">
                        <a href="{{ route('admin.magang.edit_form', $magang->id) }}" class="btn btn-sm btn-light text-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm deleteMagangBtn" data-id="{{ $magang->id }}" style="margin-left: 4px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>





<!-- Modal View Magang -->
<div class="modal fade" id="viewMagangModal" tabindex="-1" role="dialog" aria-labelledby="viewMagangModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header border-0 pl-4 pr-4 pt-4 pb-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center pb-4 px-4">
                <div class="mb-3 d-flex justify-content-center" id="viewImageContainer">
                    <img id="viewImage" src="" class="img-fluid rounded shadow-sm d-none" style="max-height: 250px; width: 100%; object-fit: contain;">
                    <div id="viewNoImage" class="d-none justify-content-center align-items-center bg-light rounded shadow-sm" style="width: 250px; height: 250px;">
                        <span class="text-muted"><i class="fas fa-image fa-3x"></i></span>
                    </div>
                </div>
                <h4 id="viewName" class="font-weight-bold mb-1"></h4>
                <div class="d-flex justify-content-center align-items-center bg-light rounded py-2 mt-3 mb-3">
                    <span class="text-muted mr-2">Biaya Program:</span>
                    <h5 class="mb-0 text-success font-weight-bold" id="viewPrice"></h5>
                </div>
                <div class="text-left bg-light rounded p-3">
                    <p class="text-muted mb-1 font-weight-bold">Deskripsi:</p>
                    <p id="viewDescription" class="mb-0" style="white-space: pre-line;"></p>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="{{ asset('js/admin/magang_admin.js') }}"></script>

@endsection
