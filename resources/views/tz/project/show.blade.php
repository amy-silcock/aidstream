@extends('tz.base.sidebar')

@section('title', 'Project')

@inject('getCode', 'App\Helpers\GetCodeName')

@section('content')
    <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
        @include('includes.response')
        <div class="element-panel-heading">
            <div>
                <span>
                    {{ $project->title ? $project->title[0]['narrative'] : 'No Title' }}
                    <a href="{{ route('project.edit', $project->id) }}" class="pull-right">Edit</a>
                    <br>
                    <a href="{{ route('change-project-defaults', $id) }}" class="pull-right">
                        Override Default Values
                    </a>
                </span>
                <div class="element-panel-heading-info">
                    <span>{{ $project->identifier['activity_identifier'] }}</span>
                    <span class="last-updated-date">Last Updated on: {{ changeTimeZone($project['updated_at'], 'M d, Y H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-8 col-lg-8 element-content-wrapper fullwidth-wrapper">
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

            <div class="panel panel-default panel-element-detail element-show">

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Project Identifier
                        </div>
                        <div class="activity-element-info">
                            {{$project->identifier['iati_identifier_text']}}
                        </div>
                    </div>
                </div>

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Project Title
                        </div>
                        <div class="activity-element-info">
                            {{$project->title[0]['narrative']}}
                        </div>
                    </div>
                </div>

                <div class="activity-element-wrapper">
                    @foreach ($project->description as $description)
                        @if(getVal($description, ['type']) == 1)
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    General Description
                                </div>
                                <div class="activity-element-info">
                                    {{$description['narrative'][0]['narrative']}}
                                </div>
                            </div>
                        @endif

                        @if(getVal($description, ['type']) == 2)
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    Objectives
                                </div>
                                <div class="activity-element-info">
                                    {{$description['narrative'][0]['narrative']}}
                                </div>
                            </div>
                        @endif

                        @if(getVal($description, ['type']) == 3)
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    Target Groups
                                </div>
                                <div class="activity-element-info">
                                    {{$description['narrative'][0]['narrative']}}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Project Status
                        </div>
                        <div class="activity-element-info">
                            {{ $getCode->getCodeListName('Activity','ActivityStatus', $project->activity_status) }}
                        </div>
                    </div>
                </div>

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Sector
                        </div>
                        <div class="activity-element-info">
                            {{ $getCode->getCodeListName('Activity','SectorCategory', getVal($project->sector, [0, 'sector_category_code'])) }}
                        </div>
                    </div>
                </div>

                @foreach ($project->activity_date as $date)
                    @if(getVal($date, ['type']) == 2)
                        <div class="activity-element-wrapper">
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    Start Date
                                </div>
                                <div class="activity-element-info">
                                    {{ formatDate($date['date']) }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(getVal($date, ['type']) == 4)
                        <div class="activity-element-wrapper">
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    End Date
                                </div>
                                <div class="activity-element-info">
                                    {{ formatDate($date['date']) }}
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Recipient Country
                        </div>
                        <div class="activity-element-info">
                            {{$getCode->getCodeListName('Organization','Country', $project->recipient_country[0]['country_code'])}}
                        </div>
                    </div>
                </div>

                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            Location
                        </div>
                        <div class="activity-element-info">
                            @foreach ($project->location as $location)
                                @if (getVal($location, ['administrative', 0, 'code']))
                                    <li>
                                        {{ getVal($location, ['administrative', 0, 'code']) }}, {{ getVal($location, ['administrative', 1, 'code']) }}
                                    </li>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                @if ($project->participating_organization)
                    <div class="activity-element-wrapper">
                        @foreach ($project->participating_organization as $participatingOrganization)
                            @if(getVal($participatingOrganization, ['narrative', 0, 'narrative']) && getVal($participatingOrganization, ['organization_role']) == "1")
                                <div class="activity-element-list">
                                    <div class="activity-element-label">
                                        Funding Organization
                                    </div>
                                    <div class="activity-element-info">
                                        <li>
                                            {{ getVal($participatingOrganization, ['narrative', 0, 'narrative']) }}
                                            , {{$getCode->getCodeListName('Activity','OrganisationType', getVal($participatingOrganization, ['organization_type']))}}
                                        </li>
                                    </div>
                                </div>
                            @endif

                            @if(getVal($participatingOrganization, ['narrative', 0, 'narrative']) && getVal($participatingOrganization, ['organization_role']) == 4)
                                <div class="activity-element-list">
                                    <div class="activity-element-label">
                                        Implementing Organization
                                    </div>
                                    <div class="activity-element-info">
                                        <li>
                                            {{ getVal($participatingOrganization, ['narrative', 0, 'narrative']) }}
                                            , {{$getCode->getCodeListName('Activity','OrganisationType', getVal($participatingOrganization, ['organization_type']))}}
                                        </li>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if ($project->resultDocuments())
                    <div class="activity-element-wrapper">
                        <div class="title">

                        </div>
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                Results/Outcomes Documents
                            </div>
                            <div class="activity-element-info">
                                <a href="{{ getVal($project->resultDocuments(), ['document_link', 'url']) }}">{{ getVal($project->resultDocuments(), ['document_link', 'title', 0, 'narrative', 0, 'narrative']) }}</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($project->annualReports())
                    <div class="activity-element-wrapper">
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                Annual Reports
                            </div>
                            <div class="activity-element-info">
                                <a href="{{ getVal($project->annualReports(), ['document_link', 'url']) }}">{{ getVal($project->annualReports(), ['document_link', 'title', 0, 'narrative', 0, 'narrative']) }}</a>
                            </div>
                        </div>
                    </div>
                @endif

                @include('tz.project.partials.add-budget')

        {{-- -------------  start of transactions --------------- --}}
                <div class="transactions-wrap">
                    <div class="title">Transactions</div>

                <div class="activity-element-wrapper">
                    @if(count($disbursement) > 0)
                        <div class="activity-element-label">
                            Disbursement
                        </div>
                        <a href="{{url(sprintf('project/%s/transaction/%s/edit', $project->id, 3))}}"
                           class="edit-element">
                            <span>Edit Disbursement</span>
                        </a>
                        <div>
                            {!! Form::open(['method' => 'POST', 'route' => ['transaction.destroy', $project->id, 3]]) !!}
                            {!! Form::submit('Delete', ['class' => 'pull-left delete-transaction']) !!}
                            {!! Form::close() !!}
                        </div>
                        @foreach($disbursement as $data)
                            <div class="activity-element-info">
                                <li>{{ number_format($data['value'][0]['amount']) }} {{ $data['value'][0]['currency'] }}, {{ formatDate(getVal($data, ['transaction_date', 0, 'date'])) }}</li>
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
                                            {{ number_format($data['value'][0]['amount']) }} {{ getVal($data, ['value', 0, 'currency']) }}
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
                                <div>
                                    <a href="javascript:void(0)" class="delete-transaction" data-route="{{ route('single.transaction.destroy', [$data['id']]) }}">Delete</a>
                                    {!! Form::open(['method' => 'POST', 'route' => ['single.transaction.destroy', $data['id']],'class' => 'hidden', 'role' => 'form', 'id' => 'transaction-delete-form']) !!}
                                    {!! Form::submit('Delete') !!}
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="activity-element-list">
                            <div class="title">Disbursement</div>
                            <a href="{{ url(sprintf('project/%s/transaction/%s/create', $project->id,3)) }}"
                               class="add-more"><span>Add Disbursement</span></a>
                        </div>
                    @endif
                </div>
                <div class="activity-element-wrapper">
                    @if(count($expenditure) > 0)
                        <div class="activity-element-label">
                            Expenditure
                        </div>
                        <a href="{{url(sprintf('project/%s/transaction/%s/edit', $project->id, 4))}}"
                           class="edit-element">
                                <span>
                                    Edit Expenditure
                                </span>
                        </a>
                        {{--<div>--}}
                            {{--{!! Form::open(['method' => 'POST', 'route' => ['transaction.destroy', $project->id, 4]]) !!}--}}
                            {{--{!! Form::submit('Delete', ['class' => 'pull-left delete-transaction']) !!}--}}
                            {{--{!! Form::close() !!}--}}
                        {{--</div>--}}
                        @foreach($expenditure as $data)
                            <div class="activity-element-info">
                                <li>{{ number_format($data['value'][0]['amount']) }} {{ $data['value'][0]['currency'] }}, {{ formatDate(getVal($data, ['transaction_date', 0, 'date'])) }}</li>
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
                                            {{ number_format($data['value'][0]['amount']) }} {{ getVal($data, ['value', 0, 'currency']) }}
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
                                <div>
                                    <a href="javascript:void(0)" class="delete-transaction" data-route="{{ route('single.transaction.destroy', [$data['id']]) }}">Delete</a>
                                    {!! Form::open(['method' => 'POST', 'route' => ['single.transaction.destroy', $data['id']],'class' => 'hidden', 'role' => 'form', 'id' => 'transaction-delete-form']) !!}
                                    {!! Form::submit('Delete') !!}
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="activity-element-list">
                            <div class="title">Expenditure</div>
                            <a href="{{ url(sprintf('project/%s/transaction/%s/create', $project->id,4)) }}"
                               class="add-more"><span>Add Expenditure</span></a>
                        </div>
                    @endif
                </div>
                <div class="activity-element-wrapper">
                    @if(count($incomingFund) > 0)
                        <div class="activity-element-label">
                            Incoming Funds
                        </div>
                        <a href="{{url(sprintf('project/%s/transaction/%s/edit', $project->id, 1))}}"
                           class="edit-element"><span>Edit Incoming Funds</span></a>
                        <div>
                            {!! Form::open(['method' => 'POST', 'route' => ['transaction.destroy', $project->id, 1]]) !!}
                            {!! Form::submit('Delete', ['class' => 'pull-left delete-transaction']) !!}
                            {!! Form::close() !!}
                        </div>
                        @foreach($incomingFund as $data)
                            <div class="activity-element-info">
                                <li>{{ number_format($data['value'][0]['amount']) }} {{ $data['value'][0]['currency'] }}, {{ formatDate(getVal($data, ['transaction_date', 0, 'date'])) }}</li>
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
                                            {{ number_format($data['value'][0]['amount']) }} {{ getVal($data, ['value', 0, 'currency']) }}
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
                                            Provider Organization
                                        </div>
                                        <div class="activity-element-info">
                                            {{$data['provider_organization'][0]['narrative'][0]['narrative']}}
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <a href="javascript:void(0)" class="delete-transaction" data-route="{{ route('single.transaction.destroy', [$data['id']]) }}">Delete</a>
                                    {!! Form::open(['method' => 'POST', 'route' => ['single.transaction.destroy', $data['id']],'class' => 'hidden', 'role' => 'form', 'id' => 'transaction-delete-form']) !!}
                                    {!! Form::submit('Delete') !!}
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="activity-element-list">
                            <div class="title">Incoming Funds</div>
                            <a href="{{ url(sprintf('project/%s/transaction/%s/create', $project->id,1)) }}"
                               class="add-more"><span>Add Incoming Funds</span></a>
                        </div>
                    @endif

                </div>
            </div>
          {{-- -------------  end of transactions --------------- --}}

            </div>
        </div>
    </div>
    <div class="hidden">
        <div class="modal" id="transactionDeleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index: 9999">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">
                            Confirm Delete?
                        </h4>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this transaction?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn_del" type="button" id="yes-delete">Yes</button>
                        <button class="btn btn-default" type="button" data-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('/js/tz/transaction.js') }}"></script>
    <script src="{{ asset('/js/tz/transactionDelete.js') }}"></script>
@endsection
