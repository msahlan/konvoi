@extends('layouts.formtwo')


@section('left')
        @inject('prefs','App\Helpers\Prefs')

        <h4>Organization Info</h4>

        {!! Former::text('coName','Name')  !!}

        {!! Former::text('code','Company Code')  !!}

        {!! Former::text('email','Email') !!}
        {!! Former::text('url','Website') !!}

        {!! Former::text('phone','Phone')  !!}

        {!! Former::text('address_1','Address line 1')  !!}
        {!! Former::text('address_2','Address line 2')  !!}
        {!! Former::text('city','City')  !!}

        {!! Former::text('province','State / Province')  !!}

        {!! Former::select('countryOfOrigin')->id('country')->options(config('country.countries'))->label('Country of Origin')  !!}


        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary')) !!}&nbsp;&nbsp;
        {{ HTML::link($back,'Cancel',array('class'=>'btn'))}}

@stop

@section('right')

        <h4>PIC Info</h4>

        {!! Former::text('picName','Full Name')  !!}

        <h4>Logo</h4>

        {!! $fupload->id('photoupload')
            ->ns('logo')
            ->title('Select Photo')
            ->label('Upload Photo')
            ->url('upload/logo')
            ->singlefile(true)
            ->prefix('creditor')
            ->multi(false)->make() !!}

@endsection

@section('modals')

@endsection

@section('aux')

<script type="text/javascript">


$(document).ready(function() {


    $('.pick-a-color').pickAColor();

    $('#name').keyup(function(){
        var title = $('#name').val();
        var slug = string_to_slug(title);
        $('#permalink').val(slug);
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


});

</script>

@endsection