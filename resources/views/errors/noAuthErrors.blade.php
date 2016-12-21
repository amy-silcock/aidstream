<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aidstream - @lang('title.non_accessible_content')</title>

    <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/flag-icon.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    @yield('head')

</head>
<body>
<div class="login-wrapper">
    {{--<div class="language-select-wrapper">--}}
    {{--<label for="" class="pull-left">Language</label>--}}
    {{--<div class="language-selector pull-left">--}}
    {{--<span class="flag-wrapper"><span class="img-thumbnail flag flag-icon-background flag-icon-{{ config('app.locale') }}"></span></span>--}}
    {{--<span class="caret pull-right"></span>--}}
    {{--</div>--}}
    {{--<ul class="language-select-wrap language-flag-wrap">--}}
    {{--@foreach(config('app.locales') as $key => $val)--}}
    {{--<li class="flag-wrapper" data-lang="{{ $key }}"><span class="img-thumbnail flag flag-icon-background flag-icon-{{ $key }}"></span><span class="language">{{ $val }}</span></li>--}}
    {{--@endforeach--}}
    {{--</ul>--}}
    {{--</div>--}}
    <div class="container-fluid login-container">
        <div class="row">
            <div class="col-lg-4 col-md-8 col-md-offset-2 form-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="/">
                            <img src="{{url('images/logo.png')}}" alt="">
                        </a>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-danger">
                            <span>
                                <p>
                                    {!! $message !!}
                                </p>
                                <p>Return to <a href="{{ url('/auth/login') }}">@lang('trans.login').</a></p>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 create-account-wrapper">
                @lang('global.dont_have_account')<a href="{{ url('/auth/register') }}">@lang('global.create_account')</a>
            </div>
            <div class="col-md-12 logo-text">Aidstream</div>
            <div class="col-md-12 support-desc">
                @lang('for_queries')<a href="mailto:support@aidstream.org">support@aidstream.org</a>
            </div>
        </div>
    </div>
</div>
<!-- Scripts -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{url('/js/jquery.cookie.js')}}"></script>
<script type="text/javascript" src="{{url('/js/main.js')}}"></script>
</body>
</html>
