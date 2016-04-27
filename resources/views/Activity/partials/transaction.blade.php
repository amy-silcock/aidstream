@if(!emptyOrHasEmptyTemplate($transactions))
    <div class="panel panel-default expanded">
        <div class="panel-heading">
            <div class="activity-element-title">
                Transactions
            </div>
            <a href="{{route('activity.transaction.index', $id)}}" class="edit-element">edit</a>
        </div>
        <div class="panel-body panel-level-1">
            @foreach($transactions as $transaction)
                <div class="panel-default">
                    <div class="panel-heading">
                        <div class="activity-element-title">
                            {{$getCode->getActivityCodeName('TransactionType', $transaction['transaction']['transaction_type'][0]['transaction_type_code']) .' ; '. $transaction['transaction']['value'][0]['amount']. ' '.$getCode->getCode('Organization', 'Currency', $transaction['transaction']['value'][0]['currency']) . ' ; ' . formatDate($transaction['transaction']['value'][0]['date'])}}
                            {{--                            {{$getCode->getActivityCodeName('TransactionType', $transaction['transaction']['transaction_type'][0]['transaction_type_code']) .' ; '. $transaction['transaction']['value'][0]['amount'] . ' ; ' . formatDate($transaction['transaction']['value'][0]['date'])}}--}}
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="panel panel-default">
                            <div class="panel-element-body row">
                                <div class="col-xs-12 col-md-12">
                                    <div class="col-xs-12 col-sm-4">Ref:</div>
                                    <div class="col-xs-12 col-sm-8">{{$transaction['transaction']['reference']}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-lg-12 panel-level-2">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="activity-element-title">Transaction Type</div>
                                </div>
                                <div class="panel-element-body row">
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Code:</div>
                                        <div class="col-xs-12 col-sm-8">{{$getCode->getActivityCodeName('TransactionType', $transaction['transaction']['transaction_type'][0]['transaction_type_code'])}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="activity-element-title">Transaction Date</div>
                                </div>
                                <div class="panel-element-body row">
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Date:</div>
                                        <div class="col-xs-12 col-sm-8">{{ formatDate($transaction['transaction']['transaction_date'][0]['date']) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="activity-element-title">Value</div>
                                </div>
                                <div class="panel-element-body row">
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Amount:</div>
                                        <div class="col-xs-12 col-sm-8">{{$transaction['transaction']['value'][0]['amount']}}</div>
                                    </div>
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Date:</div>
                                        <div class="col-xs-12 col-sm-8">{{ formatDate($transaction['transaction']['value'][0]['date']) }}</div>
                                    </div>
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Currency:</div>
                                        <div class="col-xs-12 col-sm-8">{{array_key_exists('currency', $transaction['transaction']['value'][0]) ? $getCode->getOrganizationCodeName('Currency', $transaction['transaction']['value'][0]['currency']) : ''}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="activity-element-title">Description</div>
                                </div>
                                <div class="panel-element-body row">
                                    @foreach($transaction['transaction']['description'] as $description)
                                        {{--*/
                                        $ValidNarrative = getVal($description, ['narrative'], []);
                                        $description['narrative'] = $ValidNarrative;
                                        /*--}}
                                        @foreach($description['narrative'] as $narrative)
                                            {{--*/
                                            $narrative['language'] = getVal($narrative, ['language'], '');
                                            /*--}}
                                            <div class="panel-element-body row">
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="col-xs-12 col-sm-4">Text:</div>
                                                    <div class="col-xs-12 col-sm-8">{{$narrative['narrative'] . hideEmptyArray('Organization', 'Language', $narrative['language'])}}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="activity-element-title">Provider Organization</div>
                                </div>
                                <div class="panel-element-body row">
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Ref:</div>
                                        <div class="col-xs-12 col-sm-8">{{$transaction['transaction']['provider_organization'][0]['organization_identifier_code']}}</div>
                                    </div>
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Provider Activity Id:</div>
                                        <div class="col-xs-12 col-sm-8">{{$transaction['transaction']['provider_organization'][0]['provider_activity_id']}}</div>
                                    </div>
                                    @foreach($transaction['transaction']['provider_organization'] as $narrative)
                                        {{--*/
                                        $ValidNarrative = getVal($narrative, ['narrative'], []);
                                        $narrative['narrative'] = $ValidNarrative;
                                        /*--}}
                                        @foreach($narrative['narrative'] as $narrative)
                                            {{--*/
                                            $narrative['language'] = getVal($narrative, ['language'], '');
                                            /*--}}
                                            <div class="col-xs-12 col-md-12">
                                                <div class="col-xs-12 col-sm-4">Text:</div>
                                                <div class="col-xs-12 col-sm-8">{{$narrative['narrative'] . hideEmptyArray('Organization', 'Language', $narrative['language'])}}</div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="activity-element-title">Receiver Organization</div>
                                </div>
                                <div class="panel-element-body row">
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Ref:</div>
                                        <div class="col-xs-12 col-sm-8">{{$transaction['transaction']['receiver_organization'][0]['organization_identifier_code']}}</div>
                                    </div>
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Reciever Activity Id:</div>
                                        <div class="col-xs-12 col-sm-8">{{$transaction['transaction']['receiver_organization'][0]['receiver_activity_id']}}</div>
                                    </div>
                                    @foreach($transaction['transaction']['receiver_organization'] as $narrative)
                                        {{--*/
                                        $ValidNarrative = getVal($narrative, ['narrative'], []);
                                        $narrative['narrative'] = $ValidNarrative;
                                        /*--}}
                                        @foreach($narrative['narrative'] as $narrative)
                                            {{--*/
                                            $narrative['language'] = getVal($narrative, ['language'], '');
                                            /*--}}
                                            <div class="col-xs-12 col-md-12">
                                                <div class="col-xs-12 col-sm-4">Text:</div>
                                                <div class="col-xs-12 col-sm-8">{{$narrative['narrative'] . hideEmptyArray('Organization', 'Language', $narrative['language'])}}</div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="activity-element-title">Sector</div>
                                </div>
                                <div class="panel-element-body row">
                                    {{--*/
                                        $vocabulary = $transaction['transaction']['sector'][0]['sector_vocabulary'];
                                        $vocabularyValue = $getCode->getActivityCodeName('SectorVocabulary', $vocabulary);
                                        if ($vocabulary == 1 || $vocabulary == '') {
                                            $sectorValue = $getCode->getActivityCodeName('Sector', $transaction['transaction']['sector'][0]['sector_code']);
                                        } elseif ($vocabulary == 2) {
                                            $sectorValue = $getCode->getActivityCodeName('SectorCategory', $transaction['transaction']['sector'][0]['sector_category_code']);
                                        } else {
                                            $sectorValue = $transaction['transaction']['sector'][0]['sector_text'];
                                        }
                                    /*--}}
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Vocabulary:</div>
                                        <div class="col-xs-12 col-sm-8">{{ $vocabularyValue }}</div>
                                    </div>
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-xs-12 col-sm-4">Code:</div>
                                        <div class="col-xs-12 col-sm-8">{{ $sectorValue }}</div>
                                    </div>
                                    @foreach($transaction['transaction']['sector'] as $narrative)
                                        {{--*/
                                        $ValidNarrative = getVal($narrative, ['narrative'], []);
                                        $narrative['narrative'] = $ValidNarrative;
                                        /*--}}
                                        @foreach($narrative['narrative'] as $narrative)
                                            {{--*/
                                            $narrative['language'] = getVal($narrative, ['language'], '');
                                            /*--}}
                                            <div class="col-xs-12 col-md-12">
                                                <div class="col-xs-12 col-sm-4">Text:</div>
                                                <div class="col-xs-12 col-sm-8">{{$narrative['narrative'] . hideEmptyArray('Organization', 'Language', $narrative['language'])}}</div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                            @if(getVal($transaction, ['transaction', 'recipient_country'], []))
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="activity-element-title">Recipient Country</div>
                                        <a href="{{ route('activity.transaction.delete-block', [$transaction['activity_id'], $transaction['id'], 'recipient_country']) }}" class="delete pull-right">remove</a>
                                    </div>
                                    <div class="panel-element-body row">
                                        <div class="col-xs-12 col-md-12">
                                            <div class="col-xs-12 col-sm-4">Country:</div>
                                            <div class="col-xs-12 col-sm-8">{{ $getCode->getOrganizationCodeName('Country', getVal($transaction, ['transaction', 'recipient_country', 0, 'country_code'])) }}</div>
                                        </div>
                                        @foreach($transaction['transaction']['recipient_country'] as $recipientCountry)
                                            {{--*/
                                            $ValidNarrative = getVal($recipientCountry, ['narrative'], []);
                                            $recipientCountry['narrative'] = $ValidNarrative;
                                            /*--}}
                                            @foreach($recipientCountry['narrative'] as $narrative)
                                                {{--*/
                                                $narrative['language'] = getVal($narrative, ['language'], '');
                                                /*--}}
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="col-xs-12 col-sm-4">Text:</div>
                                                    <div class="col-xs-12 col-sm-8">{{$narrative['narrative'] . hideEmptyArray('Organization', 'Language', $narrative['language'])}}</div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if(getVal($transaction, ['transaction', 'recipient_region'], []))
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="activity-element-title">Recipient Region</div>
                                        <a href="{{ route('activity.transaction.delete-block', [$transaction['activity_id'], $transaction['id'], 'recipient_region']) }}" class="delete pull-right">remove</a>
                                    </div>
                                    <div class="panel-element-body row">
                                        <div class="col-xs-12 col-md-12">
                                            <div class="col-xs-12 col-sm-4">Region:</div>
                                            <div class="col-xs-12 col-sm-8">{{ $getCode->getActivityCodeName('Region', getVal($transaction, ['transaction', 'recipient_region', 0, 'region_code'])) }}</div>
                                        </div>
                                        <div class="col-xs-12 col-md-12">
                                            <div class="col-xs-12 col-sm-4">Vocabulary:</div>
                                            <div class="col-xs-12 col-sm-8">{{ $getCode->getActivityCodeName('RegionVocabulary', getVal($transaction, ['transaction', 'recipient_region', 0, 'vocabulary'])) }}</div>
                                        </div>
                                        <div class="col-xs-12 col-md-12">
                                            <div class="col-xs-12 col-sm-4">Vocabulary URI:</div>
                                            <div class="col-xs-12 col-sm-8">{{ getVal($transaction, ['transaction', 'recipient_region', 0, 'vocabulary_uri']) }}</div>
                                        </div>
                                        @foreach($transaction['transaction']['recipient_region'] as $recipientRegion)
                                            {{--*/
                                            $ValidNarrative = getVal($recipientRegion, ['narrative'], []);
                                            $recipientRegion['narrative'] = $ValidNarrative;
                                            /*--}}
                                            @foreach($recipientRegion['narrative'] as $narrative)
                                                {{--*/
                                                $narrative['language'] = getVal($narrative, ['language'], '');
                                                /*--}}
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="col-xs-12 col-sm-4">Text:</div>
                                                    <div class="col-xs-12 col-sm-8">{{$narrative['narrative'] . hideEmptyArray('Organization', 'Language', $narrative['language'])}}</div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
