@extends('lite.base.sidebar')

@section('title', @trans('lite/title.activities'))

@inject('getCode', 'App\Helpers\GetCodeName')

@section('content')
    <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper activity__detail__wrapper">
        @include('includes.response')
        <div class="panel__heading">
            <div class="panel__heading__info pull-left">
                <div class="panel__title">
                    @lang('lite/global.activity_detail')
                </div>
                <a href="{{ route('lite.activity.index') }}" class="back-to-activities-list">@lang('lite/global.back_to_activities_list')</a>
            </div>
            <a href="{{ route('lite.activity.edit', $activity->id) }}"
               class="edit-activity pull-right">@lang('lite/global.edit_activity')</a>
        </div>
        <div class="panel__body">
            <div class="col-xs-12 col-sm-9 panel__activity__detail">
                <h1 class="activity__title">
                    {{ $activity->title ? $activity->title[0]['narrative'] : trans('lite/global.no_title') }}
                </h1>
                <div class="activity-iati-info">
                    <div class="pull-left iati-identifier-wrapper">@lang('lite/global.iati_identifier'):
                        <span class="iati-identifier">{{ $activity->identifier['activity_identifier'] }}</span>
                    </div>
                    <div class="pull-right activity-publish-state">
                        <span class="pull-left published-in-iati">@lang('lite/global.published_in_iati')</span>
                        <img src="{{asset('images/ic-iati-logo.png')}}" alt="IATI" width="27" height="25">
                    </div>
                </div>
                <div class="activity-info activity-more-info">
                    <ul class="pull-left">
                        <li>
                            <i class="pull-left material-icons">date_range</i>
                            @foreach (getVal($activity->toArray(), ['activity_date'], []) as $date)
                                @if(getVal($date, ['type']) == 2)
                                    <span>  {{ formatDate($date['date']) }} </span>
                                @endif

                                @if(getVal($date, ['type']) == 4)
                                    <span> {{ formatDate($date['date']) }} </span>
                                @endif
                            @endforeach
                        </li>
                        <li>
                            <i class="pull-left material-icons">autorenew</i>
                            @if($activity->activity_status)
                                <span>{{  $getCode->getCodeNameOnly('ActivityStatus', $activity->activity_status) }}<i>(Status)</i></span>
                            @endif
                        </li>
                    </ul>
                </div>
                @include('lite.activity.partials.activityList')
            </div>
            <div class="col-xs-12 col-sm-3 panel__activity__more--info">
                <div class="activity__block activity__status activity-status-{{ $statusLabel[$activityWorkflow] }}">
                    <h4>@lang('lite/global.activity_status')</h4>
                    {{--<div class="info-icon"></div>--}}
                    @foreach($statusLabel as $key => $value)
                        @if($key == $activityWorkflow)
                            <div class="active"><span>{{ trans(sprintf('lite/global.%s',strtolower($value)))}}</span>
                            </div>
                        @endif
                    @endforeach
                    @include('lite.activity.partials.workflow')
                </div>
                {{--<div class="activity__block activity__map">--}}
                {{--map--}}
                {{--</div>--}}
                <div class="activity__block activity__detail__block">
                    <ul>
                        <li><span>@lang('lite/global.activity_detail')</span></li>
                        <li><a href="#">@lang('lite/global.budget_information')</a><i>({{ getVal($count, ['budget'], 0) }})</i></li>
                        <li><a href="#">@lang('lite/global.transactions')</a><i>({{ getVal($count, ['transaction'], 0) }})</i></li>
                    </ul>
                </div>
                <div class="activity__block activity__updated__date">
                    <span class="last-updated-date"><i>@lang('lite/global.last_updated_on'): {{ changeTimeZone($activity['updated_at'], 'M d, Y H:i') }}</i></span>
                </div>
            </div>
        </div>
    </div>
@endsection
@section ('script')
    <script>
        $(document).ready(function () {
            function fixedTop() {
                var fixmeTop = $('.panel__activity__more--info').offset().top - 61;
                $(window).scroll(function () {
                    var currentScroll = $(window).scrollTop();
                    if (currentScroll >= fixmeTop) {
                        $('.panel__activity__more--info').css({
                            position: 'fixed',
                            top: '63px',
                            right: '1px'
                        });
                    } else {
                        $('.panel__activity__more--info').css({
                            position: 'static'
                        });
                    }
                });
            }

            fixedTop();
            $(window).resize(function () {
                fixedTop();
            });
        });
    </script>
@stop
