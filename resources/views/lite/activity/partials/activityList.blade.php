<div class="panel panel-default panel-element-detail element-show">
    {{--<div class="activity__detail">--}}
    {{--<div class="activity__element__list">--}}
    {{--<div class="activity__element--label">--}}
    {{--@lang('lite/elementForm.activity_identifier')--}}
    {{--</div>--}}
    {{--<div class="activity__element--info">--}}
    {{--{{getVal($activity->identifier,['iati_identifier_text'])}}--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}

    @foreach (getVal($activity->toArray(), ['description'], []) as $description)
        @if(getVal($description, ['type']) == 1)
            <div class="activity__detail">
                <div class="activity__element__list">
                    <h3>
                        @lang('lite/elementForm.general_description')
                    </h3>
                    <div class="activity__element--info">
                        {{$description['narrative'][0]['narrative']}}
                    </div>
                </div>
            </div>
        @endif
        @if(getVal($description, ['type']) == 2)
            <div class="activity__detail">
                <div class="activity__element__list">
                    <h3>
                        @lang('lite/elementForm.objectives')
                    </h3>
                    <div class="activity__element--info">
                        {{$description['narrative'][0]['narrative']}}
                    </div>
                </div>
            </div>
        @endif
        @if(getVal($description, ['type']) == 3)
            <div class="activity__detail">
                <div class="activity__element__list">
                    <h3>
                        @lang('lite/elementForm.target_groups')
                    </h3>
                    <div class="activity__element--info">
                        {{$description['narrative'][0]['narrative']}}
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{--<div class="activity__detail">--}}
        {{--<div class="activity__element__list">--}}
            {{--<h3>--}}
                {{--@lang('lite/elementForm.activity_status')--}}
            {{--</h3>--}}
            {{--<div class="activity__element--info">--}}
                {{--{{ $getCode->getCodeNameOnly('ActivityStatus', $activity->activity_status) }}--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="activity__detail">
        <div class="activity__element__list">
            <h3>
                @lang('lite/elementForm.sector')
            </h3>
            <div class="activity__element--info">
                {{ $getCode->getCodeNameOnly('Sector', getVal((array)$activity->sector, [0, 'sector_code']),-7)}}
            </div>
        </div>
    </div>

    @foreach (getVal($activity->toArray(), ['activity_date'], []) as $date)
        @if(getVal($date, ['type']) == 2)
            <div class="activity__detail">
                <div class="activity__element__list">
                    <h3>
                        @lang('lite/elementForm.start_date')
                    </h3>
                    <div class="activity__element--info">
                        {{ formatDate($date['date']) }}
                    </div>
                </div>
            </div>
        @endif

        @if(getVal($date, ['type']) == 4)
            <div class="activity__detail">
                <div class="activity__element__list">
                    <h3>
                        @lang('lite/elementForm.end_date')
                    </h3>
                    <div class="activity__element--info">
                        {{ formatDate($date['date']) }}
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <div class="activity__detail">
        <div class="activity__element__list">
            <h3>
                @lang('lite/elementForm.recipient_country')
            </h3>
            <div class="activity__element--info">
                @foreach((array)$activity->recipient_country as $index=> $country)
                    {{$getCode->getCodeNameOnly('Country', $country['country_code'],-4,'Organization')}}
                @endforeach
            </div>
        </div>
    </div>
    @if ($activity->participating_organization)
        @foreach ($activity->participating_organization as $participatingOrganization)
            @if(getVal($participatingOrganization, ['narrative', 0, 'narrative']) && getVal($participatingOrganization, ['organization_role']) == "1")
                <div class="activity__detail">
                    <div class="activity__element__list">
                        <h3>
                            @lang('lite/elementForm.funding_organisation')
                        </h3>
                        <div class="activity__element--info">
                            <li>
                                {{ getVal($participatingOrganization, ['narrative', 0, 'narrative']) }}
                                , {{$getCode->getCodeNameOnly('OrganisationType', getVal($participatingOrganization, ['organization_type']))}}
                            </li>
                        </div>
                    </div>
                </div>
            @endif

            @if(getVal($participatingOrganization, ['narrative', 0, 'narrative']) && getVal($participatingOrganization, ['organization_role']) == 4)
                <div class="activity__detail">
                    <div class="activity__element__list">
                        <h3>
                            @lang('lite/elementForm.implementing_organisation')
                        </h3>
                        <div class="activity__element--info">
                            <li>
                                {{ getVal($participatingOrganization, ['narrative', 0, 'narrative']) }}
                                , {{$getCode->getCodeNameOnly('OrganisationType', getVal($participatingOrganization, ['organization_type']))}}
                            </li>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
@endif
@if(array_key_exists('outcomes_document',$documentLinks))
    <div class="activity__detail">
        <div class="activity__element__list">
            <h3>
                @lang('lite/elementForm.results_outcomes_documents')
            </h3>
            <div class="activity__element--info">
                @foreach((array)getVal($documentLinks,['outcomes_document'],[]) as $index => $value)
                    <li>
                        @if(($url = getVal($value,['document_url'])) != "")
                            <a href="{{$url}}">{{getVal($value,['document_title'])}}</a>
                        @else
                            {{getVal($value,['document_title'])}}
                        @endif
                    </li>
                @endforeach
            </div>
        </div>
    </div>
@endif

@if(array_key_exists('annual_report',$documentLinks))
    <div class="activity__detail">
        <div class="activity__element__list">
            <h3>
                @lang('lite/elementForm.annual_reports')
            </h3>
            <div class="activity__element--info">
                @foreach((array) getVal($documentLinks,['annual_report'],[]) as $index => $value)
                    <li>
                        @if(($url = getVal($value,['document_url'])) != "")
                            <a href="{{$url}}">{{getVal($value,['document_title'])}}</a>
                        @else
                            {{getVal($value,['document_title'])}}
                        @endif
                    </li>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @include('lite.activity.partials.budget')
    @include('lite.activity.partials.transaction')
    </div>
