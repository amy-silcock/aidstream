<div class="hidden col-sm-12" id="tz-location-clone">
    <div class="col-sm-6" id="region-wrap">
        {!! Form::label('location[index][administrative][0][code]', 'Region', ['class' => 'control-label']) !!}
        {!! Form::select('location[index][administrative][0][code]', ['' => 'Select one of the following.'] + config('tz.location.region'), null, ['class' => 'form-control region', 'required' => 'required']) !!}

        {!! Form::hidden('location[index][administrative][0][vocabulary]', 'G1') !!}
        {!! Form::hidden('location[index][administrative][0][level]', '1') !!}
    </div>

    <div class="col-sm-6" id="district-wrap">
        {!! Form::label('location[index][administrative][1][code]', 'District', ['class' => 'control-label']) !!}
        {!! Form::select('location[index][administrative][1][code]', ['' => 'Select one of the following.'], null, ['class' => 'form-control district', 'required' => 'required']) !!}

        {!! Form::hidden('location[index][administrative][1][vocabulary]', 'G1') !!}
        {!! Form::hidden('location[index][administrative][1][level]', '2') !!}
    </div>

    <a href="javascript:void(0)" onclick="removeLocation(this)" class="remove_from_collection">Remove</a>
</div>
