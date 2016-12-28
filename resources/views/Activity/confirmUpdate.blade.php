@extends('app')

@section('title', trans('update_activities_confirmation'))

@section('content')
    <div class="container main-container">
        <div class="row">
            <div class="col-xs-12 col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-content-heading">@lang('global.confirm_to_update_activities')</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('activity-upload.update') }}">
                            <input name="_method" type="hidden" value="PUT">
                            <input name="_token" type="hidden" value="{{csrf_token()}}">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th width="30px"></th>
                                    <th>@lang('global.activity_title')</th>
                                    <th>@lang('global.activity_identifier')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $key=>$activity)
                                    <tr>
                                        <td><input type="checkbox" name="checkbox[{{$key}}]" value="{{json_encode($activity)}}"/></td>
                                        <td class="activity_title">{{ $activity['title'][0]['narrative'] }}</td>
                                        <td>{{ $activity['identifier']['activity_identifier'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary btn-submit">@lang('global.update')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

