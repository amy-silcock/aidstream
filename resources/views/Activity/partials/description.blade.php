@if(!emptyOrHasEmptyTemplate($descriptions))
    <div class="activity-element-wrapper">
        <div class="title">@lang('element.description') @if(array_key_exists('Description',$errors)) <i class='imported-from-xml'>icon</i>@endif </div>
        @foreach($descriptions as $description)
            <div class="activity-element-list">
                <div class="activity-element-label">
                    {{$getCode->getCodeNameOnly('DescriptionType', $description['type'])}} @lang('elementForm.description')
                </div>
                <div class="activity-element-info">
                    {!! getFirstNarrative($description) !!}
                    @include('Activity.partials.viewInOtherLanguage' , ['otherLanguages' => getOtherLanguages($description['narrative']) ])
                </div>
            </div>
        @endforeach
        <a href="{{route('activity.description.index', $id)}}" class="edit-element">@lang('global.edit')</a>
        <a href="{{route('activity.delete-element', [$id, 'description'])}}" class="delete pull-right">@lang('global.remove')</a>
    </div>
@endif
