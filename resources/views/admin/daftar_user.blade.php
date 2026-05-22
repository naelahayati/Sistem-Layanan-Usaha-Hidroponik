@extends('admin.Theme.defualt')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin/daftar_user.css') }}">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar User</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Daftar User</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Pengguna Terdaftar</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="text" id="searchUser" class="form-control float-right" placeholder="Cari User...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap" id="userTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $key => $user)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-sm btn-detail-user" 
                                                data-name="{{ $user->name }}"
                                                data-username="{{ $user->username }}"
                                                data-email="{{ $user->email }}"
                                                data-nohp="{{ $user->nohp }}"
                                                data-alamat="{{ $user->alamat }}"
                                                data-lat="{{ $user->latitude }}"
                                                data-lng="{{ $user->longitude }}"
                                                data-status="{{ ucfirst($user->status) }}">
                                                <i class="fas fa-id-card mr-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data user</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Detail User -->
<div class="modal fade" id="modalDetailUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-circle mr-2"></i> Detail Pengguna</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-light d-inline-block p-3 rounded-circle mb-3 shadow-sm">
                        <i class="fas fa-user fa-3x text-primary"></i>
                    </div>
                    <h4 id="user-det-name" class="font-weight-bold mb-0"></h4>
                    <p id="user-det-username" class="text-muted"></p>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted text-uppercase d-block mb-1">Email</small>
                        <span id="user-det-email" class="font-weight-bold"></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase d-block mb-1">Status</small>
                        <span id="user-det-status" class="badge"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted text-uppercase d-block mb-1">Nomor WhatsApp</small>
                    <div class="d-flex align-items-center">
                        <span id="user-det-nohp" class="font-weight-bold"></span>
                        <a id="user-det-wa-link" href="#" target="_blank" class="btn btn-success btn-xs ml-2" style="border-radius: 20px;">
                            <i class="fab fa-whatsapp"></i> Hubungi WA
                        </a>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted text-uppercase d-block mb-1">Alamat Lengkap</small>
                    <p id="user-det-alamat" class="bg-light p-2 rounded" style="font-size: 0.9rem;"></p>
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted text-uppercase">Lokasi (Maps)</small>
                        <a id="user-det-map-btn" href="#" target="_blank" class="btn btn-outline-primary btn-xs" style="border-radius: 20px;">
                            <i class="fas fa-external-link-alt"></i> Buka Maps
                        </a>
                    </div>
                    <div id="user-map-container" class="rounded overflow-hidden" style="height: 150px; background: #eee; display: flex; align-items: center; justify-content: center;">
                        <iframe id="user-map-iframe" width="100%" height="100%" frameborder="0" style="border:0; display:none;" allowfullscreen></iframe>
                        <span id="user-map-placeholder" class="text-muted small">Koordinat tidak tersedia</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/admin/daftar_user.js') }}"></script>

@endsection
