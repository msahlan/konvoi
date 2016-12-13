@extends('layouts.formtwo')


@section('left')
        @inject('prefs','App\Helpers\Prefs')


    {{--

        <h4>Employee Info</h4>

        {{ Former::text('employeeId','Employee ID') }}

        {{ Former::select('department')->options(Config::get('kickstart.salutation'))->label('Salutation') }}

        {{ Former::text('position','Position') }}

        {{ Former::select('type')->options(array('Staff'=>'Staff','Non Staff'=>'Non Staff'))->label('Employee Type') }}

        {{ Former::text('costControl','Cost Control')->class('form-control form-white') }}
        {{ Former::text('allocControl','Alloc. Control') }}

        {!! Former::select('salutation')->options(config('kickstart.salutation'))->label('Salutation')  !!}

    --}}


        <h4>Create Konvoi</h4>

        
        {!! Former::text('name','Full Name')  !!}
        {!! Former::text('mobile','Mobile')  !!}

        {!! Former::text('email','Email')  !!}

        {!! Former::text('address','Address')  !!}

        {!! Former::date('date_konvoi','Tanggal Konvoi')  !!}

        {!! Former::text('destination','Kota Tujuan') !!}

        {!! Former::textarea('description','Deskripsi Konvoi / Note') !!}


        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary')) !!}&nbsp;&nbsp;
        {{ HTML::link($back,'Cancel',array('class'=>'btn'))}}

@stop

{{--
@section('right')


        <h4>Deskripsi Konvoi </h4>

        {!! Former::text('email','Email') !!}

        {!! Former::password('password','Password')->help('Leave blank for no changes') !!}
        {!! Former::password('repass','Repeat Password') !!}

        {!! Former::select('roleId')->options($prefs->getRole()->RoleToSelection('_id','rolename' ) )->label('Role')!!}

        <h4>Avatar</h4>

        {!! $fupload->id('photoupload')
            ->ns('avatar')
            ->title('Select Photo')
            ->label('Upload Photo')
            ->url('upload/avatar')
            ->singlefile(true)
            ->prefix('photo')
            ->multi(false)->make() !!}

@endsection
--}}
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