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
         ?>

        {!! Former::text('shortCode','Kode Unik (3 karakter)')  !!}
        {!! Former::text('name','Nama')  !!}
        {!! Former::text('description','Deskripsi')  !!}

        {!! Form::submit('Save',array('class'=>'btn btn-raised btn-primary')) !!}&nbsp;&nbsp;
        {{ HTML::link($back,'Cancel',array('class'=>'btn'))}}

@stop

@section('right')


@endsection

@section('modals')

@endsection

@section('aux')


@endsection