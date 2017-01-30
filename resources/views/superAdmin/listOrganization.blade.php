@extends('app')

@section('title', 'Organisations')

@section('content')
    <div class="container main-container admin-container">
        <div class="row">
            <div class="panel-content-heading">
                <div>Organisations</div>
            </div>
            <div class="col-xs-12 col-lg-8 organization-wrapper">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @if(count($organizations) > 0)
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th width="100px">S.N.</th>
                                    <th width="30%">Organisation Name</th>
                                    <th>Version</th>
                                    <th>Users</th>
                                    <th>Activities</th>
                                    <th width="180px">Action</th>
                                    <th>Lite</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($organizations as $key=>$organization)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $organization->name}}
                                            <div>@foreach($organization->users as $user)
                                                    @if($user->role_id == 1)
                                                        {{$user['email']}}
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>{{ $organization->settings ? $organization->settings->version : '' }}</td>
                                        <td>{{ count($organization->users) }}</td>
                                        <td>{{ count($organization->activities) }}</td>
                                        <td>
                                            <div class="organization_actions">
                                                @if(session('role_id') == 3)
                                                    <a href="{{ route('admin.hide-organization', [$organization->id,($organization->display) ? 0 : 1 ]) }}"
                                                       title="{{($organization->display) ? 'Hide' : 'Show'}}"
                                                       class="display {{($organization->display) ? 'Yes' : 'No'}}">{{($organization->display) ? 'Yes' : 'No'}}</a>
                                                    <a href="{{ route('admin.edit-organization', $organization->id)}}"
                                                       class="edit" title="Edit">@lang('global.edit')</a>
                                                    <a href="{{ route('admin.change-organization-status', [$organization->id, ($organization->status) ? 0 : 1]) }}"
                                                       class="check-status {{($organization->status) ? 'Disable' : 'Enable'}}"
                                                       title="{{($organization->status) ? 'Disable' : 'Enable'}}">{{($organization->status == 1) ? 'Disable' : 'Enable'}}</a>
                                                    <a href="{{ route('admin.delete-organization', $organization->id) }}" class="delete" title="delete">Delete</a>
                                                @endif
                                                @if ($organization->getAdminUser())
                                                    <a href="{{ route('admin.masquerade-organization', [$organization->id, $organization->adminUserId()]) }}" class="masquerade" title="Masquerade">Masquerade</a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($organization->settings->version >= 2.02)
                                                @if($organization->system_version_id == 1)
                                                    <input type="checkbox" data-href="{{ route('admin.change.system_version', $organization->id) }}"
                                                           data-message="Are you sure to switch to lite version?">
                                                @else
                                                    <input type="checkbox" checked data-href="{{ route('admin.change.system_version', $organization->id) }}"
                                                           data-message="Are you sure to switch to core version?">
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center no-data">No Organisation Registered Yet ::</div>
                        @endif
                    </div>
                </div>
            </div>
            @include('includes.superAdmin.side_bar_menu')
        </div>
    </div>
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="myModal">
        <div class="modal-dialog modal-lg" role="document">     
            <div class="modal-content">         
                <div class="modal-header">         
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang('lite/global.confirmation')</h4>
                </div>
                <form action="" method="POST" id="modal-form">
                    {{ csrf_field() }}
                    <input id="index" type="hidden" value="" name="index">
                    <div class="modal-body">
                        <p id="modal-message"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="modal-submit" class="btn btn-primary">@lang('lite/settings.yes')</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lite/settings.no')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('script')

    <script type="text/javascript">
        var masqueradeBtn = document.querySelectorAll('.masquerade');

        var preventClick = false;
        for (var i = 0; i < masqueradeBtn.length; i++) {
            var button = masqueradeBtn[i];
            button.onclick = function (event) {
                if (preventClick) {
                    event.preventDefault();
                }
                preventClick = true;
            }
        }

        var modal = $('#myModal');

        $('input[type="checkbox"]').on('change', function (e) {
            if (e.target.checked) {
                $('#modal-form').attr('action', $(this).attr('data-href'));
                $('#index').attr('value', 2);
                $('#modal-message').html($(this).attr('data-message'));
                modal.modal("show");
            }

            if (!e.target.checked) {
                $('#modal-form').attr('action', $(this).attr('data-href'));
                $('#index').attr('value', 1);
                $('#modal-message').html($(this).attr('data-message'));
                modal.modal("show");
            }
        });
    </script>
@stop
