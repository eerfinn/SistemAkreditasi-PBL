// Setup the Manage Criteria Modal
function setupManageCriteriaModal() {
    // First fetch all kriteria data
    $.get('/admin/kriteria/all', function(allKriteria) {
        // Create modal for criteria access management
        let modalHtml = `
        <div class="modal fade" id="manageCriteriaModal" tabindex="-1" role="dialog" aria-labelledby="manageCriteriaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="manageCriteriaModalLabel">Manage Criteria Access untuk Dosen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> Fitur ini hanya berlaku untuk pengguna dengan role dosen. Role lain secara otomatis memiliki akses ke semua kriteria.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Criteria Access</th>
                                    </tr>
                                </thead>
                                <tbody>`;
        
        // Add rows for each dosen user
        $('#usersTable tbody tr').each(function() {
            let userId = $(this).attr('id').replace('user-', '');
            let userName = $(this).find('td').eq(1).text();
            let role = $(this).find('td').eq(4).text().trim();
            
            if (role.toLowerCase() === 'dosen') {
                modalHtml += `
                    <tr>
                        <td>${userName}</td>
                        <td>${role}</td>
                        <td>
                            <div class="criteria-select-container">
                                <select class="form-control criteria-select" 
                                        data-user-id="${userId}" 
                                        multiple>`;
                
                // Add options for each kriteria
                if (Array.isArray(allKriteria)) {
                    allKriteria.forEach(function(kriteria) {
                        modalHtml += `<option value="${kriteria.id}">${kriteria.nama_kriteria}</option>`;
                    });
                }
                
                modalHtml += `
                                </select>
                            </div>
                        </td>
                    </tr>
                `;
            }
        });
        
        modalHtml += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveCriteriaAccess">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Add modal to page and show
        $('body').append(modalHtml);
        $('#manageCriteriaModal').modal('show');
        
        // Initialize select2 for each criteria select
        $('.criteria-select').select2({
            placeholder: "Select criteria access",
            width: '100%',
            dropdownParent: $('#manageCriteriaModal')
        });
        
        // Load current criteria access for each user
        $('.criteria-select').each(function() {
            let userId = $(this).data('user-id');
            
            // Fetch user data
            $.get(`/admin/users/${userId}/json`, function(user) {
                if (user.kriteria_access && user.kriteria_access.length > 0) {
                    $(`.criteria-select[data-user-id="${userId}"]`).val(user.kriteria_access).trigger('change');
                }
            });
        });
        
        // Handle save criteria access
        $('#saveCriteriaAccess').click(function() {
            let promises = [];
            
            $('.criteria-select').each(function() {
                let userId = $(this).data('user-id');
                let selectedCriteria = $(this).val() || [];
                
                // Create promise for each update request
                let promise = new Promise((resolve, reject) => {
                    $.ajax({
                        url: `/admin/users/${userId}/kriteria-access`,
                        method: 'PUT',
                        data: {
                            kriteria_access: selectedCriteria,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(xhr) {
                            reject(xhr);
                        }
                    });
                });
                
                promises.push(promise);
            });
            
            // Wait for all updates to complete
            Promise.all(promises)
                .then(() => {
                    $('#manageCriteriaModal').modal('hide');
                    $('#alert-container').html(
                        `<div class="alert alert-success alert-dismissible fade show">
                            Criteria access updated successfully for all users.
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>`
                    );
                    
                    // Clean up the modal
                    setTimeout(() => {
                        $('#manageCriteriaModal').remove();
                    }, 500);
                })
                .catch(() => {
                    $('#alert-container').html(
                        `<div class="alert alert-danger alert-dismissible fade show">
                            An error occurred while updating criteria access.
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>`
                    );
                });
        });
        
        // Clean up modal when hidden
        $('#manageCriteriaModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }).fail(function(xhr) {
        console.error('Failed to load kriteria data:', xhr);
        alert('Gagal memuat data kriteria. Silakan coba lagi.');
    });
}

// Submit Add User Form
function submitAddUserForm(table) {
    console.log('Add user form submitted');

    // Reset previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');

    // Show kriteria access container if role is dosen
    if ($('#role').val() === 'dosen') {
        console.log('Role is dosen, showing kriteria access container');
        $('#kriteria-access-container').show();
        
        // Debug selected kriteria
        console.log('Selected kriteria:', $('#kriteria-access').val());
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: '/admin/users',
        type: 'POST',
        data: $('#addUserForm').serialize(),
        success: function(response) {
            console.log('Success response:', response);
            if(response.success) {
                // For the action buttons
                let actionsHtml = `<a href="/admin/users/${response.user.id}/edit" class="btn btn-info btn-sm" data-id="${response.user.id}" title="Edit">
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
                    response.user.email,
                    response.user.role.charAt(0).toUpperCase() + response.user.role.slice(1),
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
            console.log('Error response:', xhr);
            if(xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(function(key) {
                    console.log('Validation error for ' + key + ':', errors[key][0]);
                    $(`#${key}`).addClass('is-invalid');
                    $(`#${key}-error`).text(errors[key][0]);
                });
                
                // Keep modal open when there are validation errors
                // Show kriteria access container if role is dosen
                if ($('#role').val() === 'dosen') {
                    console.log('Role is dosen (error case), showing kriteria access container');
                    $('#kriteria-access-container').show();
                    // Reinitialize Select2
                    setTimeout(function() {
                        console.log('Re-initializing Select2 after validation error');
                        if ($('#kriteria-access').hasClass('select2-hidden-accessible')) {
                            $('#kriteria-access').select2('destroy');
                        }
                        initializeSelect2('#kriteria-access', '#addUserModal');
                    }, 200);
                }
            } else {
                $('#alert-container').html(
                    `<div class="alert alert-danger alert-dismissible fade show">
                        An error occurred while creating the user.
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>`
                );
                $('#addUserModal').modal('hide');
            }
        }
    });
}

// Handle Delete User
function handleDeleteUser(button, table) {
    let userId = button.data('id');
    let row = button.closest('tr');
    let userName = row.find('td').eq(1).text().trim(); // Get user name
    let userRole = row.find('td').eq(4).text().trim(); // Get user role
    
    Swal.fire({
        title: 'Konfirmasi Hapus User',
        html: `Anda akan menghapus user <strong>${userName}</strong> (${userRole}).<br>Tindakan ini tidak dapat dibatalkan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/users/${userId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        table.row(row).remove().draw();
                        Swal.fire(
                            'Berhasil Dihapus!',
                            `User ${userName} telah berhasil dihapus.`,
                            'success'
                        );
                        
                        // Also show alert in the page
                        $('#alert-container').html(
                            `<div class="alert alert-success alert-dismissible fade show">
                                User ${userName} telah berhasil dihapus.
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>`
                        );
                    } else {
                        Swal.fire(
                            'Gagal!',
                            response.message || 'Gagal menghapus user.',
                            'error'
                        );
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Gagal menghapus user.';
                    
                    // Check if we have a specific error message from the server
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire(
                        'Error!',
                        errorMessage,
                        'error'
                    );
                    
                    // Also show alert in the page
                    $('#alert-container').html(
                        `<div class="alert alert-danger alert-dismissible fade show">
                            ${errorMessage}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>`
                    );
                }
            });
        }
    });
}

// Handle Edit User Click
function handleEditUserClick(button, explicitUserId) {
    // First try to use the explicitly provided ID if available
    let userId = explicitUserId;
    
    // If no explicit ID was provided, try various methods to extract it
    if (!userId) {
        console.log('No explicit user ID provided, trying to extract from button');
        
        // First try: check if button has data-id attribute directly
        if (button.data('id')) {
            userId = button.data('id');
            console.log('Found user ID from data-id attribute:', userId);
        } 
        // Second try: check if there is an href that contains the ID
        else if (button.attr('href')) {
            let url = button.attr('href');
            let matches = url.match(/\/users\/(\d+)/);
            if (matches && matches[1]) {
                userId = matches[1];
                console.log('Found user ID from href:', userId);
            }
        }
        
        // Third try: extract from closest tr row ID
        if (!userId) {
            let row = button.closest('tr');
            if (row.length && row.attr('id')) {
                userId = row.attr('id').replace('user-', '');
                console.log('Found user ID from row id:', userId);
            }
        }
    
        // Fourth try: try to get from any parent with data-id
        if (!userId) {
            let parent = button.parent('[data-id]');
            if (parent.length) {
                userId = parent.data('id');
                console.log('Found user ID from parent data-id:', userId);
            }
        }
        
        // Last resort: get the ID from any HTML attribute that might contain it
        if (!userId) {
            console.log('Button HTML:', button.prop('outerHTML'));
            // Try to extract ID from button HTML
            let buttonHtml = button.prop('outerHTML');
            let htmlMatches = buttonHtml.match(/users\/(\d+)/);
            if (htmlMatches && htmlMatches[1]) {
                userId = htmlMatches[1];
                console.log('Found user ID from HTML:', userId);
            }
        }
    } else {
        console.log('Using explicitly provided user ID:', userId);
    }
    
    if (!userId) {
        console.error('Could not determine user ID for edit');
        
        // Debug information
        console.log('Button:', button);
        console.log('Button parent:', button.parent());
        console.log('Button closest TR:', button.closest('tr'));
        
        alert('Tidak dapat menentukan ID pengguna. Silakan coba lagi.');
        return;
    }
    
    // Reset form first
    $('#editUserForm')[0].reset();
    
    // Load user data using ID
    let userDataUrl = `/admin/users/${userId}/json`;
    console.log('Loading user data from:', userDataUrl);
    
    // Load user data
    $.get(userDataUrl, function(user) {
        console.log('Loaded user data:', user);
        
        // Set form values
        $('#edit-id').val(user.id);
        $('#edit-nama').val(user.nama);
        $('#edit-username').val(user.username);
        $('#edit-email').val(user.email || '');
        $('#edit-password').val('');
        $('#edit-role').val(user.role);
        
        // Reset previous error states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Show modal first, let the shown.bs.modal event handle Select2 initialization
        $('#editUserModal').modal('show');
        
        // After modal is shown, handle kriteria access if role is dosen
        setTimeout(function() {
            if (user.role === 'dosen') {
                console.log('User is dosen, showing kriteria access container');
                $('#edit-kriteria-access-container').show();
                
                // Set selected kriteria after some delay to ensure Select2 is fully initialized
                setTimeout(function() {
                    if (user.kriteria_access && user.kriteria_access.length > 0) {
                        console.log('Setting kriteria_access:', user.kriteria_access);
                        $('#edit-kriteria-access').val(user.kriteria_access).trigger('change');
                    }
                }, 300);
            } else {
                $('#edit-kriteria-access-container').hide();
            }
        }, 300);
    }).fail(function(xhr) {
        console.error('Gagal memuat data user:', xhr);
        alert('Gagal memuat data user. Silakan coba lagi.');
    });
}

// Submit Edit User Form
function submitEditUserForm() {
    // Reset previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    
    // Show kriteria access container if role is dosen
    if ($('#edit-role').val() === 'dosen') {
        $('#edit-kriteria-access-container').show();
    }
    
    let userId = $('#edit-id').val();
    let url = `/admin/users/${userId}`;
    let data = $('#editUserForm').serialize();
    
    // Save original values for comparison
    let originalNama = $('#user-' + userId).find('td').eq(1).text().trim();
    let originalUsername = $('#user-' + userId).find('td').eq(2).text().trim();
    let originalEmail = $('#user-' + userId).find('td').eq(3).text().trim();
    let originalRole = $('#user-' + userId).find('td').eq(4).text().trim().toLowerCase();
    
    // New values
    let newNama = $('#edit-nama').val();
    let newUsername = $('#edit-username').val();
    let newEmail = $('#edit-email').val() || '-';
    let newRole = $('#edit-role').val();
    let hasPasswordChange = $('#edit-password').val().length > 0;
    
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
                row.find('td').eq(3).text(response.user.email || '-');
                row.find('td').eq(4).text(response.user.role.charAt(0).toUpperCase() + response.user.role.slice(1));
                
                // Create a list of changes made
                let changes = [];
                if (originalNama !== newNama) changes.push('nama');
                if (originalUsername !== newUsername) changes.push('username');
                if (originalEmail !== newEmail) changes.push('email');
                if (originalRole !== newRole) changes.push('role');
                if (hasPasswordChange) changes.push('password');
                
                // Generate change message
                let changeMessage = '';
                if (changes.length > 0) {
                    changeMessage = 'Perubahan pada: ' + changes.join(', ');
                } else {
                    changeMessage = 'Tidak ada perubahan yang dilakukan';
                }
                
                $('#editUserModal').modal('hide');
                
                // Show detailed success message
                $('#alert-container').html(
                    `<div class="alert alert-success alert-dismissible fade show">
                        ${response.message}<br>
                        <small>${changeMessage}</small>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>`
                );
                
                // Reset form after successful update
                $('#editUserForm')[0].reset();
            }
        },
        error: function(xhr) {
            if(xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(function(key) {
                    $(`#edit-${key}`).addClass('is-invalid');
                    $(`#edit-${key}-error`).text(errors[key][0]);
                });
                
                // Keep modal open when there are validation errors
                // Show kriteria access container if role is dosen
                if ($('#edit-role').val() === 'dosen') {
                    $('#edit-kriteria-access-container').show();
                    // Reinitialize Select2
                    setTimeout(function() {
                        $('#edit-kriteria-access').select2({
                            placeholder: "Select kriteria",
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#editUserModal')
                        });
                    }, 100);
                }
                
                return;
            } else {
                let errorMessage = 'An error occurred while updating the user.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                $('#editUserModal').modal('hide');
                
                $('#alert-container').html(
                    `<div class="alert alert-danger alert-dismissible fade show">
                        ${errorMessage}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>`
                );
            }
        }
    });
}

// Handle kriteria form submission from dropdown
function handleKriteriaFormSubmit(form) {
    let userId = form.data('user-id');
    let url = `/admin/users/${userId}/kriteria-access`;
    
    // Get selected kriteria IDs from checkboxes
    let selectedKriteria = [];
    form.find('.kriteria-checkbox:checked').each(function() {
        selectedKriteria.push($(this).val());
    });
    
    // Create form data with the selected kriteria
    let formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
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
                // Show success message
                $('#alert-container').html(`
                    <div class="alert alert-success alert-dismissible fade show">
                        Kriteria access updated successfully
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                `);
                
                // Refresh the page to show updated data
                setTimeout(() => {
                    location.reload();
                }, 1500);
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
} 