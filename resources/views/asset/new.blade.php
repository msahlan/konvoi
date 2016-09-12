@extends('layouts.formtwo')

@section('left')

        <h5>Asset Info</h5>


        {!! Former::text('Name','Name') !!}

        {!! Former::text('Description','Description') !!}

        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('Brand','Brand.') !!}
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('SerialNumber','MSN') !!}
            </div>
        </div>

        <h5>Asset Number</h5>
        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Coy','Company') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Dept','Dept.') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Category','Category') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Sequence','Sequence') !!}
            </div>
        </div>

        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary'))!!}&nbsp;&nbsp;
        {!! HTML::link($back,'Cancel',array('class'=>'btn'))!!}

@stop

@section('right')
        <h5>Depreciation</h5>

        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('AcqDate','Acquisition Date') !!}
            </div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                {!! Former::select('AcqCurr')->options(array('inactive'=>'IDR','active'=>'USD'))->label('Curr.')->class('form-control bootstrap-select')  !!}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::text('AcqValue','Acquisition Value') !!}
            </div>
        </div>


        <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::text('UtilizationDate','Utilization Date') !!}
            </div>
        </div>

        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::select('Depreciable')->options(array(1=>'Depreciable',0=>'Non Depreciable'))->label('Depreciable')->class('form-control bootstrap-select')  !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::select('DeprStart')->options(array('ACQ'=>'Since Acquisition','UT'=>'Since Utilization'))->label('Start')->class('form-control bootstrap-select')  !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::select('DeprMethod')->options(array('SL'=>'Straight Line','DD'=>'Double Declining'))->label('Method')->class('form-control bootstrap-select')  !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('DeprPeriod','Depr. Period') !!}
            </div>
        </div>

        <h5>Location</h5>

        <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::select('Mobile')->options(array(0=>'Fixed Asset / Non Mobile',1=>'Movable / Mobile'))->label('Mobility')->class('form-control bootstrap-select')  !!}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::text('Location','Location') !!}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::text('Room','Room') !!}
            </div>
        </div>


        <h5>File</h5>

        {!! $fupload->id('docupload')
            ->parentclass('asset')
            ->ns('asset')
            ->title('Select Document')
            ->label('Upload Document')
            ->url('upload/docs')
            ->singlefile(false)
            ->prefix('asset')
            ->multi(true)->make() !!}

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