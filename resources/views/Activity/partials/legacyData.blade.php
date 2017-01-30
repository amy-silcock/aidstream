@if(!emptyOrHasEmptyTemplate(getVal($activityDataList, ['legacy_data'], [])))
    <div class="activity-element-wrapper">
        <div class="activity-element-list">
            <div class="activity-element-label">@lang('element.legacy_data') @if(array_key_exists('Legacy Data',$errors)) <i class='imported-from-xml'>icon</i>@endif </div>
            <div class="activity-element-info">
                @foreach(getVal($activityDataList, ['legacy_data'], []) as $legacyData)
                    <li>{{ getVal($legacyData, ['name']) . ': '. getVal($legacyData, ['value']) }}
                        <em>@lang('elementForm.iati_equivalent')
                            : {!!   checkIfEmpty(getVal($legacyData, ['iati_equivalent'])) !!}</em>
                    </li>
                @endforeach
            </div>
        </div>
        <a href="{{route('activity.legacy-data.index', $id)}}" class="edit-element">@lang('global.edit')</a>
        <a href="{{route('activity.delete-element', [$id, 'legacy_data'])}}" class="delete pull-right">@lang('global.remove')</a>
    </div>
@endif
