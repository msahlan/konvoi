@extends('layouts.formtwo')

@section('left')
<?php
    use App\Helpers\Prefs;
?>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/tags/tagsinput.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/tags/tokenfield.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/prism.min.js"></script>

    {{ HTML::script('js/autotoken.js')}}

        <h5>Device Information</h5>

        {!! Former::text('identifier','Device Identifier') !!}
        {!! Former::text('devname','Device Name') !!}
        {!! Former::text('descriptor','Description') !!}
        {!! Former::text('mobile','Number') !!}

        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                {!! Former::text('color','Color<br />')->class('form-control colorpicker') !!}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                {!! Former::select('is_on','Active')->options(array('1'=>'Yes','0'=>'No'))->class('form-control input-sm bootstrap-select') !!}
            </div>
        </div>

        {!! Form::submit('Save',array('class'=>'btn btn-primary'))!!}&nbsp;&nbsp;
        {!! HTML::link($back,'Cancel',array('class'=>'btn'))!!}

@stop

@section('right')

        <h5>Device Coverage</h5>

        {!! Former::text('city','City Coverage')->class('form-control tokenfield-city') !!}

        {!! Former::text('district','Area Coverage')->class('form-control tokenfield-district') !!}



@stop

@section('modals')

@stop

@section('aux')
{{ HTML::style('css/summernote.css') }}
{{ HTML::style('css/summernote-bs3.css') }}

{{ HTML::script('js/summernote.min.js') }}

<script type="text/javascript">


$(document).ready(function() {

    var demoPalette = [
        ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
        ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
        ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
        ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
        ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
        ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
        ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
        ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
    ]

    $(".colorpicker").spectrum({
        showPalette: true,
        palette: demoPalette
    });


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

    $('.auto_node_id').autocomplete({
        source: base + 'ajax/nodeid'
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