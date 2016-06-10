@if(!emptyOrHasEmptyTemplate($sectors))
    <div class="activity-element-wrapper">
        <div class="title">@lang('activityView.sector')</div>
        @foreach(groupActivityElements($sectors , 'sector_vocabulary') as $key => $sectors)
            <div class="activity-element-list">
                <div class="activity-element-label">{{ $getCode->getCodeNameOnly('SectorVocabulary' , $key) }}</div>
                <div class="activity-element-info">
                    @foreach($sectors as $sector)
                        <li>{!! checkIfEmpty(getSectorInformation($sector , $sector['percentage']))  !!}</li>
                        <div class="toggle-btn">
                            <span class="show-more-info">Show more info</span>
                            <span class="hide-more-info hidden">Hide more info</span>
                        </div>
                        <div class="more-info hidden">
                            <div class="element-info">
                                @if(session('version') != 'V201')
                                    <div class="activity-element-label">@lang('activityView.vocabulary_uri')</div>
                                    <div class="activity-element-info">{!!  checkIfEmpty(getClickableLink($sector['vocabulary_uri']))  !!}</div>
                                @endif
                            </div>
                            <div class="element-info">
                                <div class="activity-element-label">@lang('activityView.description')</div>
                                <div class="activity-element-info">
                                    {!!  getFirstNarrative($sector)  !!}
                                    @include('Activity.partials.viewInOtherLanguage', ['otherLanguages' => getOtherLanguages($sector['narrative'])])
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <a href="{{route('activity.sector.index', $id)}}" class="edit-element">edit</a>
        <a href="{{route('activity.delete-element', [$id, 'sector'])}}" class="delete pull-right">remove</a>
    </div>
@endif
