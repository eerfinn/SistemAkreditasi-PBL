@extends('layouts.master')

@section('title', 'Edit User')

@section('css')
<link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-selection--multiple {
        border: 1px solid #ddd !important;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Edit User</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="edit-nama">Nama</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="edit-nama" name="nama" value="{{ old('nama', $user->nama) }}" required>
                            @error('nama')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="edit-username">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="edit-username" name="username" value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="edit-email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="edit-email" name="email" value="{{ old('email', $user->email) }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="edit-password">Password (Kosongkan jika tidak ingin diubah)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="edit-password" name="password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="edit-role">Role</label>
                            <select class="form-control @error('role') is-invalid @enderror" id="edit-role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="administrator" {{ old('role', $user->role) == 'administrator' ? 'selected' : '' }}>Administrator</option>
                                <option value="direktur" {{ old('role', $user->role) == 'direktur' ? 'selected' : '' }}>Direktur</option>
                                <option value="dosen" {{ old('role', $user->role) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="koordinator" {{ old('role', $user->role) == 'koordinator' ? 'selected' : '' }}>Koordinator</option>
                                <option value="kjm" {{ old('role', $user->role) == 'kjm' ? 'selected' : '' }}>KJM</option>
                                <option value="kaprodi" {{ old('role', $user->role) == 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                                <option value="kajur" {{ old('role', $user->role) == 'kajur' ? 'selected' : '' }}>Kajur</option>
                                <option value="kps" {{ old('role', $user->role) == 'kps' ? 'selected' : '' }}>KPS</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        @if($user->role === 'dosen')
                        <div class="mb-3">
                            <label>Kriteria Access</label>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Kriteria access untuk dosen dikelola melalui fitur "Manage Dosen Criteria Access" pada halaman utama pengguna.
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
@endpush
@endsection 