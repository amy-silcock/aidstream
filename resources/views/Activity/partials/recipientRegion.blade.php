@if(!emptyOrHasEmptyTemplate($recipientRegions))
    <div class="activity-element-wrapper">
        <div class="activity-element-list">
            <div class="activity-element-label">@lang('element.recipient_region') @if(array_key_exists('Recipient Region',$errors)) <i class='imported-from-xml'>icon</i>@endif </div>
            <div class="activity-element-info">
                @foreach($recipientRegions as $recipientRegion)
                    <li>{!! getRecipientInformation($recipientRegion['region_code'], $recipientRegion['percentage'], 'Region') !!}</li>
                    <div class="toggle-btn">
                        <span class="show-more-info">@lang('global.show_more_info')</span>
                        <span class="hide-more-info hidden">@lang('global.hide_more_info')</span>
                    </div>
                    <div class="more-info hidden">
                        <div class="element-info">
                            <div class="activity-element-label">@lang('elementForm.region_vocabulary')</div>
                            <div class="activity-element-info">{{ $recipientRegion['region_vocabulary'] . '-' . substr($getCode->getActivityCodeName('RegionVocabulary', $recipientRegion['region_vocabulary']) , 0 , -4) }}</div>
                        </div>
                        @if(session('version') != 'V201')
                            <div class="element-info">
                                <div class="activity-element-label">@lang('elementForm.vocabulary_uri')</div>
                                <div class="activity-element-info">{!!  getClickableLink(getVal($recipientRegion, ['vocabulary_uri'])) !!}</div>
                            </div>
                        @endif
                        <div class="element-info">
                            <div class="activity-element-label">@lang('elementForm.description')</div>
                            <div class="activity-element-info">
                                {!! getFirstNarrative($recipientRegion) !!}
                                @include('Activity.partials.viewInOtherLanguage', ['otherLanguages' => getOtherLanguages($recipientRegion['narrative'])])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <a href="{{route('activity.recipient-region.index', $id)}}" class="edit-element">@lang('global.edit')</a>
        <a href="{{route('activity.delete-element', [$id, 'recipient_region'])}}" class="delete pull-right">@lang('global.remove')</a>
    </div>
@endif
