@extends('app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-8">
                <div class="panel panel-default">
                    <div class="panel-heading">Related Activity</div>
                    <div class="panel-body">
                        <h3>{{ $activityData->IdentifierTitle }}</h3>
                        {!! form($form) !!}
                        <div class="collection-container hidden"
                             data-prototype="{{ form_row($form->related_activity->prototype()) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-4">
                @include('includes.activity.element_menu')
            </div>
        </div>
    </div>
@endsection
