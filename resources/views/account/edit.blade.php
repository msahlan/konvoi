@extends('layouts.formtwo')


@section('left')
        @inject('prefs','App\Helpers\Prefs')
        <?php use App\Helpers\Prefs; ?>

    {{--

contractNumber
creditor',
Type',
dueDate
installmentAmt
pickupDate


    --}}


        <h4>Informasi Akun</h4>

        {!! Former::text('contractNumber','Nomor Kontrak')  !!}
        {!! Former::text('contractName','Atas Nama')  !!}
        {!! Former::select('creditor')->options(Prefs::getCreditor()->CreditorToSelection( 'id','coName',true ) )->label('Perusahaan Penyedia Kredit')->class('form-control bootstrap-select')  !!}

        {!! Former::select('Type')->options( array_merge([''=>'Select Credit Type'] ,config('jc.credit_type')) )->label('Jenis Kredit')->class('form-control bootstrap-select')  !!}
        {!! Former::text('dueDate','Tanggal Jatuh Tempo ( setiap bulan )')  !!}
        {!! Former::text('installmentAmt','Jumlah Tagihan')  !!}
        {!! Former::text('pickupDate','Tanggal Pembayaran Yang Diinginkan ( min. 2 hari sebelum Jatuh Tempo) ')  !!}


@stop

@section('right')

        <h4>Alamat Pengambilan Pembayaran</h4>
        <p>Mohon diisi sebenar-benarnya untuk mempermudah kunjungan</p>

        {!! Former::textarea('pickupAddress','Alamat Pengambilan Pembayaran')  !!}

        {!! Former::select('pickupProvince','Propinsi')->options( Prefs::getProvince()->ProvinceToSelection('province','province') )  !!}

        {!! Former::text('pickupCity','Kota')  !!}

        {!! Former::text('pickupDistrict','Kecamatan')  !!}

        {!! Former::text('pickupZIP','Kode Pos')  !!}


        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary')) !!}&nbsp;&nbsp;
        {{ HTML::link($back,'Cancel',array('class'=>'btn'))}}

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