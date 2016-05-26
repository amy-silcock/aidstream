@extends('tz.base.sidebar')

@section('title', 'Edit Activity')

@inject('code', 'App\Helpers\GetCodeName')

@section('content')
    <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
        @include('includes.response')
        <div class="panel panel-default panel-create">
            <div class="panel-content-heading panel-title-heading">
                <div>Edit a new Project</div>

                {{--<div>{{($duplicate) ? 'Duplicate Activity' : 'Add Activity'}}</div>--}}
            </div>
            <div class="panel-body">
                <div class="create-activity-form">

                    <div class="panel panel-default">
                        <div class="panel-body">
                            {!! Form::model($project, ['method' => 'post', 'route' => 'project.store', 'role' => 'form']) !!}
                            <div id="basic-info">
                                <div class="col-sm-6">
                                    {!! Form::label('identifier', 'Project Identifier', ['class' => 'control-label required']) !!}
                                    {!! Form::text('identifier', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>

                                <div class="col-sm-6">
                                    {!! Form::label('title', 'Project Title', ['class' => 'control-label required']) !!}
                                    {!! Form::text('title', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>

                                <div class="col-sm-6">
                                    {!! Form::label('description', 'Project Description', ['class' => 'control-label required']) !!}
                                    {!! Form::text('description', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>

                                <div class="col-sm-6">
                                    {!! Form::label('activity_status', 'Project Status', ['class' => 'control-label required']) !!}
                                    {!! Form::text('activity_status', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>

                                <a class="btn btn-primary btn-sm pull-right" id="projectNextStep" href="javascript:void(0)">
                                    Next
                                    <span class="glyphicon glyphicon-arrow-right"></span>
                                </a>
                            </div>

                            <div id="other-info" class="hidden">
                                <div class="col-sm-6">
                                    {!! Form::label('transaction', 'Transaction', ['class' => 'control-label required']) !!}
                                    {!! Form::text('transaction', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>

                                <div class="col-sm-6">
                                    {!! Form::label('abc', 'Project Title', ['class' => 'control-label required']) !!}
                                    {!! Form::text('abc', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>

                                {{--<div class="col-sm-6">--}}
                                {{--{!! Form::label('description', 'Project Description', ['class' => 'control-label required']) !!}--}}
                                {{--{!! Form::text('description', null, ['class' => 'form-control', 'required' => 'required']) !!}--}}
                                {{--</div>--}}

                                {{--<div class="col-sm-6">--}}
                                {{--{!! Form::label('activity_status', 'Project Status', ['class' => 'control-label required']) !!}--}}
                                {{--{!! Form::text('activity_status', null, ['class' => 'form-control', 'required' => 'required']) !!}--}}
                                {{--</div>--}}

                                <a class="btn btn-primary btn-sm pull-left" id="projectPreviousStep" href="javascript:void(0)">
                                    Back
                                    <span class="glyphicon glyphicon-arrow-left"></span>
                                </a>

                                {!! Form::submit('Create Project', ['class' => 'btn btn-primary btn-create pull-right']) !!}
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        {{--@if(!$duplicate)--}}
        {{--<div class="panel panel-default panel-element-detail panel-activity-default">--}}
        {{--<div class="panel-body">--}}
        {{--<div class="activity-description"><span>You can change the Activity Default Field Values once after you create an activity.</span></div>--}}
        {{--<div class="panel-default">--}}
        {{--<div class="panel-heading">@lang('trans.activity_default')</div>--}}
        {{--<div class="panel-body panel-element-body">--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Default Language:</div>--}}
        {{--<div class="col-md-6">{{$code->getActivityCodeName('Language', $defaultFieldValues[0]['default_language']) }}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Default Currency:</div>--}}
        {{--<div class="col-md-6">{{$code->getActivityCodeName('Currency', $defaultFieldValues[0]['default_currency'])}}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Hierarchy:</div>--}}
        {{--<div class="col-md-6">{{ $defaultFieldValues[0]['default_hierarchy'] }}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Reporting Organisation Identifier:</div>--}}
        {{--<div class="col-md-6" id="reporting_organization_identifier">{{ $reportingOrganization[0]['reporting_organization_identifier']  }}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Reporting Organisation Type:</div>--}}
        {{--<div class="col-md-6">{{ $code->getOrganizationCodeName('OrganizationType', $reportingOrganization[0]['reporting_organization_type'])}}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Reporting Organisation Name:</div>--}}
        {{--<div class="col-md-6">{{ $reportingOrganization[0]['narrative'][0]['narrative'] }}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Reporting Organisation language:</div>--}}
        {{--<div class="col-md-6">{{ $code->getActivityCodeName('Language', $reportingOrganization[0]['narrative'][0]['language']) }}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Collaboration Type:</div>--}}
        {{--<div class="col-md-6">{{ $code->getActivityCodeName('CollaborationType', $defaultFieldValues[0]['default_collaboration_type']) }}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Default Flow Type:</div>--}}
        {{--<div class="col-md-6">{{ $code->getActivityCodeName('FlowType', $defaultFieldValues[0]['default_flow_type']) }}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Default Finance Type:</div>--}}
        {{--<div class="col-md-6">{{ $code->getActivityCodeName('FinanceType', $defaultFieldValues[0]['default_finance_type']) }}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Default Aid Type:</div>--}}
        {{--<div class="col-md-6">{{ $code->getActivityCodeName('AidType', $defaultFieldValues[0]['default_aid_type']) }}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Default Tied Status:</div>--}}
        {{--<div class="col-md-6">{{ $code->getActivityCodeName('TiedStatus', $defaultFieldValues[0]['default_tied_status']) }}</div>--}}
        {{--</div>--}}
        {{--<div class="col-xs-12 col-sm-6 col-md-6">--}}
        {{--<div class="col-md-6">Linked Data uri:</div>--}}
        {{--<div class="col-md-6">{{ $defaultFieldValues[0]['linked_data_uri']  }}</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--@else--}}
        {{--<div class="activity-description duplicate-activity-description"><span>You are duplicating activity:--}}
        {{--Please note that Transactions and Results are not duplicated. You have to manually add Transactions and Results to the duplicated activity.</span></div>--}}
        {{--@endif--}}
    </div>
@stop

@section('script')
    <script src="{{ asset('/js/tz/project.js') }}"></script>
@stop