@extends('layouts.master')

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
                    <h4>Add New User</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                            @error('nama')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="administrator" {{ old('role') == 'administrator' ? 'selected' : '' }}>Administrator</option>
                                <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="koordinator" {{ old('role') == 'koordinator' ? 'selected' : '' }}>Koordinator</option>
                                <option value="kjm" {{ old('role') == 'kjm' ? 'selected' : '' }}>KJM</option>
                                <option value="kaprodi" {{ old('role') == 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                                <option value="kajur" {{ old('role') == 'kajur' ? 'selected' : '' }}>Kajur</option>
                                <option value="kps" {{ old('role') == 'kps' ? 'selected' : '' }}>KPS</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 kriteria-access-section" style="display: none;">
                            <label for="kriteria_access" class="form-label">Kriteria Access</label>
                            <div class="alert alert-info">
                                Select which kriteria this user can access.
                            </div>
                            <select class="form-control select2" id="kriteria_access" name="kriteria_access[]" multiple>
                                @foreach($kriteria as $k)
                                    <option value="{{ $k->id }}">
                                        {{ $k->nama_kriteria }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kriteria_access')
                                <span class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();
        
        // Show/hide kriteria access based on role
        $('#role').change(function() {
            if ($(this).val() === 'dosen') {
                $('.kriteria-access-section').show();
            } else {
                $('.kriteria-access-section').hide();
            }
        });
    });
</script>
@endpush
@endsection 