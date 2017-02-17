@extends('lite.base.sidebar')

@section('title', 'Activities')

@section('content')
    {{Session::get('message')}}
    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel__heading">
                <div class="panel__title">@lang('lite/global.add_an_activity')</div>
                <p>@lang('lite/global.add_an_activity_in_simple_steps')</p>
            </div>
            <div class="panel__body">
                <div class="create-form create-project-form edit-form">
                    {!! form_start($form) !!}
                    <div class="form__block" id="basics">
                        <div class="col-md-9">
                            <h2>@lang('lite/global.basics')</h2>
                            <div class="row">
                                {!! form_until($form,'target_groups') !!}
                            </div>
                        </div>
                        <div class="panel__nav">
                            <div id="nav-anchor"></div>
                            <nav>
                                <div id="activity-progress-bar"></div>
                                <ul>
                                    <li><a href="#basics">@lang('lite/global.basics')</a>
                                    </li>
                                    <li><a href="#location">@lang('lite/global.location')</a></li>
                                    <li>
                                        <a href="#involved-organisations">@lang('lite/global.involved_organisations')</a>
                                    </li>
                                    <li><a href="#results-and-reports">@lang('lite/global.results_and_reports')</a>
                                    </li>

                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="form__block" id="location">
                        <div class="col-md-9">
                            <h2>@lang('lite/global.location')</h2>
                            <div class="row">
                                {!! form_until($form,'location') !!}
                            </div>
                        </div>
                    </div>
                    <div class="form__block" id="involved-organisations">
                        <div class="col-md-9">
                            <h2>@lang('lite/global.involved_organisations')</h2>
                            <div class="row">
                                {!! form_until($form,"add_more_implementing") !!}
                            </div>
                        </div>
                    </div>
                    <div class="form__block" id="results-and-reports">
                        <div class="col-md-9">
                            <h2>@lang('lite/global.results_and_reports')</h2>
                            <div class="row">
                                {!! form_until($form,"annual_report") !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        {!! form_rest($form) !!}
                        <a href="{{ $form->getModel() ? route('lite.activity.show', $activityId) : route('lite.activity.index')}}"
                           class="pull-right btn-go-back">@lang('lite/global.cancel_and_go_back')</a>
                    </div>
                    {!! form_end($form) !!}
                    <div class="funding_organisations-container hidden"
                         data-prototype="{{ form_row($form->funding_organisations->prototype()) }}">
                    </div>
                    <div class="implementing_organisations-container hidden"
                         data-prototype="{{ form_row($form->implementing_organisations->prototype()) }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script type="text/javascript" src="{{ url('/js/jquery.scrollto.js') }}"></script>
    <script type="text/javascript" src="{{ url('/lite/js/createActivity.js') }}"></script>
    <script type="text/javascript" src="{{url('/lite/js/progressBar.js')}}"></script>
    <script type="text/javascript" src="{{url('/js/leaflet.js')}}"></script>
    <script type="text/javascript" src="{{url('/js/map.js')}}"></script>
    <script type="text/javascript" src="{{ url('/lite/js/location.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var countryDetails = [{!! $countryDetails !!}];
            Location.loadMap(countryDetails);
            Location.onCountryChange();
        })
    </script>
    <script type="text/javascript">
        var completedText = "{{strtolower(trans('lite/global.completed'))}}";
        ProgressBar.calculateProgressBar(completedText);
        ProgressBar.calculate();
        ProgressBar.onMapClicked();
        CreateActivity.editTextArea({!! empty(!$form->getModel()) !!});
        CreateActivity.addToCollection();
        CreateActivity.scroll();
    </script>
@stop
