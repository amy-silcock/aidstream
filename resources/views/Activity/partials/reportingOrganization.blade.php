@if(!emptyOrHasEmptyTemplate($reportingOrganization))
    <div class="activity-element-wrapper">
        <div class="activity-element-list">
            <div class="activity-element-label">@lang('activityView.reporting_organization')</div>
            <div class="activity-element-info">
                <li>{!! checkIfEmpty(getFirstNarrative($reportingOrganization)) !!}</li>
                @include('Activity.partials.viewInOtherLanguage' ,['otherLanguages' => getOtherLanguages($reportingOrganization['narrative'])])
                <div class="toggle-btn">
                    <span class="show-more-info">Show more info</span>
                    <span class="hide-more-info hidden">Hide more info</span>
                </div>

                <div class="more-info hidden">
                    <div class="element-info">
                        <div class="activity-element-label">@lang('activityView.organization_identifier')</div>
                        <div class="activity-element-info">{!! checkIfEmpty($reportingOrganization['reporting_organization_identifier']) !!}</div>
                    </div>
                    <div class="element-info">
                        <div class="activity-element-label">@lang('activityView.organization_type')</div>
                        <div class="activity-element-info">{!! substr($getCode->getOrganizationCodeName('OrganizationType', $reportingOrganization['reporting_organization_type']), 0, -4) !!}</div>
                    </div>
                </div>
            </div>
        </div>
        <a href="{{ route('activity.reporting-organization.index' , $id) }}" class="edit-element"></a>
    </div>
@endif
