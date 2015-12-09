@extends('app')

@section('content')
    <div class="container main-container">
        <div class="row">
            @include('includes.side_bar_menu')
            <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
                @include('includes.breadcrumb')
                <div class="panel-content-heading panel-title-heading">Adding Title of <span>FR-PDR-W691075333-9810</span></div>
                <div class="col-xs-8 col-md-8 col-lg-8 element-content-wrapper">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <div class="create-form">
                            {!! form($form) !!}
                        </div>
                            <div class="collection-container hidden"
                                 data-prototype="{{ form_row($form->narrative->prototype()) }}">
                            </div>
                        </div>
                    </div>
                </div>
                @include('includes.activity.element_menu')
            </div>
        </div>
    </div>
@endsection

 