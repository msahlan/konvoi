@extends('layouts.form')


@section('left')

<h5>Import {!! $title !!}</h5>


        {!! $aux_form !!}

        {!! Former::file('inputfile','Select file ( .xls, .xlsx )')  !!}

        {!! Former::hidden( 'controller',$back )  !!}
        {!! Former::hidden( 'importkey',$importkey )  !!}
        <div class="row">
            <div class="col-md-4">
                {!! Former::text('headindex','Row containing header')->value(config('import.header_row'))  !!}
                {!! Former::text('firstdata','Data starting at row')->value(config('import.data_row'))  !!}
                {!! Former::text('limitdata','Data Row Limit')->value(config('import.data_limit'))  !!}
            </div>
        </div>

        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary')) !!}&nbsp;&nbsp;
        {!! HTML::link($back,'Cancel',array('class'=>'btn'))!!}


@endsection

@section('aux')

<script type="text/javascript">


$(document).ready(function() {

});

</script>

@endsection