@extends('layouts.master')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUserModal">
                            <i class="fas fa-plus"></i> Add New User
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="alert-container"></div>
                    <table id="userTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
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
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('user.edit', $user->id) }}" class="btn btn-info btn-sm" title="Edit">
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
                            <option value="kaprodi">Ketua Program Studi</option>
                            <option value="kajur">Ketua Jurusan</option>
                        </select>
                        <span class="invalid-feedback" role="alert" id="role-error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                          <option value="kaprodi">Ketua Program Studi</option>
                          <option value="kajur">Ketua Jurusan</option>
                      </select>
                      <span class="invalid-feedback" role="alert" id="edit-role-error"></span>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Update User</button>
              </div>
          </form>
      </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    let table = $('#userTable').DataTable({
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

    // Reset form and error state when modal is shown
    $('#addUserModal').on('show.bs.modal', function () {
        $('#addUserForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });
    $('#editUserModal').on('show.bs.modal', function () {
        $('#editUserForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });

    // Remove modal-open and backdrop on modal hidden
    $('#addUserModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });
    $('#editUserModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });

    // Hapus backdrop saat modal benar-benar sudah tampil
    $('#addUserModal').on('shown.bs.modal', function () {
        $('.modal-backdrop').remove();
        setTimeout(function() {
            $('.modal-backdrop').remove();
        }, 100);
    });
    $('#editUserModal').on('shown.bs.modal', function () {
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
            url: '{{ route("user.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                console.log('Success:', response);
                if(response.success) {
                    // Add new row to DataTable
                    let newRow = table.row.add([
                        table.rows().count() + 1,
                        response.user.nama,
                        response.user.username,
                        response.user.role.charAt(0).toUpperCase() + response.user.role.slice(1),
                        response.user.created_at,
                        `<a href="${'{{ route("user.edit", ":id") }}'.replace(':id', response.user.id)}" class="btn btn-info btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm delete-user" data-id="${response.user.id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>`
                    ]).draw().node();

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
                    url: '{{ route("user.destroy", ":id") }}'.replace(':id', userId),
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
            $('#edit-password').val('');
            $('#edit-role').val(user.role);
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#editUserModal').modal('show');
        });
    });

    // Handle Edit User Form Submit
    $('#editUserForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        let userId = $('#edit-id').val();
        let url = '{{ route("user.update", ":id") }}'.replace(':id', userId);
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
});
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