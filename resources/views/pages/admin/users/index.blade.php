@extends('layouts.master')

@section('title', 'Users Management')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .select2-container {
            width: 100% !important;
        }
        .select2-selection--multiple {
            border: 1px solid #ddd !important;
        }
        .kriteria-badge {
            display: inline-block;
            margin: 2px;
            padding: 3px 8px;
            background-color: #e9f5fe;
            color: #2196f3;
            border-radius: 30px;
            font-size: 12px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .modal-xl {
            max-width: 1140px;
        }
        #dosenCriteriaEditor {
            transition: all 0.3s ease;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        #dosenCriteriaEditor .card {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e5e5;
        }
        #dosenCriteriaEditor .card-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
        }
        #dosenCriteriaEditor .card-body {
            padding: 20px;
        }
        #closeDosenEditor {
            cursor: pointer;
            transition: all 0.2s;
        }
        #closeDosenEditor:hover {
            transform: scale(1.1);
        }
        .edit-mode-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 10;
            display: none;
            border-radius: 8px;
        }
        .table-container {
            position: relative;
            margin-bottom: 20px;
        }
        .edit-dosen-kriteria:focus {
            box-shadow: none !important;
            outline: none !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #2196f3;
            color: white;
            border: none;
            padding: 3px 8px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
        }
        #dosenCriteriaTable, #usersTable {
            border-collapse: separate;
            border-spacing: 0;
        }
        #dosenCriteriaTable th, #usersTable th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 12px 15px;
        }
        #dosenCriteriaTable td, #usersTable td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
            border: 0.15em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e5e5e5;
            padding: 15px 20px;
        }
        .card-title {
            margin-bottom: 0;
            font-weight: 600;
            color: #333;
        }
        .card-body {
            padding: 20px;
        }
        .btn {
            border-radius: 5px;
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            transition: all 0.2s;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .btn-primary {
            background-color: #2196f3;
            border-color: #2196f3;
        }
        .btn-primary:hover {
            background-color: #0d87e9;
            border-color: #0d87e9;
        }
        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }
        .btn-info:hover {
            background-color: #138496;
            border-color: #138496;
            color: white;
        }
        .btn i {
            margin-right: 4px;
        }
        .badge {
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 30px;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .badge-primary {
            background-color: #2196f3;
        }
        .badge-info {
            background-color: #17a2b8;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #dee2e6;
            padding: 0.375rem 0.75rem;
        }
        .form-control:focus {
            border-color: #2196f3;
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        }
        .user-role {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 500;
        }
        .role-administrator {
            background-color: #ffefef;
            color: #dc3545;
        }
        .role-dosen {
            background-color: #e9f5fe;
            color: #2196f3;
        }
        .role-direktur {
            background-color: #e9f5fe;
            color: #2196f3;
        }
        .role-koordinator, .role-kjm, .role-kaprodi, .role-kajur, .role-kps {
            background-color: #e3f9f7;
            color: #17a2b8;
        }
        
        .table-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.85);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        
        .table-loader {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .loader-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2196f3;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 10px;
        }
        
        .loader-text {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Modal close button styling */
        .modal-header .close {
            padding: 1rem;
            margin: -1rem -1rem -1rem auto;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            background-color: transparent;
            border: 0;
        }
        
        .modal-header .close:hover {
            opacity: .75;
            color: #000;
            text-decoration: none;
        }
        
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Users Management</h4>
                    <div>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New User
                        </a>
                        <button type="button" class="btn btn-info btn-sm" id="manageCriteriaAccess">
                            <i class="fas fa-cog"></i> Manage Dosen Criteria Access
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="alert-container"></div>
                    <div class="table-responsive position-relative">
                        <div class="table-loading-overlay" id="usersTableLoading">
                            <div class="table-loader">
                                <div class="loader-spinner"></div>
                                <div class="loader-text">Loading users data...</div>
                            </div>
                        </div>
                        <table id="usersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama</th>
                                    <th width="15%">Username</th>
                                    <th width="20%">Email</th>
                                    <th width="10%">Role</th>
                                    <th width="15%">Created At</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                <tr id="user-{{ $user->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->nama }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email ?? '-' }}</td>
                                    <td>
                                        <span class="user-role role-{{ $user->role }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info btn-sm me-2 edit-user-link" data-id="{{ $user->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm delete-user" data-id="{{ $user->id }}" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                        <span class="invalid-feedback" role="alert" id="nama-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <span class="invalid-feedback" role="alert" id="username-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <span class="invalid-feedback" role="alert" id="email-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="invalid-feedback" role="alert" id="password-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="administrator">Administrator</option>
                            <option value="direktur">Direktur</option>
                            <option value="dosen">Dosen</option>
                            <option value="koordinator">Koordinator</option>
                            <option value="kjm">KJM</option>
                            <option value="kaprodi">Kaprodi</option>
                            <option value="kajur">Kajur</option>
                            <option value="kps">KPS</option>
                        </select>
                        <span class="invalid-feedback" role="alert" id="role-error"></span>
                    </div>

                    <div id="dosen-info" style="display: none;">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> Kriteria access untuk dosen dapat dikelola setelah user dibuat melalui fitur "Manage Dosen Criteria Access".
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="edit-nama" name="nama" required>
                        <span class="invalid-feedback" role="alert" id="edit-nama-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="edit-username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit-username" name="username" required>
                        <span class="invalid-feedback" role="alert" id="edit-username-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="edit-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email">
                        <span class="invalid-feedback" role="alert" id="edit-email-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="edit-password" class="form-label">Password (Kosongkan jika tidak ingin diubah)</label>
                        <input type="password" class="form-control" id="edit-password" name="password">
                        <span class="invalid-feedback" role="alert" id="edit-password-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="edit-role" class="form-label">Role</label>
                        <select class="form-control" id="edit-role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="administrator">Administrator</option>
                            <option value="direktur">Direktur</option>
                            <option value="dosen">Dosen</option>
                            <option value="koordinator">Koordinator</option>
                            <option value="kjm">KJM</option>
                            <option value="kaprodi">Kaprodi</option>
                            <option value="kajur">Kajur</option>
                            <option value="kps">KPS</option>
                        </select>
                        <span class="invalid-feedback" role="alert" id="edit-role-error"></span>
                    </div>

                    <div id="edit-dosen-info" style="display: none;">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> Kriteria access untuk dosen dikelola melalui tombol "Manage Dosen Criteria Access" pada halaman utama.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Criteria Access Modal -->
<div class="modal fade" id="manageCriteriaModal" tabindex="-1" role="dialog" aria-labelledby="manageCriteriaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageCriteriaModalLabel">Manage Dosen Criteria Access</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="criteria-alert-container"></div>
                
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i> Kelola akses kriteria untuk semua dosen. Gunakan tombol Edit untuk mengatur akses ke kriteria tertentu.
                </div>
                
                <div class="row mb-4" id="dosenCriteriaEditor" style="display: none;">
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0" id="editing-dosen-name">Edit Access for: <span></span></h5>
                                <button type="button" class="btn-close" id="closeDosenEditor" aria-label="Close"></button>
                            </div>
                            <div class="card-body">
                                <form id="editDosenCriteriaForm">
                                    @csrf
                                    <input type="hidden" id="edit-dosen-id" name="dosen_id">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="edit-dosen-kriteria" class="mb-2 form-label">Kriteria Access:</label>
                                                <div id="kriteria-select-loading" class="d-flex align-items-center mb-2" style="display: none !important;">
                                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <span class="text-muted small">Loading kriteria data...</span>
                                                </div>
                                                <select class="form-control select2-kriteria" id="edit-dosen-kriteria" name="kriteria_access[]" multiple>
                                                    @foreach(App\Models\Kriteria::all() as $kriteria)
                                                    <option value="{{ $kriteria->id }}">
                                                        {{ $kriteria->nama_kriteria }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <div class="form-text mt-2">
                                                    <i class="fas fa-info-circle text-info"></i> Pilih kriteria yang dapat diakses oleh dosen ini.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-12 d-flex justify-content-end">
                                            <button type="button" class="btn btn-secondary me-2" id="cancelDosenEdit">
                                                <i class="fas fa-times me-1"></i> Cancel
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i> Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-container">
                    <div class="edit-mode-overlay"></div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dosenCriteriaTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px">No</th>
                                    <th style="width: 250px">Nama Dosen</th>
                                    <th>Kriteria Access</th>
                                    <th style="width: 100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="dosenCriteriaTableBody">
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="loading-spinner"></div>
                                            <span>Loading dosen data...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
<script>
// Add Bootstrap modal compatibility layer
(function($) {
    // Check if we're using Bootstrap 4 or 5
    var isBootstrap5 = typeof bootstrap !== 'undefined' && typeof bootstrap.Modal !== 'undefined';
    
    // Create a compatibility layer for modals
    if (isBootstrap5) {
        // We're using Bootstrap 5, but the code expects Bootstrap 4 behavior
        // Add jQuery plugin method to handle both
        $.fn.modal = function(action) {
            if (action === 'show') {
                var modalEl = this[0];
                if (modalEl) {
                    var bsModal = new bootstrap.Modal(modalEl);
                    bsModal.show();
                }
            } else if (action === 'hide') {
                var modalEl = this[0];
                if (modalEl) {
                    var bsModal = bootstrap.Modal.getInstance(modalEl);
                    if (bsModal) {
                        bsModal.hide();
                    } else {
                        $(this).removeClass('show');
                        $(this).attr('aria-hidden', 'true');
                        $(this).css('display', 'none');
                    }
                }
            }
            return this;
        };
    }
})(jQuery);

$(document).ready(function() {
    // Sembunyikan loading overlay setelah halaman dimuat
    $('#usersTableLoading').fadeOut(300);
    initializeDataTable();
    
    // Initialize DataTable
    function initializeDataTable() {
        let userTable = $('#usersTable').DataTable({
            responsive: true,
            autoWidth: false,
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
    }

    // Initialize Select2
    initializeSelect2();
    
    // Show/hide kriteria access based on role in Add form
    $('#role').change(function() {
        if ($(this).val() === 'dosen') {
            $('#dosen-info').show();
        } else {
            $('#dosen-info').hide();
        }
    });
    
    // Show info message if user is dosen in Edit form
    $('#edit-role').change(function() {
        if ($(this).val() === 'dosen') {
            $('#edit-dosen-info').show();
        } else {
            $('#edit-dosen-info').hide();
        }
    });
    
    // When loading the edit modal, check if role is dosen
    var editUserModal = document.getElementById('editUserModal');
    if (editUserModal) {
        editUserModal.addEventListener('shown.bs.modal', function() {
            if ($('#edit-role').val() === 'dosen') {
                $('#edit-dosen-info').show();
            } else {
                $('#edit-dosen-info').hide();
            }
        });
    }
    
    // Handle edit user link click
    $(document).on('click', '.edit-user-link', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');
        handleEditUser(userId);
    });
    
    // Add User Form Submit
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        createUser();
    });
    
    // Edit User Form Submit
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        updateUser();
    });
    
    // Handle delete user
    $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        deleteUser(userId);
    });
    
    // Initialize manage criteria button
    $('#manageCriteriaAccess').on('click', function() {
        loadDosenCriteria();
    });
    
    // Handle edit dosen kriteria form submission
    $('#editDosenCriteriaForm').on('submit', function(e) {
        e.preventDefault();
        updateDosenKriteria();
    });
    
    // Handle clicking cancel edit button
    $('#cancelDosenEdit, #closeDosenEditor').on('click', function() {
        hideDosenEditor();
    });
    
    // Initialize Select2
    function initializeSelect2() {
        $('.select2').select2({
            placeholder: "Pilih kriteria",
            allowClear: true,
            width: '100%'
        });
        
        $('.select2-kriteria').select2({
            placeholder: "Pilih kriteria untuk dosen ini",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#dosenCriteriaEditor')
        });
    }
    
    // Function to handle edit user
    function handleEditUser(userId) {
        if (!userId) return;
        
        // Show loader
        showLoader('Loading...', 'Getting user data');
        
        // Get user data
        $.ajax({
            url: `/admin/users/${userId}/json`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                hideLoader();
                
                // Populate form fields
                $('#edit-id').val(response.id);
                $('#edit-nama').val(response.nama);
                $('#edit-username').val(response.username);
                $('#edit-email').val(response.email || '');
                $('#edit-role').val(response.role);
                
                // Show role-specific info
                if (response.role === 'dosen') {
                    $('#edit-dosen-info').show();
                } else {
                    $('#edit-dosen-info').hide();
                }
                
                // Show modal
                var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                editModal.show();
            },
            error: function(xhr, status, error) {
                showError('Error', 'Failed to get user data: ' + (xhr.responseJSON?.message || error));
            }
        });
    }
    
    // Function to create user
    function createUser() {
        // Show loader
        showLoader('Processing...', 'Creating new user');
        
        // Submit form
        $.ajax({
            url: '/admin/users',
            type: 'POST',
            data: $('#addUserForm').serialize(),
            dataType: 'json',
            success: function(response) {
                showSuccess('Success', 'User created successfully', function() {
                    $('#addUserModal').modal('hide');
                    window.location.reload();
                });
            },
            error: function(xhr, status, error) {
                hideLoader();
                
                if (xhr.status === 422) {
                    handleValidationErrors(xhr.responseJSON.errors, '');
                } else {
                    showError('Error', 'Failed to create user: ' + (xhr.responseJSON?.message || error));
                }
            }
        });
    }
    
    // Function to update user
    function updateUser() {
        const userId = $('#edit-id').val();
        const formData = $('#editUserForm').serialize();
        
        // Show loader
        showLoader('Processing...', 'Updating user data');
        
        // Submit form
        $.ajax({
            url: `/admin/users/${userId}`,
            type: 'PUT',
            data: formData,
            dataType: 'json',
            success: function(response) {
                showSuccess('Success', 'User updated successfully', function() {
                    $('#editUserModal').modal('hide');
                    window.location.reload();
                });
            },
            error: function(xhr, status, error) {
                hideLoader();
                
                if (xhr.status === 422) {
                    handleValidationErrors(xhr.responseJSON.errors, 'edit-');
                } else {
                    showError('Error', 'Failed to update user: ' + (xhr.responseJSON?.message || error));
                }
            }
        });
    }
    
    // Function to delete user
    function deleteUser(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                // Show loader
                showLoader('Processing...', 'Deleting user');
                
                // Delete user
                $.ajax({
                    url: `/admin/users/${userId}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        showSuccess('Success', 'User deleted successfully', function() {
                            // Remove row from table
                            $(`#user-${userId}`).remove();
                            // Reorder table numbers
                            reorderTableNumbers();
                        });
                    },
                    error: function(xhr, status, error) {
                        showError('Error', 'Failed to delete user: ' + (xhr.responseJSON?.message || error));
                    }
                });
            }
        });
    }
    
    // Function to load dosen criteria
    function loadDosenCriteria() {
        // Show loader
        showLoader('Loading...', 'Getting dosen data');
        
        // Get all dosen with their kriteria access
        $.ajax({
            url: '/admin/users',
            type: 'GET',
            data: { 
                role: 'dosen', 
                format: 'json' 
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(dosenList) {
                hideLoader();
                
                // Clear previous content
                $('#dosenCriteriaTableBody').empty();
                
                // Hide editor if visible
                hideDosenEditor();
                
                // If no dosen found
                if (dosenList.length === 0) {
                    $('#dosenCriteriaTableBody').html('<tr><td colspan="4" class="text-center">No dosen found</td></tr>');
                    var criteriaModal = new bootstrap.Modal(document.getElementById('manageCriteriaModal'));
                    criteriaModal.show();
                    return;
                }
                
                // Get all kriteria for reference
                $.ajax({
                    url: '/admin/kriteria/all',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(allKriteria) {
                        // Create a map of kriteria ID to name
                        const kriteriaMap = {};
                        allKriteria.forEach(k => {
                            kriteriaMap[k.id] = k.nama_kriteria;
                        });
                        
                        // Populate table with dosen data
                        dosenList.forEach((dosen, index) => {
                            let kriteriaHtml = '';
                            
                            // Display kriteria badges if any
                            if (dosen.kriteria_access && dosen.kriteria_access.length > 0) {
                                dosen.kriteria_access.forEach(kId => {
                                    if (kriteriaMap[kId]) {
                                        kriteriaHtml += `<span class="kriteria-badge">${kriteriaMap[kId]}</span>`;
                                    }
                                });
                            } else {
                                kriteriaHtml = '<span class="text-muted">No access assigned</span>';
                            }
                            
                            const row = `
                                <tr data-dosen-id="${dosen.id}">
                                    <td>${index + 1}</td>
                                    <td>${dosen.nama} <br><small class="text-muted">${dosen.username}</small></td>
                                    <td>${kriteriaHtml}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm edit-dosen-kriteria" 
                                                data-id="${dosen.id}" 
                                                data-nama="${dosen.nama}"
                                                data-kriteria='${JSON.stringify(dosen.kriteria_access || [])}'>
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            `;
                            
                            $('#dosenCriteriaTableBody').append(row);
                        });
                        
                        // Show modal
                        var criteriaModal = new bootstrap.Modal(document.getElementById('manageCriteriaModal'));
                        criteriaModal.show();
                        
                        // Handle edit dosen kriteria click
                        $('.edit-dosen-kriteria').off('click').on('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            const dosenId = $(this).data('id');
                            const dosenNama = $(this).data('nama');
                            const dosenKriteria = $(this).data('kriteria');
                            
                            // Populate edit form
                            $('#edit-dosen-id').val(dosenId);
                            $('#editing-dosen-name span').text(dosenNama);
                            
                            // Reset and set kriteria selection
                            if ($('#edit-dosen-kriteria').hasClass('select2-hidden-accessible')) {
                                $('#edit-dosen-kriteria').select2('destroy');
                            }
                            
                            // Initialize select2 with short delay to ensure proper rendering
                            setTimeout(function() {
                                $('.select2-kriteria').select2({
                                    placeholder: "Pilih kriteria untuk dosen ini",
                                    allowClear: true,
                                    width: '100%',
                                    dropdownParent: $('#dosenCriteriaEditor')
                                });
                                
                                $('#edit-dosen-kriteria').val(dosenKriteria).trigger('change');
                                
                                // Show editor section
                                showDosenEditor();
                            }, 300);
                        });
                    },
                    error: function(xhr, status, error) {
                        hideLoader();
                        console.error('Error getting kriteria data:', error);
                        showError('Error', 'Failed to get kriteria data: ' + (xhr.responseJSON?.message || error));
                    }
                });
            },
            error: function(xhr, status, error) {
                hideLoader();
                console.error('Error getting dosen data:', error);
                showError('Error', 'Failed to get dosen data: ' + (xhr.responseJSON?.message || error));
            }
        });
    }
    
    // Function to show dosen editor
    function showDosenEditor() {
        // Show the editor
        $('#dosenCriteriaEditor').slideDown(300);
        
        // Show the overlay to prevent interaction with the table
        $('.edit-mode-overlay').fadeIn(300);
        
        // Highlight the active row
        const dosenId = $('#edit-dosen-id').val();
        $(`#dosenCriteriaTableBody tr[data-dosen-id="${dosenId}"]`).addClass('table-primary');
        
        // Scroll to the editor
        $('html, body').animate({
            scrollTop: $('#dosenCriteriaEditor').offset().top - 100
        }, 300);
    }
    
    // Function to hide dosen editor
    function hideDosenEditor() {
        // Hide the editor with animation
        $('#dosenCriteriaEditor').slideUp(300, function() {
            // Reset the form after animation completes
            $('#editDosenCriteriaForm')[0].reset();
            if ($('#edit-dosen-kriteria').hasClass('select2-hidden-accessible')) {
                $('#edit-dosen-kriteria').val(null).trigger('change');
            }
        });
        
        // Hide the overlay
        $('.edit-mode-overlay').fadeOut(300);
        
        // Remove highlight from all rows
        $('#dosenCriteriaTableBody tr').removeClass('table-primary');
    }
    
    // Function to update dosen kriteria
    function updateDosenKriteria() {
        const dosenId = $('#edit-dosen-id').val();
        const kriteriaAccess = $('#edit-dosen-kriteria').val() || [];
        
        // Show loader
        showLoader('Processing...', 'Updating kriteria access');
        
        // Update kriteria access
        $.ajax({
            url: `/admin/users/${dosenId}/kriteria-access`,
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                kriteria_access: kriteriaAccess
            },
            dataType: 'json',
            success: function(response) {
                showSuccess('Success', 'Kriteria access updated successfully', function() {
                    // Hide the editor and refresh the table
                    hideDosenEditor();
                    loadDosenCriteria();
                });
            },
            error: function(xhr, status, error) {
                console.error('Error updating kriteria access:', error);
                showError('Error', 'Failed to update kriteria access: ' + (xhr.responseJSON?.message || error));
            }
        });
    }
    
    // Function to reorder table numbers after deletion
    function reorderTableNumbers() {
        $('#usersTable tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }
    
    // Helper function to show loader
    function showLoader(title, text) {
        Swal.fire({
            title: title,
            text: text,
            showConfirmButton: false,
            allowOutsideClick: false,
            onOpen: function() {
                Swal.showLoading();
            }
        });
    }
    
    // Helper function to hide loader
    function hideLoader() {
        Swal.close();
    }
    
    // Helper function to show success message
    function showSuccess(title, text, callback) {
        // Close all modals first
        $('.modal').each(function() {
            try {
                // Try Bootstrap 5 method first
                if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal !== 'undefined') {
                    const bsModal = bootstrap.Modal.getInstance(this);
                    if (bsModal) {
                        bsModal.hide();
                    } else {
                        $(this).modal('hide');
                    }
                } else {
                    // Bootstrap 4 method
                    $(this).modal('hide');
                }
            } catch (err) {
                // Fallback
                $(this).modal('hide');
                $(this).removeClass('show');
                $(this).attr('aria-hidden', 'true');
                $(this).css('display', 'none');
                $('.modal-backdrop').remove();
            }
        });
        
        // Remove any modal backdrop that might be left
        setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            
            // Then show the success message
            Swal.fire({
                title: title,
                text: text,
                type: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.value && typeof callback === 'function') {
                    callback();
                }
            });
        }, 300);
    }
    
    // Helper function to show error message
    function showError(title, text) {
        Swal.fire({
            title: title,
            text: text,
            type: 'error',
            confirmButtonText: 'OK'
        });
    }
    
    // Helper function to handle validation errors
    function handleValidationErrors(errors, prefix) {
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Display new errors
        for (const field in errors) {
            $(`#${prefix}${field}`).addClass('is-invalid');
            $(`#${prefix}${field}-error`).text(errors[field][0]);
        }
    }

    // Handle all types of close buttons (Bootstrap 4 and 5)
    $(document).on('click', '.btn-close, .close, [data-dismiss="modal"], [data-bs-dismiss="modal"]', function(e) {
        e.preventDefault();
        const $modal = $(this).closest('.modal');
        
        // Try both Bootstrap 4 and 5 methods
        if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal !== 'undefined') {
            // Bootstrap 5
            try {
                const bsModal = bootstrap.Modal.getInstance($modal[0]);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    $modal.modal('hide');
                }
            } catch (err) {
                // Fallback
                $modal.modal('hide');
            }
        } else {
            // Bootstrap 4
            $modal.modal('hide');
        }
        
        // Reset forms when modal is closed
        if ($modal.attr('id') === 'addUserModal') {
            $('#addUserForm')[0].reset();
            $('#dosen-info').hide();
        } else if ($modal.attr('id') === 'editUserModal') {
            $('#editUserForm')[0].reset();
            $('#edit-dosen-info').hide();
        } else if ($modal.attr('id') === 'manageCriteriaModal') {
            hideDosenEditor();
        }
    });

    // Handle modal closing through backdrop click
    $(document).on('click', '.modal', function(e) {
        if ($(e.target).hasClass('modal')) {
            $(this).modal('hide');
        }
    });

    // Add event listener for ESC key to close modals
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            $('.modal.show').modal('hide');
        }
    });
    
    // Handle closing manageCriteriaModal
    $('#manageCriteriaModal').on('hidden.bs.modal', function () {
        hideDosenEditor();
        $('#dosenCriteriaTableBody').empty();
    });
    
    // Handle close button in dosen criteria editor
    $('#closeDosenEditor, #cancelDosenEdit').off('click').on('click', function() {
        hideDosenEditor();
    });
    
    // Reset forms when modals are hidden
    $('#addUserModal').on('hidden.bs.modal', function () {
        $('#addUserForm')[0].reset();
        $('#dosen-info').hide();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });
    
    $('#editUserModal').on('hidden.bs.modal', function () {
        $('#editUserForm')[0].reset();
        $('#edit-dosen-info').hide();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });
});
</script>
@endpush 