@extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.min.css') }}">
    @stack('css')
@stop

@section('classes_body', $adminlte->getBodyClass())

@section('body')
    <div class="wrapper">

        {{-- Top Navbar --}}
        @include('adminlte::partials.navbar.navbar')

        {{-- Main Sidebar Container --}}
        @include('adminlte::partials.sidebar.sidebar')

        {{-- Content Wrapper. Contains page content --}}
        <div class="content-wrapper">

            {{-- Content Header (Page header) --}}
            @if(isset($content_header))
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">{{ $content_header }}</h1>
                            </div>
                            <div class="col-sm-6">
                                {{-- Breadcrumb --}}
                                @isset($breadcrumb)
                                    <ol class="breadcrumb float-sm-right">
                                        {{-- You can load breadcrumb items with the following method: --}}
                                        {{-- @foreach($breadcrumb as $item) --}}
                                        {{--     <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li> --}}
                                        {{-- @endforeach --}}
                                    </ol>
                                @endisset
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Main content --}}
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

        </div>

        {{-- Footer --}}
        @include('adminlte::partials.footer.footer')

        {{-- Control Sidebar --}}
        @include('adminlte::partials.controlsidebar.controlsidebar')

    </div>
@stop

@section('adminlte_js')
    <script src="{{ asset('js/adminlte.min.js') }}"></script>
    @stack('js')
@stop