@if(!emptyOrHasEmptyTemplate($org_name))
    <dl class="dl-horizontal">
        <dt>@lang('activityView.name')</dt>
        <dd>
            {!! getFirstOrgName($org_name) !!}
            @include('Activity.partials.viewInOtherLanguage', ['otherLanguages' => getOtherLanguages($org_name)])
        </dd>
    </dl>

    {{--<a href="{{ url('/organization/' . $orgId . '/name') }}" class="edit-element">edit</a>--}}

    <div class="panel-body panel-element-body row">
        @foreach($org_name as $name)
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-xs-4">Text:</div>
                <div class="col-xs-12 col-xs-8">{{ $name['narrative'] . hideEmptyArray('Organization', 'Language', $name['language']) }}</div>
                @if(isset($name['new_field']))
                    <div class="col-xs-12 col-xs-4">New Field Value:</div>
                    <div class="col-xs-12 col-xs-8">{{ $name['new_field'] }}</div>
                @endif
            </div>
        @endforeach
    </div>

@endif
