@extends('app')

@section('title', trans('title.create_activity'))
@inject('code', 'App\Helpers\GetCodeName')
@section('content')
    <div class="container main-container">
        <div class="row">
            @include('includes.side_bar_menu')
            <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
                @include('includes.response')
                @include('includes.breadcrumb')
                <div class="panel panel-default panel-create">
                    <div class="panel-content-heading panel-title-heading">
                        <div>{{($duplicate) ? trans('global.duplicate_activity') : trans('global.add_activity')}}</div>
                    </div>
                    <div class="panel-body">
                        <div class="create-activity-form">
                            {!! form_start($form) !!}
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    {!! form_rest($form) !!}
                                </div>
                            </div>
                            {!! form_end($form) !!}
                        </div>
                        <div class="col-md-6 hidden" id="reporting_organization_identifier">{{ $reportingOrganization[0]['reporting_organization_identifier']  }}</div>
                    </div>
                </div>
                @if(!$duplicate)
                    <div class="panel panel-default panel-element-detail panel-activity-default">
                        <div class="panel-body">
                            <div class="activity-description"><span>@lang('global.activity_default_field_change')</span></div>
                            <div class="panel-default">
                                <div class="panel-heading">@lang('trans.activity_default')</div>
                                <div class="panel-body panel-element-body">
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.default_language'):</div>
                                        <div class="col-md-6">{{$code->getActivityCodeName('Language', $defaultFieldValues[0]['default_language']) }}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.default_currency'):</div>
                                        <div class="col-md-6">{{$code->getActivityCodeName('Currency', $defaultFieldValues[0]['default_currency'])}}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.hierarchy'):</div>
                                        <div class="col-md-6">{{ $defaultFieldValues[0]['default_hierarchy'] }}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.reporting_organisation_identifier'):</div>
                                        <div class="col-md-6" id="reporting_organization_identifier">{{ $reportingOrganization[0]['reporting_organization_identifier']  }}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.reporting_organisation_type'):</div>
                                        <div class="col-md-6">{{ $code->getOrganizationCodeName('OrganizationType', $reportingOrganization[0]['reporting_organization_type'])}}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.reporting_organisation_name'):</div>
                                        <div class="col-md-6">{{ $reportingOrganization[0]['narrative'][0]['narrative'] }}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.reporting_organisation_language'):</div>
                                        <div class="col-md-6">{{ $code->getActivityCodeName('Language', $reportingOrganization[0]['narrative'][0]['language']) }}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.collaboration_type'):</div>
                                        <div class="col-md-6">{{ $code->getActivityCodeName('CollaborationType', $defaultFieldValues[0]['default_collaboration_type']) }}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.default_flow_type'):</div>
                                        <div class="col-md-6">{{ $code->getActivityCodeName('FlowType', $defaultFieldValues[0]['default_flow_type']) }}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.default_finance_type'):</div>
                                        <div class="col-md-6">{{ $code->getActivityCodeName('FinanceType', $defaultFieldValues[0]['default_finance_type']) }}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.default_aid_type'):</div>
                                        <div class="col-md-6">{{ $code->getActivityCodeName('AidType', $defaultFieldValues[0]['default_aid_type']) }}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.default_tied_status'):</div>
                                        <div class="col-md-6">{{ $code->getActivityCodeName('TiedStatus', $defaultFieldValues[0]['default_tied_status']) }}</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="col-md-6">@lang('elementForm.linked_data_uri'):</div>
                                        <div class="col-md-6">{{ $defaultFieldValues[0]['linked_data_uri']  }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="activity-description duplicate-activity-description"><span>@lang('global.duplication_activity_info')</span></div>
                @endif
            </div>
        </div>
    </div>
@endsection
