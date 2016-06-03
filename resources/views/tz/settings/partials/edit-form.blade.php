<div id="basic-info">
    <div class="col-md-12">Reporting Organisation</div>
    <div>
        <div class="col-sm-6">
            {!! Form::label('Organisation Name', 'Organisation Name', ['class' => 'control-label required']) !!}
            {!! Form::text('narrative', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>

        <div class="col-sm-6">
            {!! Form::label('language', 'Language', ['class' => 'control-label required']) !!}
            {!! Form::select('language', ['' => 'Select one of following:'] + $language, $settings['language'],['class' => 'form-control', 'required' => 'required']) !!}
        </div>

        <div class="col-sm-6">
            {!! Form::label('reporting org', 'Organisation Identifier', ['class' => 'control-label required']) !!}
            {!! Form::text('reporting_org_identifier', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>

        <div class="col-sm-6">
            {!! Form::label('reporting org type', 'Organisation Type', ['class' => 'control-label required']) !!}
            {!! Form::select('reporting_org_type', ['' => 'Select one of following:'] + $orgType, $settings['reporting_org_type'],['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="col-md-12">Publishing Information</div>
    <div>
        <div class="col-sm-6">
            {!! Form::label('publisher id', 'Publisher Id', ['class' => 'control-label']) !!}
            {!! Form::text('publisher_id', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>

        <div class="col-sm-6">
            {!! Form::label('API Key', 'Api Id', ['class' => 'control-label']) !!}
            {!! Form::text('api_id', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>

        <div class="col-sm-11 auto-publish">
            <label>Automatically Update the IATI Registry when publishing files?</label>
            <label class="control-label pull-left">
                <input type="radio" name="publish_files" value="no" checked="checked"> No
            </label>
            <label class="control-label pull-left">
                <input type="radio" name="publish_files" value="yes"> Yes
            </label>
        </div>
    </div>

    <div class="col-md-12">Default Values</div>
    <div>
        <div class="col-sm-6">
            {!! Form::label('default currency', 'Default Currency', ['class' => 'control-label required']) !!}
            {!! Form::select('default_currency', ['' => 'Select one of following:'] + $currency, $settings['default_currency'],['class' => 'form-control', 'required' => 'required']) !!}
        </div>

        <div class="col-sm-6">
            {!! Form::label('default language', 'Default Language', ['class' => 'control-label required']) !!}
            {!! Form::select('default_language', ['' => 'Select one of following:'] + $language, $settings['default_language'],['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>
</div>
