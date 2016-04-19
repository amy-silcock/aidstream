@if(!empty($defaultFlowType))
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="activity-element-title">
                Default Flow Type
            </div>
            <a href="{{route('activity.default-flow-type.index', $id)}}" class="edit-element">edit</a>
            <a href="{{route('activity.delete-element', [$id, 'default_flow_type'])}}" class="delete pull-right">remove</a>
        </div>
        <div class="panel-body panel-element-body row">
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-sm-4">Code:</div>
                <div class="col-xs-12 col-sm-8">{{$getCode->getActivityCodeName('FlowType', $defaultFlowType)}}</div>
            </div>
        </div>
    </div>
@endif
