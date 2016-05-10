@extends('app')

@section('title', 'User Logs')

@section('content')
    <div class="container main-container">
        <div class="row">
            @include('includes.side_bar_menu')
            <div class="col-xs-9 col-lg-9 content-wrapper settings-wrapper">
                @include('includes.response')
                <div class="panel panel-default">
                    <div class="element-panel-heading">
                        <div>
                            Logs
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="create-form">
                        <form method="POST" id="form-filter" action="{{ route('user-logs.filter') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                            <div class="collection_form">
                                <div class="form-group">
                                    <label>Data</label>
                                    <select class="form-control" name="dataSelection">
                                        <option value="all">All</option>
                                        <option value="organization" @if($dataSelection == "organization") selected="selected" @endif>Organization Data</option>

                                        @foreach($activitiesOfOrganization as $activities)
                                            <option value="{{ $activities->id }}" @if($dataSelection == $activities->id) selected="selected" @endif >{{ $activities->identifier['activity_identifier'] }}</option>
                                        @endforeach
                                    </select>

                                    <label>User</label>

                                    <select class="form-control" name="userSelection">
                                        <option value="all">All users</option>

                                        @foreach($usersOfOrganization as $users)
                                            <option value="{{ $users->id }}" @if($userSelection == $users->id) selected="selected" @endif >{{ $users->first_name }} {{  $users->last_name }}</option>
                                        @endforeach
                                    </select>
                                    <button id="btnSearch" name="btnSearch">Search</button>
                                </div>

                            </div>
                        </form>

                        <table class="table table-striped">
                            <thead>
                            <th>Date</th>
                            <th>Action</th>
                            <th>User</th>
                            </thead>

                            <tbody>
                            @forelse($userLogs as $logs)
                                <tr>
                                    <td> {{$logs->created_date}} </td>
                                    <td> {!! trans($logs->action, $logs->param) !!} </td>
                                    <td> {{$logs->user ? $logs->user->first_name." ".$logs->user->last_name: 'The user has been deleted.'}} </td>
                                </tr>
                            @empty
                                <td class="text-center no-data" colspan="4">No Activity Log Yet::</td>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {!! $userLogs->render() !!}
                    <div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('foot')
    <script type="text/javascript">
        $(document).ready(function () {
            $('.pagination a').click(function (e) {
                e.preventDefault();
                var pageNo = this.href.substr(this.href.indexOf('=')+1);
                $('#form-filter').attr("action","{{url('user-logs?page=')}}"+pageNo);
                $('#form-filter').submit();
            });
        });
    </script>
@endsection

