<div class="col-sm-12">
    <h2>Results/Outcomes Documents</h2>
    {!! Form::hidden('document_link[0][category][0][code]', 'A08') !!}
    {!! Form::hidden('document_link[0][format]', 'text/html') !!}
    {!! Form::hidden('document_link[0][title][0][narrative][0][language]', "") !!}
    {!! Form::hidden('document_link[0][language]', '[]') !!}

    <div class="col-sm-6">
        {!! Form::label('result_document_title', 'Title', ['class' => 'control-label required']) !!}
        {!! Form::text('document_link[0][title][0][narrative][0][narrative]', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>
    <div class="col-sm-6">
        {!! Form::label('result_document_url', 'Document URL', ['class' => 'control-label required']) !!}
        {!! Form::text('document_link[0][url]', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>
</div>

<div class="col-sm-12">
    <h2>Annual Reports</h2>
    {!! Form::hidden('document_link[1][category][0][code]', 'B01') !!}
    {!! Form::hidden('document_link[1][format]', 'text/html') !!}
    {!! Form::hidden('document_link[1][title][0][narrative][0][language]', "") !!}
    {!! Form::hidden('document_link[1][language]', '[]') !!}
    <div class="col-sm-6">
        {!! Form::label('annual_document_title', 'Title', ['class' => 'control-label required']) !!}
        {!! Form::text('document_link[1][title][0][narrative][0][narrative]', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <div class="col-sm-6">
        {!! Form::label('annual_document_url', 'Document Url', ['class' => 'control-label required']) !!}
        {!! Form::text('document_link[1][url]', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>
</div>
