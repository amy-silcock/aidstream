@extends('app')

@section('title', 'Activity Results - ' . $activityData->IdentifierTitle)

@section('content')

<div class="container main-container">
    <div class="row">
        @include('includes.side_bar_menu')
        <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
            @include('includes.response')
            <div class="element-panel-heading">
                <div>Activity Document Links
                @if(count($documentLinks) > 0)
                    <div class="panel-action-btn">
                        <a href="{{ route('activity.document-link.create', $id) }}" class="btn btn-primary add-new-btn">Add New
                            Document Link</a>
                    </div>
                @endif
                </div>
            </div>
            <div class="col-xs-12 col-md-8 col-lg-8 element-content-wrapper result-content-wrapper">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @if(count($documentLinks) > 0)
                            <table class="table table-striped" id="data-table">
                                <thead>
                                <tr>
                                    <th width="10%" class="no-sort">S.N.</th>
                                    <th width="45%" class="default-sort">Title</th>
                                    <th width="30%">Type</th>
                                    <th width="15%" class="no-sort">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($documentLinks as $documentLinkIndex => $documentLink)
                                    <tr data-href="{{ route('activity.document-link.show', [$id, $documentLink->id]) }}" class="clickable-row">
                                        <td>{{ $documentLinkIndex + 1 }}</td>
                                        <td class="activity_title">
                                            doc link title{{--{{ $documentLinkIndex->title }}--}}
                                        </td>
                                        <td>
                                            type{{--{{ $result->type }}--}}
                                        </td>
                                        <td>
                                            <div class="activity_actions">
                                                <a href="{{ route('activity.document-link.show', [$id, $documentLink->id]) }}"
                                                   class="view">View</a>
                                                <a href="{{ route('activity.document-link.edit', [$id, $documentLink->id]) }}"
                                                   class="edit">Edit</a>
                                                <a href="{{ url(sprintf('activity/%s/document-link/%s/delete', $id, $documentLink->id)) }}"
                                                   class="delete">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center no-data no-result-data">
                                You haven’t added any document link yet.
                                <a href="{{ route('activity.document-link.create', $id) }}" class="btn btn-primary">Add New Document Link</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @include('includes.activity.element_menu')
        </div>
    </div>
</div>
@endsection
