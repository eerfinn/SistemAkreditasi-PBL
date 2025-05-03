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
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
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
    </script>

    @stack('scripts')

    @yield('page-script')

</body>

</html>
