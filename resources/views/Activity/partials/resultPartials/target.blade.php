<div class="view-period-info hidden">
    <div class="element-info">
        <div class="activity-element-label">@lang('activityView.location_ref'):</div>
        <div class="activity-element-info">{!! getTargetAdditionalDetails($period[$type] , 'locationRef')!!}</div>
    </div>
    <div class="element-info">
        <div class="activity-element-label">@lang('activityView.dimension')</div>
        <div class="activity-element-info">
            {!! getTargetAdditionalDetails($period[$type] , 'dimension')!!}
        </div>
    </div>
    <div class="element-info">
        <div class="activity-element-label">@lang('Description')</div>
        <div class="activity-element-info">
            {!! getFirstNarrative($period[$type]['comment'][0]) !!}
            @include('Activity.partials.viewInOtherLanguage', ['otherLanguages' => getOtherLanguages($period['target']['comment'][0]['narrative'])])
        </div>
    </div>
</div>
