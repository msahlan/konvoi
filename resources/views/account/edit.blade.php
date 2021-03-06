@extends('layouts.formtwo')

@section('page_js')
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/tags/tagsinput.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/tags/tokenfield.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/prism.min.js"></script>

    {{ HTML::script('js/autotoken.js')}}


    <script type="text/javascript">


    $(document).ready(function() {

        $.ajaxSetup({
           'beforeSend': function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}" );
            }
        });

        $('.pick-a-color').pickAColor();
        /*
        $('#creditor-id').on('changed.bs.select',function(e){

            $.post('{{ url('ajax/creditprogram') }}',
                {
                    id : this.value
                },
                function(data) {
                    //console.log(data);
                    $('#program-name').html(data);
                    $('#program-name').selectpicker('refresh');
                },'html');
        });
        */

        $('#product-type').on('changed.bs.select',function(e){

            $.post('{{ url('ajax/creditprogram') }}',
                {
                    id : $('#creditor-id').val(),
                    type : this.value
                },
                function(data) {
                    //console.log(data);
                    $('#program-name').html(data);
                    $('#program-name').selectpicker('refresh');

                    $.post('{{ url('ajax/programname') }}',
                        {
                            id : $('#program-name').val()
                        },
                        function(data) {
                            //console.log(data);
                            $('#bank-card').html(data);
                            $('#bank-card').selectpicker('refresh');
                        },'html');

                },'html');
        });

        $('#program-name').on('changed.bs.select',function(e){

            $.post('{{ url('ajax/programname') }}',
                {
                    id : $('#program-name').val()
                },
                function(data) {
                    //console.log(data);
                    $('#bank-card').html(data);
                    $('#bank-card').selectpicker('refresh');
                },'html');
        });

        $('#province').on('changed.bs.select',function(e){

            $.post('{{ url('ajax/city') }}',
                {
                    id : this.value
                },
                function(data) {
                    //console.log(data);
                    $('#city').html(data);
                    $('#city').selectpicker('refresh');
                },'html');
        });

        $('#city').on('changed.bs.select',function(e){

            $.post('{{ url('ajax/district') }}',
                {
                    id : this.value
                },
                function(data) {
                    //console.log(data);
                    $('#district').html(data);
                    $('#district').selectpicker('refresh');
                },'html');
        });

        $('#name').keyup(function(){
            var title = $('#name').val();
            var slug = string_to_slug(title);
            $('#permalink').val(slug);
        });



    });

    </script>

@endsection

@section('left')
        <?php
            use App\Helpers\Prefs;
            use App\Helpers\Ks;

            $cities = Prefs::cityDistrictSelect($formdata['pickupProvince'], $formdata['pickupCity']);

            $city = array_keys($cities);
            foreach($city as $c){
                $citysel[$c] = $c;
            }

            $district = isset($cities[$formdata['pickupCity']])?$cities[$formdata['pickupCity']]:[];

            foreach($district as $d){
                $districtsel[$d] = $d;
            }

         ?>

        <h4>Informasi Kontrak Kredit</h4>

        {!! Former::text('contractNumber','Nomor Kontrak')  !!}
        {!! Former::text('contractName','Atas Nama')  !!}

        @if(Ks::is('Creditor'))
            {!! Former::hidden('creditor', Prefs::getCreditorByPic( Auth::user()->id )->id )->id('creditor-id') !!}
        @else
            {!! Former::select('creditor')->options(Prefs::getCreditor()->CreditorToSelection( 'id','coName',true ) )->label('Perusahaan Penyedia Kredit')->id('creditor-id')->class('form-control bootstrap-select')  !!}
        @endif

        {!! Former::select('Type')->options( array_merge([''=>'Select Goods Type'] ,config('jc.credit_type')) )->label('Jenis Barang')->class('form-control bootstrap-select')->id('product-type')  !!}

        @if(Ks::is('Member'))
            {!! Former::select('programName')->options( [''=>'Select Credit Type'] )->label('Program Kredit')->id('program-name')->class('form-control bootstrap-select')  !!}
        @else
            {!! Former::text('programName','Program Kredit')->id('program-name')->class('form-control auto-program')  !!}
        @endif

        {!! Former::select('bankCard')->options( [''=>'Select Debit Card'] )->label('Debit Card')->id('bank-card')->class('form-control bootstrap-select')  !!}

        {!! Former::text('productDescription','Deskripsi Produk')  !!}

        {!! Former::text('dueDate','Tanggal Jatuh Tempo ( setiap bulan )')  !!}
        {!! Former::text('installmentAmt','Jumlah Tagihan')  !!}
        {!! Former::text('pickupDate','Tanggal Pembayaran Yang Diinginkan ( min. 2 hari sebelum Jatuh Tempo) ')  !!}


@stop

@section('right')

        <h4>Alamat Pengambilan Pembayaran</h4>
        <p>Mohon diisi sebenar-benarnya untuk mempermudah kunjungan</p>

        {!! Former::textarea('pickupAddress','Alamat Pengambilan Pembayaran')  !!}

        {!! Former::select('pickupProvince','Propinsi')
                ->options( Prefs::getProvince()->ProvinceToSelection('province','province') )->id('province')->class('form-control bootstrap-select')  !!}

        {!! Former::select('pickupCity')->options( array_merge([''=>'Select City'], $citysel )  )->label('Kota')->id('city')->class('form-control bootstrap-select')->select($formdata['pickupCity'])  !!}

        {!! Former::select('pickupDistrict')->options( array_merge([''=>'Select District'], $districtsel) )->label('Kecamatan')->id('district')->class('form-control bootstrap-select')->select($formdata['pickupDistrict'])  !!}

        {!! Former::text('pickupZIP','Kode Pos')  !!}

        @if(Ks::is('Member'))
            {!! Former::hidden('payerEmail', Auth::user()->email )->id('creditor-id') !!}
        @else
            <h4>Akun Pembayar</h4>
            <p>Untuk identifikasi jika kontrak dimiliki oleh member JC</p>
            {!! Former::text('payerEmail','Email Pembayar')  !!}
        @endif

        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary')) !!}&nbsp;&nbsp;
        {{ HTML::link($back,'Cancel',array('class'=>'btn'))}}

@endsection

@section('modals')

@endsection

@section('aux')


@endsection