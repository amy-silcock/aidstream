@extends('app')

@section('title', 'Reporting Organisation')

@section('content')
    <div class="container main-container">
        <div class="row">
            @include('includes.side_bar_menu')
            <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
                @include('includes.response')
                <div class="panel-content-heading">
                    <div>Reporting Organisation
                    <div class="panel-action-btn">
                        <a href="{{route('organization.show', $organizationId)}}" class="btn btn-primary">View Organisation Data
                        </a>
                    </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-8 col-lg-8 element-content-wrapper">
                    <div class="panel panel-default panel-element-detail">
                        <div class="panel-body">
                            <div class="panel-default">
                                <div class="panel-body panel-element-body">
                                    <div class="col-md-12 clearfix">
                                        <div class="col-xs-12 col-sm-4 col-lg-3">Identifier:</div>
                                        <div class="col-xs-12 col-sm-8 col-lg-9">{{ $reportingOrganization[0]['reporting_organization_identifier'] }}</div>
                                    </div>
                                    <div class="col-md-12 clearfix">
                                        <div class="col-xs-12 col-sm-4 col-lg-3">Type:</div>
                                        <div class="col-xs-12 col-sm-8 col-lg-9">{{ $reportingOrganization[0]['reporting_organization_type'] }}</div>
                                    </div>
                                    <div class="col-md-12 clearfix">
                                        <div class="col-xs-12 col-sm-4 col-lg-3">Name:</div>
                                        <div class="col-xs-12 col-sm-8 col-lg-9">
                                            {{--*/ $narratives = [] /*--}}
                                            @foreach($reportingOrganization[0]['narrative'] as $narrative)
                                                {{--*/ $narratives[] = $narrative['narrative'] . ($narrative['language'] ? '[' . $narrative['language'] . ']' : '') /*--}}
                                            @endforeach
                                            {{ implode('<br />', $narratives) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="activity-description"><span>Reporting organisation information can be updated in <a href="{{ route('settings.index') }}">Settings</a>.</span></div>
                        </div>
                    </div>
                </div>
                @include('includes.menu_org')
            </div>
        </div>
    </div>
@endsection
