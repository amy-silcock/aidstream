<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <title>Aidstream - Register</title>
    <link rel="shotcut icon" type="image/png" sizes="32*32" href="{{ asset('/images/favicon.png') }}"/>
    <link href="{{ asset('/css/main.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2-rc.1/css/select2.min.css" rel="stylesheet"/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Scripts -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{url('/js/jquery.cookie.js')}}"></script>
    <script type="text/javascript" src="{{url('/js/jquery-ui-1.10.4.tooltip.js')}}"></script>
    <script type="text/javascript" src="{{url('/js/main.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('form select').select2();
        });
    </script>

    @yield('head')
</head>
<body>
<header>
    <nav class="navbar navbar-default navbar-static">
        <div class="navbar-header">
            <a href="{{ url('/') }}" class="navbar-brand">Aidstream</a>
            <button type="button" class="navbar-toggle collapsed">
                <span class="sr-only">Toggle navigation</span>
                <span class="bar1"></span>
                <span class="bar2"></span>
                <span class="bar3"></span>
            </button>
        </div>
        <div class="navbar-collapse navbar-right">
            <ul class="nav navbar-nav">
                <li><a class="{{ Request::is('about') ? 'active' : '' }}" href="{{ url('/about') }}">About</a></li>
                <li><a class="{{ Request::is('who-is-using') ? 'active' : '' }}" href="{{ url('/who-is-using') }}">Who's Using It?</a></li>
                <li><a href="https://github.com/younginnovations/aidstream-new/wiki/User-Guide" target="_blank">User Guide</a></li>
                <!--<li><a href="#">Snapshot</a></li>-->
            </ul>
            <div class="action-btn pull-left">
                @if(auth()->check())
                    <a href="{{ url((auth()->user()->role_id == 1 || auth()->user()->role_id == 2) ? config('app.admin_dashboard') : config('app.super_admin_dashboard'))}}" class="btn btn-primary">Go
                        to Dashboard</a>
                @else
                    <a href="{{ url('/auth/login')}}" class="btn btn-primary">Login/Register</a>
                @endif
            </div>
        </div>
    </nav>
</header>

<div class="register-wrapper">
    {{--    <div class="language-select-wrapper">
            <label for="" class="pull-left">Language</label>

            <div class="language-selector pull-left">
                <span class="flag-wrapper"><span class="img-thumbnail flag flag-icon-background flag-icon-{{ config('app.locale') }}"></span></span>
                <span class="caret pull-right"></span>
            </div>
            <ul class="language-select-wrap language-flag-wrap">
                @foreach(config('app.locales') as $key => $val)
                    <li class="flag-wrapper" data-lang="{{ $key }}"><span class="img-thumbnail flag flag-icon-background flag-icon-{{ $key }}"></span><span class="language">{{ $val }}</span></li>
                @endforeach
            </ul>
        </div>--}}
    <div class="container-fluid register-container same-identifier-container">
        <div class="row">
            <div class="col-lg-4 col-md-8 col-md-offset-2 form-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                    </div>
                    <div class="panel-body text-center same-identifier">
                        <img src="{{ url('/images/ic-warning.svg') }}" alt="warning" width="81" height="66">
                        <h1>IATI Organisational Identifier Error</h1>
                        <p>
                            The IATI organisational identifier you entered <strong>"{{ $orgIdentifier }}"</strong> is being used by another organisation on AidStream.
                        </p>
                        <h2>"{{ $orgName }}"</h2>
                        <div class="col-md-12 identifier-information">
                            <p>If this is your organisation you may do one of the followings</p>
                            <div class="col-sm-6">
                                <h3>Retrieve Login Credentials</h3>
                                <p>
                                    I already have an account but forgotten my login credentials.
                                </p>
                                <a href="/password/email" class="btn btn-primary">Retrieve Login Credentials</a>
                            </div>
                            <div class="col-sm-6">
                                <h3>Administrator Information</h3>
                                <p>
                                    The administrator of the organisation name is
                                </p>
                                <span class="admin-name">{{ $adminName }}</span>
                                <a href="{{ route('contact', ['need-new-user']) }}" class="btn btn-primary">Contact Administrator</a>
                            </div>
                        </div>
                        <p>
                            No, this is not my organisation. <a href="{{ route('contact', ['not-my-organization']) }}">Contact support@aidstream.org</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-12 create-account-wrapper">
                <a href="{{ url('/auth/login') }}">I already have an account</a>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

@if(env('APP_ENV') == 'local')
    <script type="text/javascript" src="{{url('/js/jquery.js')}}"></script>
    <script type="text/javascript" src="{{url('/js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{url('/js/jquery.cookie.js')}}"></script>
    <script type="text/javascript" src="{{url('/js/select2.min.js')}}"></script>
@else
    <script type="text/javascript" src="{{url('/js/main.min.js')}}"></script>
    <!-- Google Analytics -->
    <script type="text/javascript" src="{{url('/js/ga.js')}}"></script>
    <!-- End Google Analytics -->
    <script>
        $(document).ready(function () {
            function hamburgerMenu() {
                $('.navbar-toggle.collapsed').click(function () {
                    $('.navbar-collapse').toggleClass('out');
                    $(this).toggleClass('collapsed');
                });
            }

            hamburgerMenu();
        });
    </script>
@endif
</body>
</html>
