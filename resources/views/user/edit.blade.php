@extends('layout.form')


@section('left')
    {{--
        {{ Former::hidden('id')->value($formdata['_id']) }}

        <h4>Employee Info</h4>
        {{ Former::text('employeeId','Employee ID') }}

        {{ Former::select('department')->options(Config::get('kickstart.salutation'))->label('Salutation') }}

        {{ Former::text('position','Position') }}

        {{ Former::select('type')->options(array('Staff'=>'Staff','Non Staff'=>'Non Staff'))->label('Employee Type') }}

        {{ Former::text('costControl','Cost Control')->class('form-control form-white') }}
        {{ Former::text('allocControl','Alloc. Control') }}

    --}}

        <h4>User Info</h4>

        {{ Former::select('salutation')->options(Config::get('kickstart.salutation'))->label('Salutation') }}
        {{ Former::text('firstname','First Name') }}
        {{ Former::text('lastname','Last Name') }}
        {{ Former::text('mobile','Mobile') }}

        {{ Former::text('address_1','Address line 1') }}
        {{ Former::text('address_2','Address line 2') }}
        {{ Former::text('city','City') }}

        {{ Former::text('state','State / Province') }}

        {{ Former::select('countryOfOrigin')->id('country')->options(Config::get('country.countries'))->label('Country of Origin') }}

        {{ Former::submit('Save',array('class'=>'btn btn-raised btn-primary'))}}&nbsp;&nbsp;
        {{ HTML::link($back,'Cancel',array('class'=>'btn'))}}

@stop

@section('right')
        <h4>Login Info</h4>

        {{ Former::text('email','Email') }}

        {{ Former::password('pass','Password')->help('Leave blank for no changes') }}
        {{ Former::password('repass','Repeat Password') }}

        {{ Former::select('roleId')->options(Prefs::getRole()->RoleToSelection('_id','rolename' ) )->label('Role')}}

        <h5>Avatar</h5>
        <?php
            $fupload = new Wupload();
        ?>
        {{ $fupload->id('photoupload')
            ->ns('photo')
            ->parentid($formdata['_id'])
            ->parentclass('user')
            ->title('Select Picture')
            ->label('Upload Picture')
            ->url('upload/avatar')
            ->singlefile(true)
            ->prefix('photo')
            ->multi(false)
            ->make($formdata) }}

@stop

@section('modals')

@stop

@section('aux')

<script type="text/javascript">


$(document).ready(function() {


});

</script>

@stop