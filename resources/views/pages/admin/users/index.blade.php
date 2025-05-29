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
            overflow: hidden;
            height: auto !important;
            min-height: 38px !important;
            border: 1px solid #ddd !important;
            padding: 3px !important;
        }
        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            margin-right: 3px;
            margin-bottom: 3px;
        }
        .badge-info {
            color: #fff;
            background-color: #17a2b8;
        }
        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }
        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
        .badge-secondary {
            color: #fff;
            background-color: #6c757d;
        }
        /* Tooltip styles */
        .tooltip-inner {
            max-width: 300px;
            padding: 10px;
            text-align: left;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            background-color: #fff;
            color: #333;
            border: 1px solid #ddd;
        }
        .tooltip-kriteria-list {
            padding: 0;
        }
        .tooltip-header {
            margin-top: 0;
            margin-bottom: 8px;
            font-weight: bold;
            color: #17a2b8;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .tooltip-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        .tooltip-list li {
            padding: 3px 0;
            font-size: 13px;
        }
        .tooltip.show {
            opacity: 1;
        }
        .btn-outline-info {
            color: #17a2b8;
            border-color: #17a2b8;
            padding: 2px 6px;
            font-size: 12px;
        }
        .btn-xs {
            padding: 1px 5px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }
        .ml-2 {
            margin-left: 0.5rem !important;
        }
        /* Checkbox styles for kriteria selection */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #17a2b8;
            color: #fff;
            border: none;
            padding: 3px 8px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            margin-right: 5px;
        }
        .kriteria-list-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #eee;
            padding: 10px;
            border-radius: 4px;
        }
        .custom-control {
            margin-bottom: 8px;
            padding-left: 30px;
        }
        .custom-checkbox .custom-control-label::before {
            border-radius: 3px;
            border-color: #17a2b8;
        }
        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26l2.974 2.99L8 2.193z'/%3e%3c/svg%3e");
        }
        /* Dropdown styles for kriteria selection */
        .kriteria-dropdown {
            width: 280px;
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .kriteria-list {
            max-height: 250px;
            overflow-y: auto;
            margin-bottom: 10px;
            padding-right: 5px;
        }
        .dropdown-header {
            font-weight: bold;
            color: #17a2b8;
            padding: 8px;
            border-bottom: 1px solid #eee;
            margin-bottom: 8px;
        }
        .dropdown-toggle::after {
            display: none;
        }
        .kriteria-form .custom-control {
            margin-bottom: 6px;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUserModal">
                            <i class="fas fa-plus"></i> Add New User
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="alert-container"></div>
                    <table id="usersTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Kriteria Access</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                            <tr id="user-{{ $user->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->nama }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>
                                    @if($user->role === 'administrator' || $user->role !== 'dosen')
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-primary">All Access</span>
                                        </div>
                                    @elseif(!empty($user->kriteria_access))
                                        @php
                                            $kriteriaIds = $user->kriteria_access;
                                            $kriteriaCount = count($kriteriaIds);
                                            $kriteriaList = App\Models\Kriteria::whereIn('id', $kriteriaIds)->get();
                                            $allKriteria = App\Models\Kriteria::all();
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success mr-2">{{ $kriteriaCount }}</span>
                                            <div class="dropdown">
                                                <button class="btn btn-xs btn-info dropdown-toggle" type="button" id="dropdownMenuButton-{{ $user->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-cog"></i>
                                                </button>
                                                <div class="dropdown-menu kriteria-dropdown p-2" aria-labelledby="dropdownMenuButton-{{ $user->id }}">
                                                    <h6 class="dropdown-header">Kriteria Access for {{ $user->nama }}</h6>
                                                    <form class="px-2 py-1 kriteria-form" data-user-id="{{ $user->id }}">
                                                        <div class="kriteria-list">
                                                            @foreach($allKriteria as $kriteria)
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input kriteria-checkbox" 
                                                                    id="kriteria-{{ $user->id }}-{{ $kriteria->id }}" 
                                                                    name="kriteria_access[]" 
                                                                    value="{{ $kriteria->id }}"
                                                                    {{ in_array($kriteria->id, $kriteriaIds) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="kriteria-{{ $user->id }}-{{ $kriteria->id }}">
                                                                    {{ $kriteria->nama_kriteria }}
                                                                </label>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="dropdown-divider"></div>
                                                        <button type="submit" class="btn btn-primary btn-sm btn-block">Save</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            $allKriteria = App\Models\Kriteria::all();
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-warning mr-2">No access</span>
                                            <div class="dropdown">
                                                <button class="btn btn-xs btn-info dropdown-toggle" type="button" id="dropdownMenuButton-{{ $user->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                <div class="dropdown-menu kriteria-dropdown p-2" aria-labelledby="dropdownMenuButton-{{ $user->id }}">
                                                    <h6 class="dropdown-header">Kriteria Access for {{ $user->nama }}</h6>
                                                    <form class="px-2 py-1 kriteria-form" data-user-id="{{ $user->id }}">
                                                        <div class="kriteria-list">
                                                            @foreach($allKriteria as $kriteria)
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input kriteria-checkbox" 
                                                                    id="kriteria-{{ $user->id }}-{{ $kriteria->id }}" 
                                                                    name="kriteria_access[]" 
                                                                    value="{{ $kriteria->id }}">
                                                                <label class="custom-control-label" for="kriteria-{{ $user->id }}-{{ $kriteria->id }}">
                                                                    {{ $kriteria->nama_kriteria }}
                                                                </label>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="dropdown-divider"></div>
                                                        <button type="submit" class="btn btn-primary btn-sm btn-block">Save</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete-user" data-id="{{ $user->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <!-- Tombol close (X) dihapus sesuai permintaan -->
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                        <span class="invalid-feedback" role="alert" id="nama-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <span class="invalid-feedback" role="alert" id="username-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <span class="invalid-feedback" role="alert" id="email-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="invalid-feedback" role="alert" id="password-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="administrator">Administrator</option>
                            <option value="dosen">Dosen</option>
                            <option value="koordinator">Koordinator</option>
                            <option value="kjm">KJM</option>
                            <option value="kaprodi">Kaprodi</option>
                            <option value="kajur">Kajur</option>
                        </select>
                        <span class="invalid-feedback" role="alert" id="role-error"></span>
                    </div>

                    <div id="kriteria-access-container" style="display: none;">
                        <div class="form-group">
                            <label>Kriteria Access</label>
                            <select class="form-control select2" id="kriteria-access" name="kriteria_access[]" multiple>
                                @foreach(App\Models\Kriteria::all() as $kriteria)
                                <option value="{{ $kriteria->id }}">
                                    {{ $kriteria->nama_kriteria }}
                                </option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback" role="alert" id="kriteria_access-error"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
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
              <!-- Tombol X dihapus, hanya tombol close teks yang berfungsi -->
          </div>
          <form id="editUserForm">
              @csrf
              @method('PUT')
              <input type="hidden" id="edit-id" name="id">
              <div class="modal-body">
                  <div class="form-group">
                      <label for="edit-nama">Nama</label>
                      <input type="text" class="form-control" id="edit-nama" name="nama" required>
                      <span class="invalid-feedback" role="alert" id="edit-nama-error"></span>
                  </div>
                  <div class="form-group">
                      <label for="edit-username">Username</label>
                      <input type="text" class="form-control" id="edit-username" name="username" required>
                      <span class="invalid-feedback" role="alert" id="edit-username-error"></span>
                  </div>
                  <div class="form-group">
                      <label for="edit-email">Email</label>
                      <input type="email" class="form-control" id="edit-email" name="email">
                      <span class="invalid-feedback" role="alert" id="edit-email-error"></span>
                  </div>
                  <div class="form-group">
                      <label for="edit-password">Password (Kosongkan jika tidak ingin diubah)</label>
                      <input type="password" class="form-control" id="edit-password" name="password">
                      <span class="invalid-feedback" role="alert" id="edit-password-error"></span>
                  </div>
                  <div class="form-group">
                      <label for="edit-role">Role</label>
                      <select class="form-control" id="edit-role" name="role" required>
                          <option value="">Select Role</option>
                          <option value="administrator">Administrator</option>
                          <option value="dosen">Dosen</option>
                          <option value="koordinator">Koordinator</option>
                          <option value="kjm">KJM</option>
                          <option value="kaprodi">Kaprodi</option>
                          <option value="kajur">Kajur</option>
                      </select>
                      <span class="invalid-feedback" role="alert" id="edit-role-error"></span>
                  </div>

                  <div id="edit-kriteria-access-container" style="display: none;">
                      <div class="form-group">
                          <label>Kriteria Access</label>
                          <select class="form-control select2" id="edit-kriteria-access" name="kriteria_access[]" multiple>
                              @foreach(App\Models\Kriteria::all() as $kriteria)
                              <option value="{{ $kriteria->id }}">
                                  {{ $kriteria->nama_kriteria }}
                              </option>
                              @endforeach
                          </select>
                          <span class="invalid-feedback" role="alert" id="edit-kriteria_access-error"></span>
                      </div>
                  </div>
              </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Update User</button>
              </div>
          </form>
      </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'click hover',
        placement: 'right',
        boundary: 'window',
        template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
    });
    
    // Initialize DataTable
    let table = $('#usersTable').DataTable({
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

    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select kriteria",
        allowClear: true,
        width: '100%'
    });

    // Toggle kriteria access container based on role selection - now for all roles
    $('#role').change(function() {
        // Show kriteria access for all roles
        $('#kriteria-access-container').show();
    });

    // Toggle edit kriteria access container based on role selection - now for all roles
    $('#edit-role').change(function() {
        // Show kriteria access for all roles
        $('#edit-kriteria-access-container').show();
    });

    // Reset form and error state when modal is shown
    $('#addUserModal').on('show.bs.modal', function () {
        $('#addUserForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Show kriteria access container by default
        $('#kriteria-access-container').show();
        
        // Reinitialize Select2
        $('#kriteria-access').val(null).trigger('change');
        $('#kriteria-access').select2({
            placeholder: "Select kriteria",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addUserModal')
        });
    });
    
    $('#editUserModal').on('show.bs.modal', function () {
        $('#editUserForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Show kriteria access container by default
        $('#edit-kriteria-access-container').show();
        
        // Reinitialize Select2
        $('#edit-kriteria-access').val(null).trigger('change');
        $('#edit-kriteria-access').select2({
            placeholder: "Select kriteria",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#editUserModal')
        });
    });
    
    // Remove modal-open and backdrop on modal hidden
    $('#addUserModal, #editUserModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });
    
    // Hapus backdrop saat modal benar-benar sudah tampil
    $('#addUserModal, #editUserModal').on('shown.bs.modal', function () {
        $('.modal-backdrop').remove();
        setTimeout(function() {
            $('.modal-backdrop').remove();
        }, 100);
    });

    // Add User Form Submit
    $('#addUserForm').off('submit').on('submit', function(e) {
        e.preventDefault();

        // Reset previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{ route("admin.users.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                console.log('Success:', response);
                if(response.success) {
                    // For the action buttons
                    let actionsHtml = `<a href="${'{{ route("admin.users.edit", ":id") }}'.replace(':id', response.user.id)}" class="btn btn-info btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-danger btn-sm delete-user" data-id="${response.user.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>`;
                    
                    // Add new row to DataTable
                    let newRow = table.row.add([
                        table.rows().count() + 1,
                        response.user.nama,
                        response.user.username,
                        response.user.role.charAt(0).toUpperCase() + response.user.role.slice(1),
                        getKriteriaAccessHtml(response.user, response.allKriteria),
                        response.user.created_at,
                        actionsHtml
                    ]).draw().node();

                    // Reinitialize tooltips for new elements
                    $('[data-toggle="tooltip"]').tooltip();

                    // Add row ID
                    $(newRow).attr('id', 'user-' + response.user.id);

                    // Show success message
                    $('#alert-container').html(
                        `<div class="alert alert-success alert-dismissible fade show">
                            ${response.message}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>`
                    );

                    // Reset form and close modal
                    $('#addUserForm')[0].reset();
                    $('#addUserModal').modal('hide');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}-error`).text(errors[key][0]);
                    });
                } else {
                    $('#alert-container').html(
                        `<div class="alert alert-danger alert-dismissible fade show">
                            An error occurred while creating the user.
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>`
                    );
                }
                $('#addUserModal').modal('hide');
            }
        });
    });

    // Delete User
    $(document).on('click', '.delete-user', function() {
        let userId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.users.destroy", ":id") }}'.replace(':id', userId),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.success) {
                            table.row($('#user-' + userId)).remove().draw();
                            Swal.fire(
                                'Deleted!',
                                'User has been deleted.',
                                'success'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'Error deleting user.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Handle Edit Button Click
    $(document).on('click', '.btn-info.btn-sm', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        $.get(url.replace('/edit', '/json'), function(user) {
            $('#edit-id').val(user.id);
            $('#edit-nama').val(user.nama);
            $('#edit-username').val(user.username);
            $('#edit-email').val(user.email);
            $('#edit-password').val('');
            $('#edit-role').val(user.role);
            
            // Show kriteria access section for all users
            $('#edit-kriteria-access-container').show();
            
            // Clear previous selections
            $('#edit-kriteria-access').val(null).trigger('change');
            
            // Set selected kriteria
            if (user.kriteria_access && user.kriteria_access.length > 0) {
                $('#edit-kriteria-access').val(user.kriteria_access).trigger('change');
            }
            
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#editUserModal').modal('show');
        });
    });

    // Handle Edit User Form Submit
    $('#editUserForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        let userId = $('#edit-id').val();
        let url = '{{ route("admin.users.update", ":id") }}'.replace(':id', userId);
        let data = $(this).serialize();
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                if(response.success) {
                    // Update row in DataTable
                    let row = $('#user-' + userId);
                    row.find('td').eq(1).text(response.user.nama);
                    row.find('td').eq(2).text(response.user.username);
                    row.find('td').eq(3).text(response.user.role.charAt(0).toUpperCase() + response.user.role.slice(1));
                    
                    // Update kriteria access column
                    if (response.user.kriteria_access && response.user.kriteria_access.length > 0) {
                        // Fetch kriteria names for the updated access
                        $.ajax({
                            url: '/admin/kriteria/names', // We'll create this endpoint
                            type: 'GET',
                            data: {
                                ids: response.user.kriteria_access
                            },
                            success: function(kriteriaData) {
                                let badgesHtml = '<div class="d-flex align-items-center">';
                                badgesHtml += `<span class="badge badge-success mr-2">${kriteriaData.length}</span>`;
                                badgesHtml += `<div class="kriteria-tooltip position-relative" data-toggle="tooltip" data-html="true" title="
                                    <div class='tooltip-kriteria-list'>
                                        <h6 class='tooltip-header'>Assigned Kriteria</h6>
                                        <ul class='tooltip-list'>`;
                                
                                kriteriaData.forEach(function(k) {
                                    badgesHtml += `<li><i class='fa fa-check text-success'></i> ${k.nama_kriteria}</li>`;
                                });
                                
                                badgesHtml += `</ul></div>"><button class="btn btn-xs btn-outline-info">Show</button></div>`;
                                badgesHtml += `<button class="btn btn-xs btn-primary ml-2 manage-kriteria" data-id="${userId}" data-name="${response.user.nama}">
                                    <i class="fa fa-edit"></i> Manage
                                </button>`;
                                badgesHtml += '</div>';
                                row.find('td').eq(4).html(badgesHtml);
                                
                                // Reinitialize tooltips
                                $('[data-toggle="tooltip"]').tooltip();
                            },
                            error: function() {
                                row.find('td').eq(4).html('<span class="badge badge-info">Updated - Refresh to see details</span>');
                            }
                        });
                    } else {
                        row.find('td').eq(4).html(`
                            <div class="d-flex align-items-center">
                                <span class="badge badge-warning mr-2">No access</span>
                                <button class="btn btn-xs btn-primary manage-kriteria" data-id="${userId}" data-name="${response.user.nama}">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </div>
                        `);
                    }
                    
                    $('#editUserModal').modal('hide');
                    $('#alert-container').html(
                        `<div class="alert alert-success alert-dismissible fade show">
                            ${response.message}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>`
                    );
                }
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        $(`#edit-${key}`).addClass('is-invalid');
                        $(`#edit-${key}-error`).text(errors[key][0]);
                    });
                } else {
                    $('#alert-container').html(
                        `<div class="alert alert-danger alert-dismissible fade show">
                            An error occurred while updating the user.
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>`
                    );
                }
            }
        });
    });

    // Handle kriteria form submission from dropdown
    $(document).on('submit', '.kriteria-form', function(e) {
        e.preventDefault();
        let form = $(this);
        let userId = form.data('user-id');
        let url = '{{ route("admin.users.updateKriteriaAccess", ":id") }}'.replace(':id', userId);
        
        // Get selected kriteria IDs from checkboxes
        let selectedKriteria = [];
        form.find('.kriteria-checkbox:checked').each(function() {
            selectedKriteria.push($(this).val());
        });
        
        // Create form data with the selected kriteria
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        selectedKriteria.forEach(function(kriteriaId) {
            formData.append('kriteria_access[]', kriteriaId);
        });
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    // Update the user's row in the table
                    let row = $('#user-' + userId);
                    let role = row.find('td').eq(3).text().trim().toLowerCase();
                    
                    // If it's admin or non-dosen role, show all access badge
                    if (role === 'administrator' || role !== 'dosen') {
                        row.find('td').eq(4).html('<div class="d-flex align-items-center"><span class="badge badge-primary">All Access</span></div>');
                    } else {
                        // For dosen, update the display based on selected kriteria
                        if (selectedKriteria.length > 0) {
                            // With kriteria access
                            let allKriteria = [];
                            $('.kriteria-form[data-user-id="' + userId + '"] .kriteria-checkbox').each(function() {
                                allKriteria.push({
                                    id: $(this).val(),
                                    nama_kriteria: $(this).next('label').text().trim()
                                });
                            });
                            
                            row.find('td').eq(4).html(getKriteriaAccessHtml({
                                id: userId,
                                nama: row.find('td').eq(1).text().trim(),
                                kriteria_access: selectedKriteria,
                                role: role
                            }, allKriteria));
                        } else {
                            // No kriteria access
                            row.find('td').eq(4).html(getKriteriaAccessHtml({
                                id: userId,
                                nama: row.find('td').eq(1).text().trim(),
                                kriteria_access: [],
                                role: role
                            }, []));
                        }
                    }
                    
                    // Show success message
                    $('#alert-container').html(`
                        <div class="alert alert-success alert-dismissible fade show">
                            Kriteria access updated successfully
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Error updating kriteria access:', xhr);
                $('#alert-container').html(`
                    <div class="alert alert-danger alert-dismissible fade show">
                        An error occurred while updating kriteria access.
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                `);
            }
        });
    });
    
    // Prevent dropdown from closing when clicking inside it
    $(document).on('click', '.kriteria-dropdown', function(e) {
        e.stopPropagation();
    });
});

// Helper function to generate kriteria access HTML
function getKriteriaAccessHtml(user, allKriteria) {
    // Admins and non-dosen roles have all access
    if (user.role === 'administrator' || user.role !== 'dosen') {
        return '<div class="d-flex align-items-center"><span class="badge badge-primary">All Access</span></div>';
    } 
    // Only dosen need specific kriteria management
    else if (user.kriteria_access && user.kriteria_access.length > 0) {
        // For users with kriteria access
        let html = `<div class="d-flex align-items-center">
            <span class="badge badge-success mr-2">${user.kriteria_access.length}</span>
            <div class="dropdown">
                <button class="btn btn-xs btn-info dropdown-toggle" type="button" id="dropdownMenuButton-${user.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-cog"></i>
                </button>
                <div class="dropdown-menu kriteria-dropdown p-2" aria-labelledby="dropdownMenuButton-${user.id}">
                    <h6 class="dropdown-header">Kriteria Access for ${user.nama}</h6>
                    <form class="px-2 py-1 kriteria-form" data-user-id="${user.id}">
                        <div class="kriteria-list">`;
        
        // If we have the full kriteria list, use it; otherwise just show selected ones
        if (Array.isArray(allKriteria) && allKriteria.length > 0) {
            allKriteria.forEach(function(kriteria) {
                html += `
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input kriteria-checkbox" 
                        id="kriteria-${user.id}-${kriteria.id}" 
                        name="kriteria_access[]" 
                        value="${kriteria.id}"
                        ${user.kriteria_access.includes(kriteria.id.toString()) ? 'checked' : ''}>
                    <label class="custom-control-label" for="kriteria-${user.id}-${kriteria.id}">
                        ${kriteria.nama_kriteria}
                    </label>
                </div>`;
            });
        }
                        
        html += `</div>
                        <div class="dropdown-divider"></div>
                        <button type="submit" class="btn btn-primary btn-sm btn-block">Save</button>
                    </form>
                </div>
            </div>
        </div>`;
        return html;
    } else if (user.role === 'dosen') { // Only show "No access" for dosen
        return `<div class="d-flex align-items-center">
            <span class="badge badge-warning mr-2">No access</span>
            <div class="dropdown">
                <button class="btn btn-xs btn-info dropdown-toggle" type="button" id="dropdownMenuButton-${user.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-plus"></i>
                </button>
                <div class="dropdown-menu kriteria-dropdown p-2" aria-labelledby="dropdownMenuButton-${user.id}">
                    <h6 class="dropdown-header">Kriteria Access for ${user.nama}</h6>
                    <form class="px-2 py-1 kriteria-form" data-user-id="${user.id}">
                        <div class="kriteria-list">`;
        
        // If we have the full kriteria list, use it
        if (Array.isArray(allKriteria) && allKriteria.length > 0) {
            allKriteria.forEach(function(kriteria) {
                html += `
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input kriteria-checkbox" 
                        id="kriteria-${user.id}-${kriteria.id}" 
                        name="kriteria_access[]" 
                        value="${kriteria.id}">
                    <label class="custom-control-label" for="kriteria-${user.id}-${kriteria.id}">
                        ${kriteria.nama_kriteria}
                    </label>
                </div>`;
            });
        }
                
        html += `</div>
                        <div class="dropdown-divider"></div>
                        <button type="submit" class="btn btn-primary btn-sm btn-block">Save</button>
                    </form>
                </div>
            </div>
        </div>`;
        return html;
    } else {
        // Default all access for any other role
        return '<div class="d-flex align-items-center"><span class="badge badge-primary">All Access</span></div>';
    }
}
</script>
@endpush

@push('styles')
<style>
.modal-backdrop {
    z-index: 1040 !important;
}
.modal {
    z-index: 1051 !important;
}
</style>
@endpush 