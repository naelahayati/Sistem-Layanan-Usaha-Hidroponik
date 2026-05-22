@extends('admin.Theme.defualt')

@section('content')

<style>
    .password-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .password-masked {
        font-family: monospace;
        letter-spacing: 2px;
    }
</style>

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

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Admin</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.admin.create') }}" class="btn shadow-sm px-4" style="border-radius: 10px; background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; font-weight: bold;">
                                <i class="fas fa-plus mr-1"></i> Tambah Admin Baru
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover text-center align-middle" id="adminTable">
                            <thead style="background-color: #f8f9fc;">
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
                                    <tr style="transition: background 0.2s;">
                                        <td class="py-3 text-muted">{{ $key + 1 }}</td>
                                        <td class="py-3 text-left">
                                            <div class="font-weight-bold text-dark">{{ $admin->name }}</div>
                                        </td>
                                        <td class="py-3 text-muted" style="letter-spacing: 0.5px;">
                                            {{ $admin->username }}
                                        </td>
                                        <td class="py-3 text-left">
                                            <i class="far fa-envelope mr-1 text-muted"></i> {{ $admin->email }}
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <a href="{{ route('admin.admin.edit_form', $admin->id) }}" class="btn btn-sm shadow-sm px-3 mr-2" style="border-radius: 10px; background: #fff4e5; color: #d97706; border: 1px solid #fbbf24; font-weight: 600; white-space: nowrap;">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                                <button class="btn btn-sm shadow-sm px-3 deleteAdminBtn" data-id="{{ $admin->id }}" style="border-radius: 10px; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-weight: 600; white-space: nowrap;">
                                                    <i class="fas fa-trash mr-1"></i> Hapus
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
</section>



<script src="{{ asset('js/admin/kelola_admin.js') }}"></script>

@endsection
