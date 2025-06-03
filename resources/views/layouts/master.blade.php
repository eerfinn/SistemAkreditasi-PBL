<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="robots" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Yeshadmin:Customer Relationship Management Admin Bootstrap 5 Template">
    <meta property="og:title" content="Yeshadmin:Customer Relationship Management Admin Bootstrap 5 Template">
    <meta property="og:description" content="Yeshadmin:Customer Relationship Management Admin Bootstrap 5 Template">
    <meta property="og:image" content="https://yeshadmin.dexignzone.com/xhtml/social-image.png">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PAGE TITLE HERE -->
    <title>@yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}">
    <link href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Vendor Style -->
    @yield('vendor-style')

    <!-- Page Style -->
    @yield('page-style')

    <!-- tagify-css -->
    <link href="{{ asset('assets/vendor/tagify/dist/tagify.css') }}" rel="stylesheet">

    <!-- Style css -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">

    <!-- Global style overrides -->
    <style>
        /* Hide modal backdrops globally */
        .modal-backdrop {
            display: none !important;
        }

        /* Ensure modals are always on top */
        .modal {
            padding-top: 70px;
            margin-left: 100px;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999 !important;
            margin-top: 0px;
        }

        /* Ensure modal content is clickable */
        .modal-dialog {
            z-index: 10000 !important;
        }

        /* Notification styles */
        .notification_dropdown .dropdown-menu {
            min-width: 320px;
            max-width: 400px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border: none;
        }

        .notification_dropdown .timeline-panel {
            transition: all 0.2s ease;
        }

        .notification_dropdown .all-notification {
            display: block;
            padding: 10px;
            text-align: center;
            font-weight: 500;
            color: var(--primary);
            border-top: 1px solid #eee;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .notification_dropdown .all-notification:hover {
            background: var(--rgba-primary-1);
        }

        #notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            border-radius: 50%;
            font-size: 0.65rem;
            font-weight: 600;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>

</head>

<body>

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">
        @include('layouts/navbar')
        @include('layouts/sidebar')

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        <!-- Logout Form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>



        @include('layouts/footer')
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    @yield('vendor-script')

    <!-- Custom Scripts -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/deznav-init.js') }}"></script>
    <script src="{{ asset('assets/js/demo.js') }}"></script>

    <!-- Setup AJAX CSRF Token -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Notification URLs
        var notificationGetUrl = "{{ route('notifications.getNavbar') }}";
        var notificationReadUrl = "{{ url('notifications/read/__id__') }}";
    </script>

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Theme switcher
            $(".dz-theme-mode").on("click", function() {
                $("body").toggleClass("theme-dark");
                var isDark = $("body").hasClass("theme-dark");
                localStorage.setItem("theme", isDark ? "dark" : "light");
            });

            // Load theme from local storage
            var savedTheme = localStorage.getItem("theme");
            if (savedTheme === "dark") {
                $("body").addClass("theme-dark");
            }

            // Load notifications
            loadNotifications();

            // Load events for the Notes tab in navbar
            loadEvents();

            // Handle save event button click
            $('#saveEventBtn').on('click', function() {
                saveEvent();
            });

            // Handle update event button click
            $('#updateEventBtn').on('click', function() {
                updateEvent();
            });

            // Handle save note button click
            $('#saveNoteBtn').on('click', function() {
                saveNote();
            });

            // Additional script for managing active sidebar menu in kriteria.upload.form route
            @if(request()->routeIs('kriteria.upload.form'))
                // Pastikan menu Kriteria terbuka dan aktif
                $('.metismenu > li').each(function() {
                    if ($(this).find('a.has-arrow').first().hasClass('active')) {
                        $(this).addClass('mm-active');
                        $(this).find('ul').addClass('mm-show');
                        
                        // Cari dan aktifkan sub-menu kriteria yang sesuai
                        var kriteriaId = "{{ request()->route('kriteria') }}";
                        $(this).find('ul li a').each(function() {
                            if ($(this).attr('href').includes('/kriteria/' + kriteriaId)) {
                                $(this).addClass('mm-active');
                                $(this).parent().addClass('mm-active');
                            }
                        });
                    }
                });
            @endif

            // Function to load events
            function loadEvents() {
                $('#events-loading').show();
                $('#no-events-message').addClass('d-none');

                $.ajax({
                    url: '{{ route("tugas.index") }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#events-loading').hide();

                        if (response.length === 0) {
                            $('#no-events-message').removeClass('d-none');
                            return;
                        }

                        // Clear existing events
                        $('#events-list .event-item').remove();

                        // Add events to the list
                        response.forEach(function(event) {
                            addEventToList(event);
                        });

                        // Broadcast an event to update dashboard events
                        $(document).trigger('eventsUpdated', [response]);
                    },
                    error: function(xhr) {
                        $('#events-loading').hide();
                        $('#no-events-message').removeClass('d-none').text('Error loading events');
                        console.error('Error loading events:', xhr);
                    }
                });
            }

            // Function to add event to the list
            function addEventToList(event) {
                const date = new Date(event.tanggal);
                const formattedDate = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });

                const eventHtml = `
                <li class="event-item">
                    <div class="d-flex bd-highlight">
                        <div class="user_info">
                            <span>${event.judul}</span>
                            <p>${formattedDate}</p>
                        </div>
                        <div class="ms-auto">
                            <a href="javascript:void(0);" class="btn btn-primary btn-xs sharp me-1 edit-event" data-id="${event.id}">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <a href="javascript:void(0);" class="btn btn-danger btn-xs sharp delete-event" data-id="${event.id}">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </li>`;

                $('#events-list').append(eventHtml);
            }

            // Function to save event
            function saveEvent() {
                const title = $('#eventTitle').val().trim();
                const date = $('#eventDate').val();
                const time = $('#eventTime').val() || '00:00';
                const addToCalendar = $('#addToCalendar').is(':checked');

                if (!title || !date) {
                    alert('Judul dan tanggal harus diisi!');
                    return;
                }

                $.ajax({
                    url: '{{ route("tugas.store") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        judul: title,
                        tanggal: date,
                        waktu: time,
                        show_in_calendar: addToCalendar
                    }),
                    processData: false,
                    success: function(response) {
                        // Add to events list
                        addEventToList(response);

                        // Reset form and close modal
                        $('#addEventForm')[0].reset();
                        $('#addEventModal').modal('hide');

                        // Hide no events message if visible
                        $('#no-events-message').addClass('d-none');

                        // Show success message
                        showToast('Success', 'Event berhasil ditambahkan', 'success');

                        // Reload events to update both navbar and dashboard
                        loadEvents();
                    },
                    error: function(xhr) {
                        console.error('Error saving event:', xhr);
                        showToast('Error', 'Gagal menyimpan event', 'error');
                    }
                });
            }

            // Function to update event
            function updateEvent() {
                const id = $('#editEventId').val();
                const title = $('#editEventTitle').val().trim();
                const date = $('#editEventDate').val();
                const time = $('#editEventTime').val() || '00:00';
                const updateCalendar = $('#updateCalendar').is(':checked');

                if (!title || !date) {
                    alert('Judul dan tanggal harus diisi!');
                    return;
                }

                $.ajax({
                    url: `/tugas/${id}`,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        judul: title,
                        tanggal: date,
                        waktu: time,
                        show_in_calendar: updateCalendar
                    }),
                    processData: false,
                    success: function(response) {
                        // Reload events
                        loadEvents();

                        // Reset form and close modal
                        $('#editEventForm')[0].reset();
                        $('#editEventModal').modal('hide');

                        // Show success message
                        showToast('Success', 'Event berhasil diperbarui', 'success');
                    },
                    error: function(xhr) {
                        console.error('Error updating event:', xhr);
                        showToast('Error', 'Gagal memperbarui event', 'error');
                    }
                });
            }

            // Function to save note
            function saveNote() {
                const title = $('#noteTitle').val().trim();
                const content = $('#noteContent').val().trim();

                if (!title) {
                    alert('Judul catatan harus diisi!');
                    return;
                }

                // Create a simple note object
                const note = {
                    title: title,
                    content: content,
                    date: new Date().toISOString().split('T')[0]  // Today's date in YYYY-MM-DD format
                };

                // Get existing notes from localStorage or create empty array
                let notes = JSON.parse(localStorage.getItem('userNotes') || '[]');

                // Add new note
                notes.push(note);

                // Save back to localStorage
                localStorage.setItem('userNotes', JSON.stringify(notes));

                // Update notes list in UI
                updateNotesUI();

                // Reset form and close modal
                $('#addNoteForm')[0].reset();
                $('#addNoteModal').modal('hide');

                // Show success message
                showToast('Success', 'Catatan berhasil ditambahkan', 'success');
            }

            // Function to update notes UI
            function updateNotesUI() {
                // Get notes from localStorage
                const notes = JSON.parse(localStorage.getItem('userNotes') || '[]');

                // Clear existing notes
                $('.notes-section-header').nextUntil('.events-section-header').remove();

                // Add notes to UI
                if (notes.length > 0) {
                    notes.forEach(function(note, index) {
                        const formattedDate = new Date(note.date).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });

                        const noteHtml = `
                        <li class="note-item" data-index="${index}">
                            <div class="d-flex bd-highlight">
                                <div class="user_info">
                                    <span>${note.title}</span>
                                    <p>${formattedDate}</p>
                                </div>
                                <div class="ms-auto">
                                    <a href="javascript:void(0);" class="btn btn-primary btn-xs sharp me-1 edit-note" data-index="${index}"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-xs sharp delete-note" data-index="${index}"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </li>`;

                        $(noteHtml).insertAfter('.notes-section-header');
                    });
                } else {
                    $('<li class="text-center py-3"><p class="mb-0 text-muted">Tidak ada catatan</p></li>').insertAfter('.notes-section-header');
                }
            }

            // Initialize notes UI on document ready
            updateNotesUI();

            // Handle delete note button click
            $(document).on('click', '.delete-note', function() {
                const index = $(this).data('index');

                if (confirm('Apakah Anda yakin ingin menghapus catatan ini?')) {
                    // Get notes from localStorage
                    let notes = JSON.parse(localStorage.getItem('userNotes') || '[]');

                    // Remove note at index
                    notes.splice(index, 1);

                    // Save back to localStorage
                    localStorage.setItem('userNotes', JSON.stringify(notes));

                    // Update UI
                    updateNotesUI();

                    // Show success message
                    showToast('Success', 'Catatan berhasil dihapus', 'success');
                }
            });

            // Handle edit event button click
            $(document).on('click', '.edit-event', function() {
                const id = $(this).data('id');

                // Get event data
                $.ajax({
                    url: `/tugas/${id}`,
                    type: 'GET',
                    success: function(response) {
                        $('#editEventId').val(response.id);
                        $('#editEventTitle').val(response.judul);
                        $('#editEventDate').val(response.tanggal);
                        $('#editEventTime').val(response.waktu ? response.waktu.substring(0, 5) : '00:00');
                        $('#updateCalendar').prop('checked', response.show_in_calendar);

                        // Show modal
                        $('#editEventModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Error fetching event:', xhr);
                        showToast('Error', 'Gagal mengambil data event', 'error');
                    }
                });
            });

            // Handle delete event button click
            $(document).on('click', '.delete-event', function() {
                const id = $(this).data('id');

                if (confirm('Apakah Anda yakin ingin menghapus event ini?')) {
                    $.ajax({
                        url: `/tugas/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            // Reload events to update both navbar and dashboard
                            loadEvents();

                            // Show success message
                            showToast('Success', 'Event berhasil dihapus', 'success');
                        },
                        error: function(xhr) {
                            console.error('Error deleting event:', xhr);
                            showToast('Error', 'Gagal menghapus event', 'error');
                        }
                    });
                }
            });

            // Function to show toast notification
            function showToast(title, message, type) {
                if (typeof toastr !== 'undefined') {
                    toastr[type](message, title);
                } else {
                    alert(`${title}: ${message}`);
                }
            }

            // Load notifications
            function loadNotifications() {
                // Implementation for notifications
                // ...
            }
        });
    </script>

    @stack('scripts')

    @yield('page-script')

</body>

</html>
