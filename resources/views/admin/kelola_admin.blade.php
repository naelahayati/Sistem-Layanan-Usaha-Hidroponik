@extends('admin.Theme.defualt')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kelola_admin.css') }}">
@endpush

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Kelola Admin</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Admin</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content page-kelola-admin">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Admin</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.admin.create') }}" class="btn shadow-sm px-4 btn-tambah-admin">
                                <i class="fas fa-plus mr-1"></i> Tambah Admin Baru
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                        <table class="table table-hover text-center align-middle mb-0" id="adminTable">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th class="py-3" width="5%">No</th>
                                    <th class="py-3 text-left">Nama Lengkap</th>
                                    <th class="py-3">Username</th>
                                    <th class="py-3 text-left">Email</th>
                                    <th class="py-3" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $key => $admin)
                                    <tr>
                                        <td class="py-3 text-muted">{{ $key + 1 }}</td>
                                        <td class="py-3 text-left">
                                            <div class="font-weight-bold text-dark">{{ $admin->name }}</div>
                                        </td>
                                        <td class="py-3 text-muted admin-username-cell">
                                            {{ $admin->username }}
                                        </td>
                                        <td class="py-3 text-left">
                                            <i class="far fa-envelope mr-1 text-muted"></i> {{ $admin->email }}
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex justify-content-center align-items-center btn-action-group">
                                                <a href="{{ route('admin.admin.edit_form', $admin->id) }}" class="btn btn-sm shadow-sm px-3 btn-edit-admin">
                                                    <i class="fas fa-edit mr-1" aria-hidden="true"></i><span class="btn-label">Edit</span>
                                                </a>
                                                <button type="button" class="btn btn-sm shadow-sm px-3 deleteAdminBtn btn-hapus-admin" data-id="{{ $admin->id }}">
                                                    <i class="fas fa-trash mr-1" aria-hidden="true"></i><span class="btn-label">Hapus</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-5 text-muted">
                                            <div class="mb-3"><i class="fas fa-user-shield fa-3x opacity-20"></i></div>
                                            Belum ada data admin terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@push('scripts')
<script src="{{ asset('js/admin/kelola_admin.js') }}"></script>
@endpush

@endsection
