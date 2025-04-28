@extends('layouts.master')

@section('title', 'Levels Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Levels Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addLevelModal">
                            <i class="fas fa-plus"></i> Add New Level
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="alert-container"></div>
                    <table id="levelsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($levels as $index => $level)
                            <tr id="level-{{ $level->level_id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $level->level_kode }}</td>
                                <td>{{ $level->name }}</td>
                                <td>{{ $level->description }}</td>
                                <td>
                                    <a href="{{ route('admin.levels.edit', $level->level_id) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete-level" data-id="{{ $level->level_id }}" title="Delete">
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

<!-- Add Level Modal -->
<div class="modal fade" id="addLevelModal" tabindex="-1" role="dialog" aria-labelledby="addLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLevelModalLabel">Add New Level</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addLevelForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="level_kode">Level Code</label>
                        <input type="text" class="form-control" id="level_kode" name="level_kode" required>
                        <span class="invalid-feedback" role="alert" id="level_kode-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <span class="invalid-feedback" role="alert" id="name-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <span class="invalid-feedback" role="alert" id="description-error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Level</button>
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
    let table = $('#levelsTable').DataTable({
        responsive: true,
        autoWidth: false,
        order: [[0, 'asc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
        }
    });

    // Add Level Form Submit
    $('#addLevelForm').on('submit', function(e) {
        e.preventDefault();
        
        // Reset previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: '{{ route("admin.levels.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    // Add new row to DataTable
                    let newRow = table.row.add([
                        table.rows().count() + 1,
                        response.level.level_kode,
                        response.level.name,
                        response.level.description,
                        `<a href="/admin/levels/${response.level.level_id}/edit" class="btn btn-info btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm delete-level" data-id="${response.level.level_id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>`
                    ]).draw().node();
                    
                    // Add row ID
                    $(newRow).attr('id', 'level-' + response.level.level_id);
                    
                    // Show success message
                    $('#alert-container').html(
                        `<div class="alert alert-success alert-dismissible fade show">
                            ${response.message}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>`
                    );
                    
                    // Reset form and close modal
                    $('#addLevelForm')[0].reset();
                    $('#addLevelModal').modal('hide');
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
                            An error occurred while creating the level.
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>`
                    );
                }
            }
        });
    });

    // Delete Level
    $(document).on('click', '.delete-level', function() {
        let levelId = $(this).data('id');
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
                    url: `/admin/levels/${levelId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.success) {
                            table.row($('#level-' + levelId)).remove().draw();
                            Swal.fire(
                                'Deleted!',
                                'Level has been deleted.',
                                'success'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'Error deleting level.',
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