@extends('layouts.master')

@section('title', 'Users Management')

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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Level</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                            <tr id="user-{{ $user->user_id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->level->name }}</td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete-user" data-id="{{ $user->user_id }}" title="Delete">
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <span class="invalid-feedback" role="alert" id="name-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <span class="invalid-feedback" role="alert" id="email-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="invalid-feedback" role="alert" id="password-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="level_id">Level</label>
                        <select class="form-control" id="level_id" name="level_id" required>
                            <option value="">Select Level</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->level_id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" role="alert" id="level_id-error"></span>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    let table = $('#usersTable').DataTable({
        responsive: true,
        autoWidth: false,
        order: [[0, 'asc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
        }
    });

    // Add User Form Submit
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        
        // Reset previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: '{{ route("admin.users.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    // Add new row to DataTable
                    let newRow = table.row.add([
                        table.rows().count() + 1,
                        response.user.name,
                        response.user.email,
                        response.user.level.name,
                        response.user.created_at,
                        `<a href="${'{{ route("admin.users.edit", ":id") }}'.replace(':id', response.user.user_id)}" class="btn btn-info btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm delete-user" data-id="${response.user.user_id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>`
                    ]).draw().node();
                    
                    // Add row ID
                    $(newRow).attr('id', 'user-' + response.user.user_id);
                    
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
});
</script>
@endpush 