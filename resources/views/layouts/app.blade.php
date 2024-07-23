<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ $theme['app_name'] . " | " . $title }}</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" href="{{ asset('fonts/fontawesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/temposdusmus-bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/icheck-boostrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/overlayScrollbar.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">

        <style>
            [class*=sidebar-dark-]{
                background-color: {{ $theme['sidebar_bg_color'] }} !important;
            }
            thead tr{
                background-color: {{ $theme['table_header_color'] }} !important;
            }
            thead td{
                color: {{ $theme['table_header_font_color'] }} !important;
            }
            tr.group td{
                background-color: {{ $theme['table_group_color'] }} !important;
            }
            tr.group td{
                color: {{ $theme['table_group_font_color'] }} !important;
            }
            .sidebar a{
                color: {{ $theme['sidebar_font_color'] }} !important;
            }
            .sidebar nav-link.active{
                color: black !important;
            }
            .brand-link .brand-image{
                float:  none !important;
            }
        </style>

        @stack('styles')

    </head>

    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <div class="preloader"></div>

            @include('includes.header')
            @include('includes.sidebar')

            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">{{ $title ?? "Title" }}</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                    <li class="breadcrumb-item active">{{ $title ?? "Title" }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                @yield('content')
            </div>

            <!-- <footer class="main-footer">
            <strong>Copyright &copy; 2022 <a href=""></a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.2.0
            </div>
            </footer> -->
        </div>

        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
        <script>$.widget.bridge('uibutton', $.ui.button)</script>
        <script src="{{ asset('js/bootstrap-bundle.min.js') }}"></script>
        <script src="{{ asset('js/moment.min.js') }}"></script>
        <script src="{{ asset('js/temposdusmus-bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/overlayScrollbar.min.js') }}"></script>
        <script src="{{ asset('js/adminlte.min.js') }}"></script>
        <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>
        <script src="{{ asset('js/numeral.min.js') }}"></script>

        <script>
            @if(session('success'))
                ss({{ session('success') }});
            @elseif(session('error'))
                se({{ session('error') }});
            @endif

            @if(in_array(auth()->user()->role, ['Admin', "Approver"]))
                $("p:contains(Requesition Entry)").parent().find('i').removeClass('fa-keyboard').addClass('fa-desktop')
                $("p:contains(Requesition Entry)").html('Request Monitoring');

                $("p:contains(RX)").html('Consolidated RX');
            @endif
        </script>

        @stack('scripts');
    </body>
</html>