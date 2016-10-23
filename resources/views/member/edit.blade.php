@extends('layouts.formtwo')


@section('left')
        @inject('prefs','App\Helpers\Prefs')

        <h4>Member Info</h4>

        {!! Former::text('name','Full Name')  !!}
        {!! Former::text('phone','Phone')  !!}
        {!! Former::text('mobile','Phone / Mobile')  !!}

        {!! Former::select('bankCard')->options(array_merge([''=>'Select Debit Card used'] ,config('card.issuer')) )->label('Debit Card')  !!}

        {!! Former::text('address_1','Address line 1')  !!}
        {!! Former::text('address_2','Address line 2')  !!}
        {!! Former::text('city','City')  !!}

        {!! Former::text('state','State / Province')  !!}

        {!! Former::select('countryOfOrigin')->id('country')->options(config('country.countries'))->label('Country of Origin')  !!}

        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary')) !!}&nbsp;&nbsp;
        {{ HTML::link($back,'Cancel',array('class'=>'btn'))}}

@stop

@section('right')


        <h4>Login Info</h4>

        {!! Former::text('email','Email') !!}

        {!! Former::password('password','Password')->help('Leave blank for no changes') !!}
        {!! Former::password('repass','Repeat Password') !!}

        <h4>Avatar</h4>

        {!! $fupload->id('photoupload')
            ->parentid($formdata['_id'])
            ->ns('avatar')
            ->title('Select Photo')
            ->label('Upload Photo')
            ->url('upload/avatar')
            ->singlefile(true)
            ->prefix('photo') // this will try to get $prefix.wdetail item template
            ->multi(false)
            ->make($formdata) !!}

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