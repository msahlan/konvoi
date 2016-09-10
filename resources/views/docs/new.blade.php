@extends('layouts.formtwo')

@section('left')

        <h5>Document Info</h5>


        {!! Former::text('Subject','Subject') !!}

        {!! Former::text('DocRef','Doc. Ref.') !!}

        {!! Former::text('DocDate','Doc. Date') !!}

        <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::select('Tipe')->options(array('inactive'=>'Inactive','active'=>'Active'))->label('Type')->class('form-control bootstrap-select')  !!}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::select('IO')->options(array('incoming'=>'Incoming','outgoing'=>'Outgoing'))->label('I/O')->class('form-control bootstrap-select')  !!}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::text('IODate','IO Date') !!}
            </div>
        </div>



        <h5>Actors</h5>

        {!! Former::text('Sender','Sender') !!}

        {!! Former::text('Recipient','Recipient') !!}

        {!! Former::text('Action','Action') !!}


        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary'))!!}&nbsp;&nbsp;
        {!! HTML::link($back,'Cancel',array('class'=>'btn'))!!}

@stop

@section('right')
        <h5>Call Code</h5>
        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Topic','Topic') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Coy','Company') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('MMYY','MMYY') !!}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                {!! Former::text('Urut','Sequence') !!}
            </div>
        </div>


        {!! Former::text('Fcallcode','File Call Code')->id('Fcallcode') !!}

        <h5>Location</h5>

        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('Location','Location') !!}
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('Boxing','Boxing') !!}
            </div>
        </div>

        <h5>Retention</h5>

        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('RetPer','Retention Period') !!}
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('RetDate','Retention Date') !!}
            </div>
        </div>

        <h5>File</h5>

        {!! $fupload->id('docupload')
            ->ns('document')
            ->title('Select Document')
            ->label('Upload Document')
            ->url('upload/docs')
            ->singlefile(false)
            ->prefix('document')
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