@extends('layouts.formtwo')

@section('left')

        <h5>Location Info</h5>


        {!! Former::text('Name','Name') !!}

        {!! Former::text('Description','Description') !!}

        {!! Former::text('Address','Address') !!}

        <h5>Location Clasification</h5>
        {!! Former::select('Type')->options(array('Building'=>'Building','Field'=>'Field'))->label('Type')->class('form-control bootstrap-select')  !!}

        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Coy','Company') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Dept','Dept.') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('City','City') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Country','Country') !!}
            </div>
        </div>


        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary'))!!}&nbsp;&nbsp;
        {!! HTML::link($back,'Cancel',array('class'=>'btn'))!!}

@stop

@section('right')
        <h5>Geo Location</h5>
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('Latitude','Latitude') !!}
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('Longitude','Longitude') !!}
            </div>
        </div>

        <h5>File</h5>

        {!! $fupload->id('docupload')
            ->parentclass('assetlocation')
            ->ns('assetlocation')
            ->title('Select Document')
            ->label('Upload Document')
            ->url('upload/docs')
            ->singlefile(false)
            ->prefix('assetlocation')
            ->multi(true)->make($formdata) !!}

@stop

@section('modals')

@stop

@section('aux')
{!! HTML::style('css/summernote.css') !!}
{!! HTML::style('css/summernote-bs3.css') !!}

{!! HTML::script('js/summernote.min.js') !!}

<script type="text/javascript">


$(document).ready(function() {


    $('.pick-a-color').pickAColor();

    $('#name').keyup(function(){
        var title = $('#name').val();
        var slug = string_to_slug(title);
        $('#permalink').val(slug);
    });

    $('.editor').summernote({
        height:500
    });

    $('#location').on('change',function(){
        var location = $('#location').val();
        console.log(location);

        $.post('{{ URL::to('asset/rack' ) }}',
            {
                loc : location
            },
            function(data){
                var opt = updateselector(data.html);
                $('#rack').html(opt);
            },'json'
        );

    })

    $('.auto_merchant').autocomplete({
        source: base + 'ajax/merchant',
        select: function(event, ui){
            $('#merchant-id').val(ui.item.id);
        }
    });

    function updateselector(data){
        var opt = '';
        for(var k in data){
            opt += '<option value="' + k + '">' + data[k] +'</option>';
        }
        return opt;
    }


});

</script>

@stop