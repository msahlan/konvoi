@extends('layouts.formtwo')

@section('page_js')
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/tags/tagsinput.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/tags/tokenfield.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/prism.min.js"></script>

    {{ HTML::script('js/autotoken.js')}}

@endsection

@section('left')

        <?php
            use App\Helpers\Prefs;
        ?>

        <h4>Organization Info</h4>

        {!! Former::text('coName','Name')  !!}

        {!! Former::text('code','Company Code')  !!}

        {!! Former::text('coEmail','Email') !!}
        {!! Former::text('coUrl','Website') !!}

        {!! Former::text('coPhone','Phone')  !!}
        {!! Former::text('coFax','Fax')  !!}

        {!! Former::text('address_1','Address line 1')  !!}
        {!! Former::text('address_2','Address line 2')  !!}
        {!! Former::text('city','City')  !!}

        {!! Former::text('province','State / Province')  !!}


        {!! Former::select('countryOfOrigin')->id('country')->options(config('country.countries'))->label('Country of Origin')  !!}

        {!! Former::hidden('roleId', Prefs::getRoleId('Creditor') ) !!}


        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary')) !!}&nbsp;&nbsp;
        {{ HTML::link($back,'Cancel',array('class'=>'btn'))}}

@stop

@section('right')

        <h4>PIC Info</h4>

        {!! Former::text('email','Email')  !!}

        {!! Former::text('picId','User Id ( if already exists )')  !!}

        {!! Former::text('name','Full Name')  !!}

        {!! Former::text('phone','Phone')  !!}

        {!! Former::text('mobile','Phone / Mobile')  !!}

        {!! Former::text('password','Password')  !!}

        {!! Former::text('password_confirmation','Confirm Password')  !!}

        <h4>Pickup Fee</h4>

        {!! Former::text('pickupFee','Pickup Fee')  !!}

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