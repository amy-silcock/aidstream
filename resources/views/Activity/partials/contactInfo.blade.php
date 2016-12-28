@if(!emptyOrHasEmptyTemplate($contactInfo))
    <div class="activity-element-wrapper">
        <div class="title">@lang('element.contact_info') @if(array_key_exists('Contact Info',$errors)) <i class='imported-from-xml'>icon</i>@endif </div>
        @foreach(groupContactInformation($contactInfo) as $key => $contactInformation)
            <div class="activity-element-list">
                <div class="activity-element-label">{{ checkIfEmpty($getCode->getCodeNameOnly('ContactType' , $contactInformation[0]['type']), "General Enquiries") }}</div>
                <div class="activity-element-info">
                    @foreach($contactInformation as $information)
                        <li>
                            {!! getFirstNarrative($information['person_name'][0]) !!}
                            @include('Activity.partials.viewInOtherLanguage', ['otherLanguages' => getOtherLanguages($information['person_name'][0]['narrative'])])
                            ,{!! getFirstNarrative($information['organization'][0]) !!}
                            @include('Activity.partials.viewInOtherLanguage', ['otherLanguages' => getOtherLanguages($information['organization'][0]['narrative'])])
                        </li>

                        <div class="toggle-btn">
                            <span class="show-more-info">@lang('global.show_more_info')</span>
                            <span class="hide-more-info hidden">@lang('global.hide_more_info')</span>
                        </div>
                        <div class="more-info hidden">
                            <div class="element-info">
                                <div class="activity-element-label">@lang('elementForm.department')</div>
                                <div class="activity-element-info">
                                    {!!  getFirstNarrative($information['department'][0])  !!}
                                    @include('Activity.partials.viewInOtherLanguage', ['otherLanguages' => getOtherLanguages($information['department'][0]['narrative'])])
                                </div>

                            </div>
                            <div class="element-info">
                                <div class="activity-element-label">@lang('elementForm.job_title')</div>
                                <div class="activity-element-info">
                                    {!! getFirstNarrative($information['job_title'][0]) !!}
                                    @include('Activity.partials.viewInOtherLanguage', ['otherLanguages' => getOtherLanguages($information['job_title'][0]['narrative'])])
                                </div>

                            </div>
                            <div class="element-info">
                                <div class="activity-element-label">@lang('elementForm.telephone')</div>
                                <div class="activity-element-info">{!! getContactInfo('telephone', $information['telephone'])  !!}</div>
                            </div>
                            <div class="element-info">
                                <div class="activity-element-label">@lang('elementForm.email')</div>
                                <div class="activity-element-info">{!! getContactInfo('email', $information['email']) !!}</div>
                            </div>
                            <div class="element-info">
                                <div class="activity-element-label">@lang('elementForm.website')</div>
                                <div class="activity-element-info">{!! getContactInfo('website' , $information['website']) !!}</div>
                            </div>
                            <div class="element-info">
                                <div class="activity-element-label">@lang('elementForm.mailing_address')</div>
                                <div class="activity-element-info">
                                    {!!  getFirstNarrative($information['mailing_address'][0])  !!}
                                    @include('Activity.partials.viewInOtherLanguage' ,['otherLanguages' => getOtherLanguages($information['mailing_address'][0]['narrative'])])
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <a href="{{route('activity.contact-info.index', $id)}}" class="edit-element">@lang('global.edit')</a>
        <a href="{{route('activity.delete-element', [$id, 'contact_info'])}}" class="delete pull-right">@lang('global.remove')</a>
    </div>
@endif
