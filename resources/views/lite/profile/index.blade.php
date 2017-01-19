@extends('lite.base.sidebar')

@section('title', trans('lite/title.profile'))

@inject('getCode', 'App\Helpers\GetCodeName')
@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper profile-content-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default panel-profile">
            <div class="panel-body">
                <div class="profile-top-wrapper">
                    <div class="profile-img">
                        @if(Auth::user()->profile_url)
                            <img src="{{url(Auth::user()->profile_url)}}" width="180" height="180"
                                 alt="{{Auth::user()->name}}">
                        @else
                            <img src="{{url('images/avatar-full.png')}}" width="150" height="158"
                                 alt="{{Auth::user()->name}}">
                        @endif
                    </div>
                    <div class="pull-left">
                        <div class="profile-info-block">
                            <div class="profile-basic-info">
                                <div class="auth-name">{{Auth::user()->name}}</div>
                                <span class="profile-username">{{Auth::user()->username}}</span>
                                <a href="{{route('lite.user.profile.edit', Auth::user()->id)}}"
                                   class="edit-profile">@lang('user.edit_profile')</a>
                                <a href="{{route('user.reset-user-password', Auth::user()->id)}}"
                                   class="change-password">@lang('user.change_password')</a>
                                <span class="profile-user-email"><a
                                            href="mailto:{{Auth::user()->email}}">{{Auth::user()->email}}</a></span>
                            </div>
                        </div>
                        @if((Auth::user()->isAdmin()) && $organisation->secondary_contact)
                            <div class="secondary-contact-block">
                                <span>@lang('user.secondary_contact')</span>
                                <div>
                                    <span class="profile-name">{{ getVal((array)$organisation->secondary_contact,['first_name']) }} {{getVal((array)$organisation->secondary_contact,['last_name'])}}</span>
                                    <a href="mailto:{{ getVal((array)$organisation->secondary_contact,['email']) }}"> {{getVal((array)$organisation->secondary_contact,['email'])}} </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="profile-bottom-wrapper">
                    <div class="profile-role-info">
                        <dl>
                            <dt>@lang('user.timezone')</dt>
                            <dd>{{Auth::user()->time_zone}}</dd>
                        </dl>
                        <dl>
                            <dt>@lang('user.language')</dt>
                            <dd>English</dd>
                        </dl>
                        <dl>
                            <dt>@lang('user.permission')</dt>
                            <dd>{{Auth::user()->role->role}}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default panel-associated-organization">
            <div class="panel-org-heading">
                <div class="panel-sub-heading">@lang('user.associated_organisation')</div>
                <a href="{{route('settings')}}" class="edit-org pull-right">@lang('user.edit_organisation')</a>
            </div>
            <div class="panel-body">
                <div class="organization-logo"><img
                            src="{{$organisation->logo ? url($organisation->logo_url) : url('images/no-logo.png')}}">
                </div>
                <div class="organization-detail">
                    <div class="organization-name">{{getVal((array)$organisation->reporting_org,[0,'narrative',0,'narrative'])}}</div>
                    <ul>
                        @if($organisation->address)
                            <li class="address">
                                <label>@lang('user.address')</label><span>{{$organisation->address}}</span></li>
                        @endif
                        @if($organisation->country)
                            <li class="country">
                                <label>@lang('user.country')</label><span>{{$getCode->getOrganizationCodeName('Country', $organisation->country)}}</span>
                            </li>
                        @endif
                        @if($organisation['reporting_org'][0]['reporting_organization_type'])
                            <li class="org-type"><label>@lang('organisation.organisation_type')</label>
                                <span>
                                            {{substr($getCode->getOrganizationCodeName('OrganizationType',$organisation['reporting_org'][0]['reporting_organization_type']),0,-4)}}
                                        </span>
                            </li>
                        @endif
                        <h2>IATI @lang('organisation.organisation_identifier')</h2>
                        @if($organisation->registration_agency)
                            <li class="org-reg-agency"><label>@lang('organisation.organisation_registration_agency')</label>
                                <span>{{$organisation->registration_agency}}</span>
                            </li>
                        @endif
                        @if($organisation->registration_number)
                            <li class="org-reg-number">
                                <label>@lang('organisation.organisation_registration_number')</label>
                                <span>{{$organisation->registration_number}}</span>
                            </li>
                        @endif
                        @if($organisation->organization_url)
                            <li class="website"><label>@lang('user.website')</label><a href="{{$organisation->organization_url}}"
                                                                                       target="_blank">{{$organisation->organization_url}}</a>
                            </li>
                        @endif
                        @if($organisation->telephone)
                            <li class="telephone">
                                <label>@lang('user.telephone')</label><span>{{$organisation->telephone}}</span>
                            </li>
                        @endif
                        @if($organisation->twitter)
                            <li class="twitter"><label>@lang('user.twitter')</label>
                                <a href="http://www.twitter.com/{{ $organisation->twitter }}">{{$organisation->twitter}}</a>
                            </li>
                        @endif

                    </ul>
                    {{--<div class="disqus-wrapper"><span>Disqus Comments : </span>{{($organisation[0]->disqus_comments == 1) ? 'Enabled' : 'Disabled'}}</div>--}}
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')

@stop
