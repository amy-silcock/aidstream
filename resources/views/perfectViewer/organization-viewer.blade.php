<!DOCTYPE html>
<html>

@inject('codeListHelper', 'App\Helpers\GetCodeName')

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Organization Viewer</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <link rel="shortcut icon" type="image/png" sizes="16*16" href="{{asset('/images/favicon.png')}}"/>
    <link rel="stylesheet" href="{{asset('/css/bootstrap.min.css')}}">
    <link href="{{ asset('/css/jquery.jscrollpane.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/style.min.css')}}">
</head>

<style type="text/css">
    .bar {
        fill: #B2BEC4;
        font-size: 14px;
        cursor: pointer;
    }

    rect.bar:hover {
        fill: #00A8FF !important;
    }

    .axis {
        font: 14px sans-serif;
        fill: #979797;
    }

    .axis path,
    .axis line {
        fill: none;
        shape-rendering: crispEdges;
    }
</style>

<body>
@include('includes.header')
<div class="wrapper">
    @include('includes.response')
    <div id="tooltip" class="tooltips"></div>
    <div id="map"></div>
    <section class="col-md-12 org-map-wrapper pull-left">
        <div class="width-940">
            <div class="col-md-4 organisation-info">
                <a href="#" class="organisation-logo">
                    @if($organizations['logo'])
                        <div class="logo">
                            <img src="{{ $organizations['logo_url'] }}" alt="{{ $organizations['name'] }}" width="auto" height="68">
                        </div>
                    @endif
                </a>
                <div class="organisation-more-info">
                <span class="organisation-name">
        <a href="#" title="AbleChildAfrica">
            {{$organizations['name']}}
        </a>
    </span>
                    @if($organizations['address'])
                        <address><i class="pull-left material-icons">room</i>{{$organizations['address']}}</address>
                    @endif
                    <a href="{{url('/who-is-using')}}" class="see-all-activities"><i class="pull-left material-icons">arrow_back</i>See all Organisations</a>
                </div>
            </div>
        </div>
    </section>
    <section class="col-md-12 org-main-wrapper">
        <div class="width-940" data-sticky_parent>
            <div class="col-xs-12 col-sm-4 col-md-4 org-main-transaction-wrapper pull-right" data-sticky_column>
                <div class="org-transaction-wrapper">
                    <ul>
                        <li>
                            <h4>Total Commitments</h4>
                            <span>
                                ${{round(getVal($organizations, ['transaction_totals', 'total_commitments'], 0),3)}}
                            </span>
                        </li>
                        <li>
                            <h4>Total Disbursements</h4>
                            <span>
                                ${{round(getVal($organizations, ['transaction_totals', 'total_disbursements'], 0),3)}}
                            </span>
                        </li>
                        <li>
                            <h4>Total Expenditures</h4>
                            <span>
                                ${{round(getVal($organizations, ['transaction_totals', 'total_expenditures'], 0),3)}}
                            </span>
                        </li>
                        <li>
                            <h4>Total Incoming Funds</h4>
                            <span>
                                ${{round(getVal($organizations, ['transaction_totals', 'total_incoming_funds'], 0),3)}}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-xs-12 col-sm-8 col-md-8 org-activity-wrapper pull-left" data-sticky_column>
                @if(count($activities) <= 0)<h2>Activities <span class="activity-count">({{count($activities)}})</span></h2>@endif
                <ul class="activities-listing">
                    @foreach($activities as $index => $activity)
                        <li>
                            <a href="{{url('/who-is-using/'.$organizations['org_slug'].'/'.$activity['activity_id'])}}">
                                <div class="col-xs-12 col-sm-9 col-md-9 pull-left activity-info-wrapper">
                                    <h3 class="activity-name">
                                        {{getVal($activity, ['published_data', 'title', 0, 'narrative'], 'Not Available')}}
                                    </h3>
                                    <div class="activity-publish-state">
                                        @if(getVal($activity, ['activity_in_registry'], null))
                                            <span class="pull-left published-in-iati">
                                        Registered in IATI
                                    </span>
                                        @else
                                            <span class="pull-left unpublished-in-iati">
                                        Not Published in IATI
                                    </span>
                                        @endif
                                        <img src="{{asset('images/ic-iati-logo.png')}}" alt="IATI" width="20" height="19">
                                    </div>
                                    @if(getVal($activity, ['published_data', 'identifier', 'activity_identifier'], null))
                                        <div class="iati-identifier-wrapper">IATI Identifier:
                                            <span class="iati-identifier">
                                                {{getVal($activity, ['published_data', 'identifier', 'activity_identifier'], '')}}
                                        </span>
                                        </div>
                                    @endif
                                    <div class="activity-info">
                                        <ul class="pull-left">
                                            @if($activity['published_data']['activity_date'])
                                                <li><i class="pull-left material-icons">date_range</i>
                                                    @foreach(getVal($activity, ['published_data', 'activity_date'], []) as $index => $date)
                                                        <span>
                                                            @if(getVal($date, ['type'], 0) == 2)
                                                                {{dateFormat('M d, Y', getVal($date, ['date'], ''))}}
                                                                @break
                                                            @elseif(getVal($date, ['type'], 0) == 1)
                                                                {{dateFormat('M d, Y', getVal($date, ['date'], ''))}}
                                                                @break
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                    @foreach(getVal($activity, ['published_data', 'activity_date'], []) as $index => $date)
                                                        <span>
                                                            @if(getVal($date, ['type'], 0) == 4)
                                                                - {{dateFormat('M d, Y', getVal($date, ['date'], ''))}}
                                                                @break
                                                            @elseif(getVal($date, ['type'], 0) == 3)
                                                                - {{dateFormat('M d, Y', getVal($date, ['date'], ''))}}
                                                                @break
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </li>
                                            @endif
                                            @if(getVal($activity, ['published_data', 'activity_status'], null))
                                                <li>
                                                    <i class="pull-left material-icons">autorenew</i>
                                                    <span>
                                    {{ $codeListHelper->getCodeNameOnly('ActivityStatus', getVal($activity, ['published_data', 'activity_status'], '')) }}
                                                        <i>(Status)</i></span>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3 pull-right total-budget-wrapper">
                                    @if(getVal($activity, ['published_data', 'totalBudget', 'value'], null))
                                        <span>Total Budget</span>
                                        <span class="total-budget-amount">{{round(getVal($activity, ['published_data', 'totalBudget', 'value'], 0), 3)}}</span>
                                        <span class="currency">USD</span>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    <footer>
        <div class="width-900">
            <div class="social-wrapper bottom-line">
                <div class="col-md-12 text-center">
                    <ul>
                        <li><a href="https://github.com/younginnovations/aidstream-new" class="github"
                               title="Fork us on Github">Fork us on Github</a></li>
                        <li><a href="https://twitter.com/aidstream" class="twitter" title="Follow us on Twitter">Follow
                                us
                                on Twitter</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-nav bottom-line">
                <div class="col-md-12">
                    <ul>
                        <li><a href="{{ url('/about') }}">About</a></li>
                        <li><a href="{{ url('/who-is-using') }}">Who's using</a></li>
                    </ul>
                    <ul>
                        @if(auth()->check())
                            <li>
                                <a href="{{ url((auth()->user()->role_id == 1 || auth()->user()->role_id == 2) ? config('app.admin_dashboard') : config('app.super_admin_dashboard'))}}">Go
                                    to Dashboard</a>
                            </li>
                        @else
                            <li><a href="{{ url('/auth/login') }}">Login</a></li>
                            <li><a href="{{ url('/auth/register') }}">Register</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="footer-logo">
                <div class="col-md-12 text-center">
                    <a href="{{ url('/') }}"><img src="/images/logo-text.png" alt=""></a>
                </div>
            </div>
        </div>
        <div class="width-900 text-center">
            <div class="col-md-12 support-desc">
                For queries, suggestions, shoot us an email at <a href="mailto:support@aidstream.org">support@aidstream
                    .org</a>
            </div>
        </div>
    </footer>
</div>
</body>
<script src="/js/jquery.js"></script>
<script src="/js/jquery-stickykit.js"></script>
<script src="/js/d3.min.js"></script>
<script type="text/javascript" src="/js/worldMap.js"></script>
<script>
    var recipientCountries = {!!json_encode(array_flip($recipientCountries))!!};
    $(document).ready(function () {
        function hamburgerMenu() {
            $('.navbar-toggle.collapsed').click(function () {
                $('.navbar-collapse').toggleClass('out');
                $(this).toggleClass('collapsed');
            });
        }

        hamburgerMenu();

        function sidebarStick() {
            if ($(window).width() > 768) {
                var contentHeight = $('.org-activity-wrapper').height();
                var sidebarHeight = $('.org-main-transaction-wrapper').height();
                if (contentHeight > sidebarHeight) {
                    $('.org-main-transaction-wrapper').height(contentHeight);
                    $(".org-main-transaction-wrapper .org-transaction-wrapper").stick_in_parent();
                }
            }
            else {
                $('.org-main-transaction-wrapper').height('auto');
            }
        }

        sidebarStick();

        $(window).resize(function () {
            sidebarStick();
        });
    });
</script>
</html>
