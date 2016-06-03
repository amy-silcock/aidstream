@if(!emptyOrHasEmptyTemplate($recipientCountries))
    <div class="activity-element-wrapper">
        <div class="activity-element-list">
            <div class="activity-element-label">@lang('activityView.recipient_country')</div>
            <div class="activity-element-info">
                @foreach($recipientCountries as $recipientCountry)
                    <li>{!! getRecipientInformation($recipientCountry['country_code'], $recipientCountry['percentage'], 'Country') !!}</li>
                    <div class="toggle-btn">
                        <span class="show-more-info">Show more info</span>
                        <span class="hide-more-info hidden">Hide more info</span>
                    </div>
                    <div class="more-info hidden">
                        <div class="element-info">
                            <div class="activity-element-label">@lang('activityView.description')</div>
                            <div class="activity-element-info">
                                {!! checkIfEmpty(getFirstNarrative($recipientCountry)) !!}
                                @include('Activity.partials.viewInOtherLanguage', ['otherLanguages' => getOtherLanguages($recipientCountry['narrative'])])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <a href="{{route('activity.recipient-country.index', $id)}}" class="edit-element">edit</a>
        <a href="{{route('activity.delete-element', [$id, 'recipient_country'])}}" class="delete pull-right">remove</a>
    </div>
@endif
