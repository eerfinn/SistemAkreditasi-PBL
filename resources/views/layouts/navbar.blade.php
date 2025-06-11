<!--**********************************
    Nav header start
***********************************-->
<div class="nav-header">
    <a href="{{ url('/dashboard') }}" class="brand-logo">
        <img src="{{ asset('assets/images/Jti_polinema.png') }}" class="logo-abbr" alt="Polinema Logo" style="height: 40px;">
        <span class="brand-title" style="font-weight: bold; font-size: 1.4rem; color: white;">SiAkred</span>
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line">
                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7468 5.58925C11.0722 5.26381 11.0722 4.73617 10.7468 4.41073C10.4213 4.0853 9.89369 4.0853 9.56826 4.41073L4.56826 9.41073C4.25277 9.72622 4.24174 10.2342 4.54322 10.5631L9.12655 15.5631C9.43754 15.9024 9.96468 15.9253 10.3039 15.6143C10.6432 15.3033 10.6661 14.7762 10.3551 14.4369L6.31096 10.0251L10.7468 5.58925Z" fill="#452B90"/>
                    <path opacity="0.3" d="M16.5801 5.58924C16.9056 5.26381 16.9056 4.73617 16.5801 4.41073C16.2547 4.0853 15.727 4.0853 15.4016 4.41073L10.4016 9.41073C10.0861 9.72622 10.0751 10.2342 10.3766 10.5631L14.9599 15.5631C15.2709 15.9024 15.798 15.9253 16.1373 15.6143C16.4766 15.3033 16.4995 14.7762 16.1885 14.4369L12.1443 10.0251L16.5801 5.58924Z" fill="#452B90"/>
                </svg>
            </span>
        </div>
    </div>
</div>

<!--**********************************
    Nav header end
***********************************-->

<!--**********************************
    Chat box start
***********************************-->
<div class="chatbox">
    <div class="chatbox-close"></div>
    <div class="custom-tab-1">

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#notes">Notes</a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#chat">Chat</a>
            </li> --}}
        </ul>
        <div class="tab-content">
            {{-- Chat tab content (commented out)
            <div class="tab-pane fade" id="chat">
            // ... existing code ...
            </div>
            --}}
            <div class="tab-pane fade active show" id="notes">
                <div class="card mb-sm-3 mb-md-0 note_card">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addNoteModal"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"/><rect fill="#000000" opacity="1.0" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"/></g></svg></a>
                        <div class="flex-grow-1 d-flex justify-content-center">
                            <div>
                                <h6 class="mb-1">Notes & Events</h6>
                                <p class="mb-0">Tambahkan catatan atau event</p>
                            </div>
                        </div>
                        <!-- Added a placeholder div to balance the header -->
                        <div style="width: 18px;"></div>
                    </div>
                    <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Contacts_Body2">
                        <ul class="contacts">
                            <!-- Notes Section -->
                            <li class="notes-section-header">
                                <div class="d-flex bd-highlight">
                                    <div class="user_info">
                                        <h6 class="mb-0">Notes</h6>
                                    </div>
                                </div>
                            </li>
                            <li class="active">
                                <div class="d-flex bd-highlight">
                                    <div class="user_info">
                                        <span>New order placed..</span>
                                        <p>10 Aug 2020</p>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0);" class="btn btn-primary btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-danger btn-xs sharp"><i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex bd-highlight">
                                    <div class="user_info">
                                        <span>Youtube, a video-sharing website..</span>
                                        <p>10 Aug 2020</p>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0);" class="btn btn-primary btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-danger btn-xs sharp"><i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </li>

                            <!-- Events Section -->
                            <li class="events-section-header mt-3">
                                <div class="d-flex bd-highlight">
                                    <div class="user_info">
                                        <h6 class="mb-0">Events</h6>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0);" class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                            <i class="fas fa-plus"></i> Tambah Event
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <div id="events-list">
                                <!-- Events will be loaded dynamically via JavaScript -->
                                <li class="text-center py-3" id="events-loading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </li>
                                <li class="text-center py-3 d-none" id="no-events-message">
                                    <p class="mb-0 text-muted">Tidak ada events</p>
                                </li>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="alerts">
                <div class="card mb-sm-3 mb-md-0 contacts_card">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg></a>
                        <div>
                            <h6 class="mb-1">Notications</h6>
                            <p class="mb-0">Show All</p>
                        </div>
                        <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="1"/><path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"/></g></svg></a>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--**********************************
    Chat box End
***********************************-->

<!--**********************************
    Header start
***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        @yield('title')
                    </div>
                </div>
                <div class="header-right d-flex align-items-center">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link bell dz-theme-mode" href="javascript:void(0);">
                                <svg id="icon-light" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M19.5,10.5 L21,10.5 C21.8284271,10.5 22.5,11.1715729 22.5,12 C22.5,12.8284271 21.8284271,13.5 21,13.5 L19.5,13.5 C18.6715729,13.5 18,12.8284271 18,12 C18,11.1715729 18.6715729,10.5 19.5,10.5 Z M16.0606602,5.87132034 L17.1213203,4.81066017 C17.7071068,4.22487373 18.6568542,4.22487373 19.2426407,4.81066017 C19.8284271,5.39644661 19.8284271,6.34619408 19.2426407,6.93198052 L18.1819805,7.99264069 C17.5961941,8.57842712 16.6464466,8.57842712 16.0606602,7.99264069 C15.4748737,7.40685425 15.4748737,6.45710678 16.0606602,5.87132034 Z M16.0606602,18.1819805 C15.4748737,17.5961941 15.4748737,16.6464466 16.0606602,16.0606602 C16.6464466,15.4748737 17.5961941,15.4748737 18.1819805,16.0606602 L19.2426407,17.1213203 C19.8284271,17.7071068 19.8284271,18.6568542 19.2426407,19.2426407 C18.6568542,19.8284271 17.7071068,19.8284271 17.1213203,19.2426407 L16.0606602,18.1819805 Z M3,10.5 L4.5,10.5 C5.32842712,10.5 6,11.1715729 6,12 C6,12.8284271 5.32842712,13.5 4.5,13.5 L3,13.5 C2.17157288,13.5 1.5,12.8284271 1.5,12 C1.5,11.1715729 2.17157288,10.5 3,10.5 Z M12,1.5 C12.8284271,1.5 13.5,2.17157288 13.5,3 L13.5,4.5 C13.5,5.32842712 12.8284271,6 12,6 C11.1715729,6 10.5,5.32842712 10.5,4.5 L10.5,3 C10.5,2.17157288 11.1715729,1.5 12,1.5 Z M12,18 C12.8284271,18 13.5,18.6715729 13.5,19.5 L13.5,21 C13.5,21.8284271 12.8284271,22.5 12,22.5 C11.1715729,22.5 10.5,21.8284271 10.5,21 L10.5,19.5 C10.5,18.6715729 11.1715729,18 12,18 Z M4.81066017,4.81066017 C5.39644661,4.22487373 6.34619408,4.22487373 6.93198052,4.81066017 L7.99264069,5.87132034 C8.57842712,6.45710678 8.57842712,7.40685425 7.99264069,7.99264069 C7.40685425,8.57842712 6.45710678,8.57842712 5.87132034,7.99264069 L4.81066017,6.93198052 C4.22487373,6.34619408 4.22487373,5.39644661 4.81066017,4.81066017 Z M4.81066017,19.2426407 C4.22487373,18.6568542 4.22487373,17.7071068 4.81066017,17.1213203 L5.87132034,16.0606602 C6.45710678,15.4748737 7.40685425,15.4748737 7.99264069,16.0606602 C8.57842712,16.6464466 8.57842712,17.5961941 7.99264069,18.1819805 L6.93198052,19.2426407 C6.34619408,19.8284271 5.39644661,19.8284271 4.81066017,19.2426407 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    </g>
                                </svg>
                                <svg id="icon-dark" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M12.0700837,4.0003006 C11.3895108,5.17692613 11,6.54297551 11,8 C11,12.3948932 14.5439081,15.9620623 18.9299163,15.9996994 C17.5467214,18.3910707 14.9612535,20 12,20 C7.581722,20 4,16.418278 4,12 C4,7.581722 7.581722,4 12,4 C12.0233848,4 12.0467462,4.00010034 12.0700837,4.0003006 Z" fill="#000000"/>
                                </g>
                                </svg>
                            </a>
                        </li>
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.5 12H19C19.8284 12 20.5 12.6716 20.5 13.5C20.5 14.3284 19.8284 15 19 15H6C5.17157 15 4.5 14.3284 4.5 13.5C4.5 12.6716 5.17157 12 6 12H7.5L8.05827 6.97553C8.30975 4.71226 10.2228 3 12.5 3C14.7772 3 16.6903 4.71226 16.9417 6.97553L17.5 12Z" fill="#222B40"/>
                                    <path opacity="0.3" d="M14.5 18C14.5 16.8954 13.6046 16 12.5 16C11.3954 16 10.5 16.8954 10.5 18C10.5 19.1046 11.3954 20 12.5 20C13.6046 20 14.5 19.1046 14.5 18Z" fill="#222B40"/>
                                </svg>
                                <span id="notification-badge" class="badge light text-white bg-primary rounded-circle position-absolute" style="display: none; top: 0; right: 0; font-size: 0.7rem; padding: 0.2rem 0.45rem;"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-header d-flex align-items-center justify-content-between bg-primary text-white px-4 py-3">
                                    <h6 class="mb-0 text-white">Notifikasi</h6>
                                    <a href="{{ route('notifications.markAllRead') }}" onclick="event.preventDefault(); document.getElementById('mark-all-read-form').submit();" class="text-white fs-6" title="Tandai semua dibaca">
                                        <i class="fas fa-check-double"></i>
                                    </a>
                                    <form id="mark-all-read-form" action="{{ route('notifications.markAllRead') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                                <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3" style="height:350px;">
                                    <ul class="timeline" id="notification-list">
                                        <li class="text-center py-4" id="notification-loading">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </li>
                                        <li class="text-center py-4" id="notification-empty" style="display: none;">
                                            <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                                            <p class="mb-0">Tidak ada notifikasi</p>
                                        </li>
                                    </ul>
                                </div>
                                <a class="all-notification text-center py-2 border-top" href="{{ route('notifications.index') }}">Lihat semua notifikasi <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </li>
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link bell-link" href="javascript:void(0);" onclick="toggleChatbox()">
                            <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1_463)">
                                <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M6.5 2H18.5C19.0523 2 19.5 2.44772 19.5 3V13C19.5 13.5523 19.0523 14 18.5 14H6.5C5.94772 14 5.5 13.5523 5.5 13V3C5.5 2.44772 5.94772 2 6.5 2ZM14.3 4C13.6562 4 12.9033 4.72985 12.5 5.2C12.0967 4.72985 11.3438 4 10.7 4C9.5604 4 8.9 4.88887 8.9 6.02016C8.9 7.27339 10.1 8.6 12.5 10C14.9 8.6 16.1 7.3 16.1 6.1C16.1 4.96871 15.4396 4 14.3 4Z" fill="#222B40"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.29275 6.57254L12.5 12.5L20.7073 6.57254C20.9311 6.41086 21.2437 6.46127 21.4053 6.68514C21.4669 6.77034 21.5 6.87278 21.5 6.97788V17C21.5 18.1046 20.6046 19 19.5 19H5.5C4.39543 19 3.5 18.1046 3.5 17V6.97788C3.5 6.70174 3.72386 6.47788 4 6.47788C4.10511 6.47788 4.20754 6.511 4.29275 6.57254Z" fill="#222B40"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_1_463">
                                <rect width="24" height="24" fill="white" transform="translate(0.5)"/>
                                </clipPath>
                                </defs>
                            </svg>
                            </a>
                        </li>
                            <li class="nav-item ps-3">
                                <div class="dropdown header-profile2">
                                    <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="header-info2 d-flex align-items-center">
                                            <div class="header-media">
                                            {{-- <img src="{{ Auth::user()->photo ? asset('storage/profile/' . Auth::user()->photo) : asset('assets/images/user.jpg') }}" alt="" class= "avatar avatar-md" > --}}
                                            <img src="{{ Auth::user()->photo ? asset('storage/profile/' . Auth::user()->photo) : asset('assets/images/avatar/1.png') }}" alt="Profile" class="navbar-profile-img">
                                        </div>
                                        </div>
                                    </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="card border-0 mb-0">
                                        <div class="card-header py-2">
                                            <div class="products d-flex align-items-center gap-2">
                                            <img src="{{ Auth::user()->photo ? asset('storage/profile/' . Auth::user()->photo) : asset('assets/images/avatar/1.png') }}" class="avatar avatar-md" alt="">
                                            {{-- <img src="{{ Auth::user()->photo ? asset('storage/profile/' . Auth::user()->photo) : asset('assets/images/user.jpg') }}" alt="Profile" class="rounded-circle border border-light shadow-sm" style="width: 36px; height: 36px; object-fit: cover;"> --}}
                                            {{-- <img src="{{ asset('storage/profile/' . $user->photo) }}" alt="User" class="avatar avatar-md"> --}}

                                        <div>
                                            <h6>{{ Auth::user()->name }}</h6>
                                            <span>{{ Auth::user()->role ?? 'User' }}</span> <!-- Sesuaikan jika role tersedia -->
                                        </div>
                                            </div>
                                                </div>
                                                <div class="card-body px-0 py-2">
                                                    <a href="/profile" class="dropdown-item ai-icon">
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.9848 15.3462C8.11714 15.3462 4.81429 15.931 4.81429 18.2729C4.81429 20.6148 8.09619 21.2205 11.9848 21.2205C15.8524 21.2205 19.1543 20.6348 19.1543 18.2938C19.1543 15.9529 15.8733 15.3462 11.9848 15.3462Z" stroke="var(--primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.9848 12.0059C14.5229 12.0059 16.58 9.94779 16.58 7.40969C16.58 4.8716 14.5229 2.81445 11.9848 2.81445C9.44667 2.81445 7.38857 4.8716 7.38857 7.40969C7.38 9.93922 9.42381 11.9973 11.9524 12.0059H11.9848Z" stroke="var(--primary)" stroke-width="1.42857" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                        <span class="ms-2">Profile</span>
                                                    </a>

                                                            <form method="POST" action="{{ route('logout') }}">
                                                                    @csrf
                                                                 <a href="javascript:void(0);" onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item ai-icon">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                                            <span class="ms-2">Logout</span>
                                                        </a>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>
<!--**********************************
    Header end
***********************************-->
<style>
.navbar-profile-img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
}

</style>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addEventModalLabel">Tambah Event Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm">
                    <div class="mb-3">
                        <label for="eventTitle" class="form-label">Judul Event</label>
                        <input type="text" class="form-control" id="eventTitle" required placeholder="Masukkan judul event">
                    </div>
                    <div class="mb-3">
                        <label for="eventDate" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="eventDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventTime" class="form-label">Waktu</label>
                        <input type="time" class="form-control" id="eventTime" value="00:00">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="addToCalendar" checked>
                        <label class="form-check-label" for="addToCalendar">Tambahkan ke Kalender</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveEventBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editEventForm">
                    <input type="hidden" id="editEventId">
                    <div class="mb-3">
                        <label for="editEventTitle" class="form-label">Judul Event</label>
                        <input type="text" class="form-control" id="editEventTitle" required placeholder="Masukkan judul event">
                    </div>
                    <div class="mb-3">
                        <label for="editEventDate" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="editEventDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEventTime" class="form-label">Waktu</label>
                        <input type="time" class="form-control" id="editEventTime" value="00:00">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="updateCalendar" checked>
                        <label class="form-check-label" for="updateCalendar">Perbarui di Kalender</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="updateEventBtn">Perbarui</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addNoteModalLabel">Tambah Catatan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addNoteForm">
                    <div class="mb-3">
                        <label for="noteTitle" class="form-label">Judul Catatan</label>
                        <input type="text" class="form-control" id="noteTitle" required placeholder="Masukkan judul catatan">
                    </div>
                    <div class="mb-3">
                        <label for="noteContent" class="form-label">Isi Catatan</label>
                        <textarea class="form-control" id="noteContent" rows="4" placeholder="Masukkan isi catatan"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveNoteBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleChatbox() {
    document.querySelector('.chatbox').classList.toggle('active');
}
</script>
