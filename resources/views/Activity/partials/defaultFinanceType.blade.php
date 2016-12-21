@if(!empty($defaultFinanceType))
    <div class="activity-element-wrapper">
        <div class="activity-element-list">
            <div class="activity-element-label">@lang('element.default_finance_type') @if(array_key_exists('Default Finance Type',$errors)) <i class='imported-from-xml'>icon</i>@endif </div>
            <div class="activity-element-info">
                {{ substr($getCode->getActivityCodeName('FinanceType', $defaultFinanceType) , 0 , -5)}}
            </div>
        </div>
        <a href="{{route('activity.default-finance-type.index', $id)}}" class="edit-element">@lang('global.edit')</a>
        <a href="{{route('activity.delete-element', [$id, 'default_finance_type'])}}" class="delete pull-right">@lang('global.remove')</a>
    </div>
@endif
