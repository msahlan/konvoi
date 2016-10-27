@extends('layouts.formtwo')


@section('left')
        @inject('prefs','App\Helpers\Prefs')
        <?php
            use App\Helpers\Prefs;
            use App\Helpers\Ks;

         ?>

    {{--

contractNumber
creditor',
Type',
dueDate
installmentAmt
pickupDate


    --}}


        {!! Former::text('programName','Nama Program Kredit')  !!}

        @if(Ks::is('Creditor'))
            {!! Former::hidden('creditor', Prefs::getCreditorByPic( Auth::user()->id )->id ) !!}
        @else
            {!! Former::select('creditor')->options(Prefs::getCreditor()->CreditorToSelection( 'id','coName',true ) )->label('Perusahaan Penyedia Kredit')->class('form-control bootstrap-select')  !!}
        @endif

        {!! Former::select('Type')->options( array_merge([''=>'Select Goods Type'] ,config('jc.credit_type')) )->label('Jenis Komoditas / Barang')->class('form-control bootstrap-select')  !!}

        <!-- Default multiselect -->
        <div class="form-group">
            <label>Jenis Kartu Debit yang Digunaka</label>
            <div class="multi-select-full">
                {!! Form::select('cardType[]',Prefs::getCardtype()->CardtypeToSelection( 'label','label',false ), isset($formdata['cardType'])?$formdata['cardType']:null,['class'=>'multiselect','multiple'=>'multiple' ]) !!}
            </div>
        </div>


        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary')) !!}&nbsp;&nbsp;
        {{ HTML::link($back,'Cancel',array('class'=>'btn'))}}


@stop

@section('right')

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