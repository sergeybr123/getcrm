<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ asset('/img/logo/logo.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @hasSection('title')
            @yield('title') - {{ config('app.name', 'GetCRM') }}
        @else
            {{ config('app.name', 'GetCRM') }}
        @endif
    </title>
    <link href="{{ asset('vendors/css/flag-icon.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/css/simple-line-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body id="app" class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
<header class="app-header navbar">
    <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="{{ route('dashboard') }}"><span>GetCRM</span></a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
        <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="nav navbar-nav d-md-down-none mr-auto">

    </ul>
    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item pr-3">
            <a class="nav-link" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out-alt"></i> Выйти
            </a>
        {{--<li class="nav-item dropdown">--}}
            {{--<a class="nav-link nav-link mr-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">--}}
                {{--<i class="far fa-user-circle" style="font-size: 34px;"></i>--}}
            {{--</a>--}}
            {{--<div class="dropdown-menu dropdown-menu-right">--}}
                {{--<div class="dropdown-header text-center">--}}
                    {{--<strong>Управление</strong>--}}
                {{--</div>--}}
                {{--<a class="dropdown-item" href="#"><i class="fa fa-user"></i> Профиль</a>--}}
                {{--<a class="dropdown-item" href="#"><i class="fa fa-wrench"></i> Настройки</a>--}}
                {{--<a class="dropdown-item" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-lock"></i> Выйти</a>--}}
            {{--</div>--}}
        </li>
    </ul>
</header>
<div class="app-body">
    @role('admin|manager')
    @include('layouts.includes.sidebar')
    @endrole
    @role('partner+')
    @include('layouts.includes.partner_sidebar')
    @endrole
    <main class="main">
        <div class="container-fluid">
            <div class="animated fadeIn">
                @yield('content')
            </div>
        </div>
    </main>
</div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
<script src="{{ asset('vendors/js/jquery.min.js') }}"></script>
<script src="{{ asset('vendors/js/popper.min.js') }}"></script>
<script src="{{ asset('vendors/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/all.min.js') }}"></script>
<script src="{{ asset('vendors/js/pace.min.js') }}"></script>
<script src="{{ asset('js/app_simple.js') }}"></script>
<script src="{{ asset('vendors/js/toastr.min.js') }}"></script>
<script src="{{ asset('vendors/js/gauge.min.js') }}"></script>
<script src="{{ asset('vendors/js/moment.min.js') }}"></script>
{{--<script src="js/views/main.js"></script>--}}
<script>
    $(function () {
        $('[data-tooltip="tooltip"]').tooltip()
    })
</script>
@yield('scripts')
</body>
</html>