@extends('tz.base.sidebar')

@section('title', 'Project')

@inject('getCode', 'App\Helpers\GetCodeName')

@section('content')
    <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
        @include('includes.response')
        <div class="element-panel-heading">
            <div>
                <span>{{ $project->title ? $project->title[0]['narrative'] : 'No Title' }}</span>
                <div class="element-panel-heading-info">
                    <span>{{ $project->identifier['activity_identifier'] }}</span>
                    <span class="last-updated-date">Last Updated on: {{ changeTimeZone($project['updated_at'], 'M d, Y H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-8 col-lg-8 element-content-wrapper">
            <div class="activity-status activity-status-{{ $statusLabel[$activityWorkflow] }}">
                <ol>
                    @foreach($statusLabel as $key => $value)
                        @if($key == $activityWorkflow)
                            <li class="active"><span>{{ $value }}</span></li>
                        @else
                            <li><span>{{ $value }}</span></li>
                        @endif
                    @endforeach
                </ol>
                @include('tz.project.partials.workflow')
            </div>

            <a href="{{ route('change-project-defaults', $id) }}" class="pull-right">
                <span class="glyphicon glyphicon-triangle-left"></span>Override Activity Default
            </a>
            <div class="panel panel-default panel-element-detail element-show">

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Project Identifier:
                        </div>
                        <div class="activity-element-info">
                            {{$project->identifier['iati_identifier_text']}}
                        </div>
                    </div>
                </div>

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Project Title:
                        </div>
                        <div class="activity-element-info">
                            {{$project->title[0]['narrative']}}
                        </div>
                    </div>
                </div>

                <div class="title">
                    Description
                </div>
                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            General Description:
                        </div>
                        <div class="activity-element-info">
                            @if(getVal($project->description, [0, 'type']) == 1)
                                {{$project->description[0]['narrative'][0]['narrative']}}
                            @else
                                &nbsp;
                            @endif
                        </div>
                    </div>
                </div>

                @if(getVal($project->description, [0, 'type']) == 2)
                    <div class="activity-element-wrapper">
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                Objectives:
                            </div>
                            <div class="activity-element-info">
                                {{$project->description[0]['narrative'][0]['narrative']}}
                            </div>
                        </div>
                    </div>
                @endif

                @if(getVal($project->description, [0, 'type']) == 3)
                    <div class="activity-element-wrapper">
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                Target Groups:
                            </div>
                            <div class="activity-element-info">
                                {{$project->description[0]['narrative'][0]['narrative']}}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Project Status:
                        </div>
                        <div class="activity-element-info">
                            {{ $getCode->getActivityCodeName('ActivityStatus', $project->activity_status) }}
                        </div>
                    </div>
                </div>

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Sector:
                        </div>
                        <div class="activity-element-info">
                            {{ $getCode->getActivityCodeName('SectorCategory', getVal($project->sector, [0, 'sector_category_code'])) }}
                        </div>
                    </div>
                </div>

                @if(getVal($project->activity_date, [0, 'type']) == 2)
                    <div class="activity-element-wrapper">
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                Start Date:
                            </div>
                            <div class="activity-element-info">
                                {{$project->activity_date[0]['date']}}
                            </div>
                        </div>
                    </div>
                @endif

                @if(getVal($project->activity_date, [0, 'type']) == 4)
                    <div class="activity-element-wrapper">
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                End Date:
                            </div>
                            <div class="activity-element-info">
                                {{ getVal($project->activity_date, [0, 'date']) }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="title">
                    Participating Organization
                </div>
                @foreach ($project->participating_organization as $participatingOrganization)
                    @if(getVal($participatingOrganization, ['organization_role']) == "1")
                        <div class="activity-element-wrapper">
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    Funding Organization Name:
                                </div>
                                <div class="activity-element-info">
                                    {{ getVal($participatingOrganization, ['narrative', 0, 'narrative']) }}
                                </div>
                            </div>
                        </div>

                        <div class="activity-element-wrapper">
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    Funding Organization Type:
                                </div>
                                <div class="activity-element-info">
                                    {{$getCode->getActivityCodeName('OrganisationType', getVal($participatingOrganization, ['organization_type']))}}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(getVal($participatingOrganization, ['organization_role']) == 4)
                        <div class="activity-element-wrapper">
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    Implementing Organization Name:
                                </div>
                                <div class="activity-element-info">
                                    {{ getVal($participatingOrganization, ['narrative', 0, 'narrative']) }}
                                </div>
                            </div>
                        </div>

                        <div class="activity-element-wrapper">
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    Implementing Organization Type:
                                </div>
                                <div class="activity-element-info">
                                    {{$getCode->getActivityCodeName('OrganisationType', getVal($participatingOrganization, ['organization_type']))}}
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Recipient Country:
                        </div>
                        <div class="activity-element-info">
                            {{$getCode->getOrganizationCodeName('Country', $project->recipient_country[0]['country_code'])}}
                        </div>
                    </div>
                </div>

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Recipient Region:
                        </div>
                        <div class="activity-element-info">
                            {{$getCode->getActivityCodeName('Region', $project->recipient_region[0]['region_code'])}}
                        </div>
                    </div>
                </div>

                <div class="title">
                    Transactions
                </div>
                @if(count($disbursement) > 0)
                    <div class="title">
                        Disbursement
                        <a href="{{url(sprintf('project/%s/transaction/%s/edit', $project->id,3))}}"><span>Edit a Disbursement</span></a>
                    </div>
                    @foreach($disbursement as $data)
                        <div>
                            {!! Form::open(['route' => ['transaction.destroy', $project->id, $data['id']]]) !!}
                            {!! Form::submit('Delete') !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                Disbursement
                                <div class="activity-element-info">
                                    <li>{{ $getCode->getOrganizationCodeName('Currency', $data['value'][0]['currency']) }}</li>
                                    <div class="toggle-btn">
                                        <span class="show-more-info">Show more info</span>
                                        <span class="hide-more-info hidden">Hide more info</span>
                                    </div>
                                    <div class="more-info hidden">
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Internal Ref:
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['reference']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Transaction Value:
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['value'][0]['amount']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Transaction Date:
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['transaction_date'][0]['date']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Description
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['description'][0]['narrative'][0]['narrative']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Receiver Organization
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['provider_organization'][0]['narrative'][0]['narrative']}}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="activity-element-wrapper">
                        <div class="title">Disbursement</div>
                        <a href="{{ url(sprintf('project/%s/transaction/%s/create', $project->id,3)) }}"><span>Add a Disbursement</span></a>
                    </div>
                @endif

                @if(count($expenditure) > 0)
                    <div class="title">
                        Expenditure
                        <a href="{{url(sprintf('project/%s/transaction/%s/edit', $project->id,4))}}"><span>Edit a Expenditure</span></a>
                    </div>
                    @foreach($expenditure as $data)
                        <div>
                            {!! Form::open(['route' => ['transaction.destroy', $project->id, $data['id']]]) !!}
                            {!! Form::submit('Delete') !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                Expenditure
                                <div class="activity-element-info">
                                    <li>{{ $getCode->getOrganizationCodeName('Currency', $data['value'][0]['currency']) }}</li>
                                    <div class="toggle-btn">
                                        <span class="show-more-info">Show more info</span>
                                        <span class="hide-more-info hidden">Hide more info</span>
                                    </div>
                                    <div class="more-info hidden">
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Internal Ref:
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['reference']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Transaction Value:
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['value'][0]['amount']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Transaction Date:
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['transaction_date'][0]['date']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Description
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['description'][0]['narrative'][0]['narrative']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Receiver Organization
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['provider_organization'][0]['narrative'][0]['narrative']}}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="activity-element-wrapper">
                        <div class="title">Expenditure</div>
                        <a href="{{ url(sprintf('project/%s/transaction/%s/create', $project->id,4)) }}"><span>Add a Expenditure</span></a>
                    </div>
                @endif

                @if(count($incomingFund) > 0)
                    <div class="title">
                        Incoming Fund
                        <a href="{{url(sprintf('project/%s/transaction/%s/edit', $project->id,1))}}"><span>Edit a Incoming Fund</span></a>
                    </div>
                    @foreach($incomingFund as $data)
                        <div>
                            {!! Form::open(['route' => ['transaction.destroy', $project->id, $data['id']]]) !!}
                            {!! Form::submit('Delete') !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                Incoming Fund
                                <div class="activity-element-info">
                                    <li>{{ $getCode->getOrganizationCodeName('Currency', $data['value'][0]['currency']) }}</li>
                                    <div class="toggle-btn">
                                        <span class="show-more-info">Show more info</span>
                                        <span class="hide-more-info hidden">Hide more info</span>
                                    </div>
                                    <div class="more-info hidden">
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Internal Ref:
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['reference']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Transaction Value:
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['value'][0]['amount']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Transaction Date:
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['transaction_date'][0]['date']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Description
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['description'][0]['narrative'][0]['narrative']}}
                                            </div>
                                        </div>
                                        <div class="element-info">
                                            <div class="activity-element-label">
                                                Receiver Organization
                                            </div>
                                            <div class="activity-element-info">
                                                {{$data['provider_organization'][0]['narrative'][0]['narrative']}}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="activity-element-wrapper">
                        <div class="title">Incoming Fund</div>
                        <a href="{{ url(sprintf('project/%s/transaction/%s/create', $project->id,1)) }}"><span>Add a Incoming Fund</span></a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
