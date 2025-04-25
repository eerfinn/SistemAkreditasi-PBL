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
                        <a href="/admin/levels/create" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Level
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <table id="levelsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($levels as $index => $level)
                            <tr id="level-{{ $level->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $level->name }}</td>
                                <td>{{ $level->description }}</td>
                                <td>
                                    <span class="badge badge-{{ $level->status ? 'success' : 'danger' }}">
                                        {{ $level->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $level->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="/admin/levels/{{ $level->id }}/edit" class="btn btn-info btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete-level" data-id="{{ $level->id }}" title="Delete">
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#levelsTable').DataTable({
        responsive: true,
        autoWidth: false,
        order: [[0, 'asc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
        }
    });

    // Delete Level
    $('.delete-level').click(function() {
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
                            $('#level-' + levelId).remove();
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