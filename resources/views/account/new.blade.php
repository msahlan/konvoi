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


        <h4>Account Info</h4>

        {!! Former::text('contractNumber','Contract Number')  !!}
        {!! Former::select('creditor')->options(Prefs::getCreditor()->CreditorToSelection( 'id','coName',true ) )->label('Credit Company')  !!}

        {!! Former::select('Type')->options( array_merge([''=>'Select Credit Type'] ,config('jc.credit_type')) )->label('Credit Company')  !!}
        {!! Former::text('dueDate','Due Date ( every month )')  !!}
        {!! Former::text('installmentAmt','Installment Amount')  !!}
        {!! Former::text('pickupDate','JC Payment Visit Date ( every month )')  !!}


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