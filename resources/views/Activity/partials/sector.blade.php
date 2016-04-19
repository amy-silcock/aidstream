@if(!emptyOrHasEmptyTemplate($sectors))
    <div class="panel panel-default expanded">
        <div class="panel-heading">
            <div class="activity-element-title">
                Sectors
            </div>
            <a href="{{route('activity.sector.index', $id)}}" class="edit-element">edit</a>
            <a href="{{route('activity.delete-element', [$id, 'sector'])}}" class="delete pull-right">remove</a>
        </div>
        <div class="panel-body panel-level-1">
            @foreach($sectors as $sector)
                {{--*/
                    $vocabulary = $sector['sector_vocabulary'];
                    $vocabularyValue = $getCode->getActivityCodeName('SectorVocabulary', $vocabulary);
                    if ($vocabulary == 1 || $vocabulary == '') {
                        $sectorValue = $getCode->getActivityCodeName('Sector', $sector['sector_code']);
                    } elseif ($vocabulary == 2) {
                        $sectorValue = $getCode->getActivityCodeName('SectorCategory', $sector['sector_category_code']);
                    } else {
                        $sectorValue = $sector['sector_text'];
                    }
                /*--}}
                <div class="panel-default">
                    <div class="panel-heading">
                        <div class="activity-element-title">
                            {{ ($vocabularyValue ? $vocabularyValue . ' ; ' : '') . $sectorValue }}
                        </div>
                    </div>
                    <div class="panel-body panel-element-body row">
                        <div class="col-xs-12 col-md-12">
                            <div class="col-xs-12 col-sm-4">Vocabulary:</div>
                            <div class="col-xs-12 col-sm-8">{{ $vocabularyValue }}</div>
                        </div>
                        <div class="col-xs-12 col-md-12">
                            <div class="col-xs-12 col-sm-4">Code:</div>
                            <div class="col-xs-12 col-sm-8">{{ $sectorValue }}</div>
                        </div>
                        <div class="col-xs-12 col-md-12">
                            <div class="col-xs-12 col-sm-4">Percentage:</div>
                            <div class="col-xs-12 col-sm-8">{{$sector['percentage']}}</div>
                        </div>
                        @foreach($sector['narrative'] as $narrative)
                            <div class="col-xs-12 col-md-12">
                                <div class="col-xs-12 col-sm-4">Text:</div>
                                <div class="col-xs-12 col-sm-8">{{$narrative['narrative'] . hideEmptyArray('Organization', 'Language', $narrative['language'])}}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
