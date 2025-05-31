// Override Bootstrap modal focus handling to prevent issues with Select2
$.fn.modal.Constructor.prototype._enforceFocus = function() {};

// Debug events - fixed to prevent infinite recursion
(function() {
    // Store the original console.log function
    var originalConsoleLog = console.log;
    var isLogging = false; // Flag to prevent recursive calls
    
    // Replace with a safe version that prevents recursion
    console.log = function() {
        // If we're already processing a log, use the original function directly
        if (isLogging) {
            return originalConsoleLog.apply(console, arguments);
        }
        
        isLogging = true; // Set the flag to prevent recursion
        
        try {
            // Check if the first argument is a string and contains "url is undefined"
            if (arguments.length > 0 && 
                typeof arguments[0] === 'string' && 
                arguments[0].includes('url is undefined')) {
                
                // Log with the original function to avoid recursion
                originalConsoleLog.call(console, 'Select2 Error: url is undefined detected');
                
                // Force re-initialize the Select2 when this error occurs
                if ($('#kriteria-access').length) {
                    initializeSelect2('#kriteria-access', '#addUserModal');
                }
                if ($('#edit-kriteria-access').length) {
                    initializeSelect2('#edit-kriteria-access', '#editUserModal');
                }
                return;
            }
            
            // For all other logs, just use the original function
            return originalConsoleLog.apply(console, arguments);
        } finally {
            isLogging = false; // Reset the flag when done
        }
    };
})();

$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Handle Manage Criteria Access button
    $('#manageCriteriaAccess').click(function() {
        setupManageCriteriaModal();
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

    // Initialize Select2 after DOM is ready with delays to ensure proper rendering
    setTimeout(function() {
        initializeSelect2('#kriteria-access', '#addUserModal');
        initializeSelect2('#edit-kriteria-access', '#editUserModal');
        console.log('Select2 initialization complete');
    }, 500);

    // Toggle kriteria access container based on role selection with improved visibility
    $('#role').change(function() {
        handleRoleChange($(this).val(), '#kriteria-access-container', '#kriteria-selection-alert', '#kriteria-access', '#addUserModal');
    });

    // Toggle edit kriteria access container with similar improvements
    $('#edit-role').change(function() {
        handleRoleChange($(this).val(), '#edit-kriteria-access-container', '#edit-kriteria-selection-alert', '#edit-kriteria-access', '#editUserModal');
    });

    // Reset form and error state when modal is shown
    $('#addUserModal').on('show.bs.modal', function () {
        // Reset form before modal is shown
        $('#addUserForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#kriteria-access-container').hide();
    });
    
    $('#addUserModal').on('shown.bs.modal', function () {
        console.log('Add user modal shown');
        
        // Ensure the modal doesn't have overflow hidden which can hide dropdowns
        $(this).css('overflow', 'visible');
        $('.modal-open .modal').css('overflow', 'visible');
        
        // Initialize Select2 after modal is fully shown
        setTimeout(function() {
            initializeSelect2('#kriteria-access', '#addUserModal');
            console.log('Add user modal Select2 initialized');
        }, 200);
    });
    
    $('#editUserModal').on('show.bs.modal', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#edit-kriteria-access-container').hide();
    });
    
    $('#editUserModal').on('shown.bs.modal', function () {
        console.log('Edit user modal shown');
        
        // Ensure the modal doesn't have overflow hidden
        $(this).css('overflow', 'visible');
        $('.modal-open .modal').css('overflow', 'visible');
        
        // Initialize Select2 after modal is fully shown
        setTimeout(function() {
            initializeSelect2('#edit-kriteria-access', '#editUserModal');
            console.log('Edit user modal Select2 initialized');
            
            // If role is dosen, show the kriteria access container
            if ($('#edit-role').val() === 'dosen') {
                $('#edit-kriteria-access-container').show();
            }
        }, 200);
    });

    // Improved button handlers for Select2 
    $(document).on('click', '#show-kriteria-selector', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Stop event propagation
        openKriteriaSelector('#kriteria-access', '#addUserModal');
    });
    
    // Same improved handling for edit modal
    $(document).on('click', '#edit-show-kriteria-selector', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Stop event propagation
        openKriteriaSelector('#edit-kriteria-access', '#editUserModal');
    });

    // Add User Form Submit
    $('#addUserForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        submitAddUserForm(table);
    });

    // Delete User
    $(document).on('click', '.delete-user', function() {
        handleDeleteUser($(this), table);
    });

    // Handle Edit Button Click - with improved selector and delegation
    $(document).on('click', '.btn-info.btn-sm, [data-action="edit-user"]', function(e) {
        e.preventDefault();
        
        // Store this reference to make it available in the setTimeout function
        const $button = $(this);
        
        // Log the button that was clicked
        console.log('Edit button clicked:', $button);
        console.log('Button HTML:', $button.prop('outerHTML'));
        
        // Get user ID from various sources
        let userId = $button.data('id');
        if (!userId) {
            // Try to extract from URL if it's an anchor
            const href = $button.attr('href');
            if (href) {
                const matches = href.match(/\/users\/(\d+)/);
                if (matches && matches[1]) {
                    userId = matches[1];
                }
            }
        }
        
        // If we still don't have a userId, try from the parent row
        if (!userId) {
            const $row = $button.closest('tr');
            if ($row.length && $row.attr('id')) {
                userId = $row.attr('id').replace('user-', '');
            }
        }
        
        console.log('User ID before calling handleEditUserClick:', userId);
        
        // Call edit function with button and explicit userId
        setTimeout(() => {
            handleEditUserClick($button, userId);
        }, 100);
    });

    // Handle Edit User Form Submit
    $('#editUserForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        submitEditUserForm();
    });

    // Prevent dropdown from closing when clicking inside it
    $(document).on('click', '.kriteria-dropdown', function(e) {
        e.stopPropagation();
    });
});

// Improved Select2 initialization function
function initializeSelect2(selector, parentSelector) {
    console.log('Initializing Select2 for ' + selector);
    
    try {
        // Destroy existing instance if ada
        if ($(selector).hasClass('select2-hidden-accessible')) {
            $(selector).select2('destroy');
        }
        
        // Make sure parent element is visible
        $(parentSelector).css('overflow', 'visible');
        
        // Inisialisasi Select2 dengan opsi lengkap
        $(selector).select2({
            placeholder: "Pilih kriteria akses",
            allowClear: true,
            width: '100%',
            dropdownParent: $(parentSelector),
            closeOnSelect: false,
            templateResult: function(data) {
                return data.text;
            }
        }).on('select2:open', function() {
            console.log('Select2 opened for ' + selector);
            
            // Fix z-index issues
            $('.select2-container--open').css('z-index', 2000);
            $('.select2-dropdown').css('z-index', 2000);
            
            // Add visual indicator for debugging
            $(this).addClass('select2-debug');
            
            // Force dropdown to be visible - critical fix
            setTimeout(function() {
                $('.select2-dropdown').css({
                    'z-index': 2000,
                    'display': 'block',
                    'visibility': 'visible'
                });
            }, 50);
        });
        
        // Prevent dropdown from closing when clicking inside modal
        $(document).on('click', '.select2-container', function(e) {
            e.stopPropagation();
        });
        
        return true;
    } catch (e) {
        console.error('Error initializing Select2:', e);
        return false;
    }
}

// Handle role change
function handleRoleChange(role, containerSelector, alertSelector, selectSelector, modalSelector) {
    console.log('Role changed to: ' + role);
    if (role === 'dosen') {
        $(containerSelector).show();
        
        // Add an alert to make it very clear
        if ($(alertSelector).length === 0) {
            $(containerSelector).prepend(
                '<div id="' + alertSelector.substring(1) + '" class="alert alert-warning mb-2">' +
                '<strong>Perhatian:</strong> Silakan gunakan tombol "Tampilkan Pilihan Kriteria" di bawah untuk memilih kriteria.' +
                '</div>'
            );
        }
        
        // Re-initialize Select2 when container is shown
        setTimeout(function() {
            initializeSelect2(selectSelector, modalSelector);
        }, 100);
    } else {
        $(containerSelector).hide();
    }
}

// Open kriteria selector
function openKriteriaSelector(selectSelector, modalSelector) {
    console.log('Show kriteria selector clicked for ' + selectSelector);
    
    // Force destroy and reinitialize
    if ($(selectSelector).hasClass('select2-hidden-accessible')) {
        $(selectSelector).select2('destroy');
    }
    
    // Set a specific higher z-index to the dropdown container
    $(modalSelector).css('z-index', 1050);
    
    setTimeout(function() {
        // Initialize with dropdown parent explicitly set to body to avoid z-index issues
        $(selectSelector).select2({
            placeholder: "Pilih kriteria akses",
            allowClear: true,
            width: '100%',
            dropdownParent: $('body'),
            closeOnSelect: false,
            dropdownCssClass: 'select2-dropdown-large'
        });
        
        // Open the dropdown
        $(selectSelector).select2('open');
        
        // Force visibility with specific styles
        $('.select2-container--open').css({
            'z-index': 2000,
            'position': 'absolute'
        });
        
        $('.select2-dropdown').css({
            'z-index': 2001,
            'width': '300px !important'
        });
        
        console.log('Kriteria selector reinitialized and opened');
    }, 100);
} 