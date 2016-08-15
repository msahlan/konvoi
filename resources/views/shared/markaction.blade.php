<a class="btn btn-raised btn-primary btn-info btn-sm" id="mark_email"><i class="fa fa-envelope"></i> Mark Same Email</a>
<a class="btn btn-raised btn-primary btn-danger btn-sm" id="unmark_email"><i class="fa fa-envelope"></i> Remove Email Mark</a>
<a class="btn btn-raised btn-primary btn-info btn-sm" id="mark_phone"><i class="fa fa-phone"></i> Mark Same Phone</a>
<a class="btn btn-raised btn-primary btn-danger btn-sm" id="unmark_phone"><i class="fa fa-phone"></i> Remove Phone Mark</a>
<a class="btn btn-raised btn-primary btn-info btn-sm" id="mark_blacklist"><i class="fa fa-user"></i> Mark Black List</a>
<a class="btn btn-raised btn-primary btn-danger btn-sm" id="unmark_blacklist"><i class="fa fa-user"></i> Remove Black List</a>


<div id="mark-data-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Confirm Data</span></h3>
    </div>
    <div class="modal-body" >
        {{ Former::textarea('mark_reason', 'Reason' )->id('mark-reason')->class('form-control') }}
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Confirm</button>
        <button class="btn btn-raised btn-primary" id="do-mark-data">Save</button>
    </div>
</div>

<script type="text/javascript">
        $(document).ready(function(){

        $.ajaxSetup({
           'beforeSend': function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}" );
            }
        });



        $('#mark_phone').on('click',function(e){

            var props = $('.selector:checked');
            var ids = [];

            $.each(props, function(index){
                ids.push( $(this).val() );
            });

            $.post('{{ URL::to('ajax/mark')}}',
            {
                ids : ids,
                action : 'mark_phone'
            },
            function(data){
                if(data.result == 'OK'){
                    alert('Phones Marked')
                }
                oTable.draw();
            }
            ,'json');

            e.preventDefault();
        });

        $('#unmark_phone').on('click',function(e){

            var props = $('.selector:checked');
            var ids = [];

            $.each(props, function(index){
                ids.push( $(this).val() );
            });

            $.post('{{ URL::to('ajax/mark')}}',
            {
                ids : ids,
                action : 'unmark_phone'
            },
            function(data){
                if(data.result == 'OK'){
                    alert('Phones Unmarked')
                }
                oTable.draw();
            }
            ,'json');

            e.preventDefault();
        });

        $('#mark_email').on('click',function(e){

            var props = $('.selector:checked');
            var ids = [];

            $.each(props, function(index){
                ids.push( $(this).val() );
            });

            $.post('{{ URL::to('ajax/mark')}}',
            {
                ids : ids,
                action : 'mark_email'
            },
            function(data){
                if(data.result == 'OK'){
                    alert('Emails Marked')
                }
                oTable.draw();
            }
            ,'json');

            e.preventDefault();
        });

        $('#unmark_email').on('click',function(e){

            var props = $('.selector:checked');
            var ids = [];

            $.each(props, function(index){
                ids.push( $(this).val() );
            });

            $.post('{{ URL::to('ajax/mark')}}',
            {
                ids : ids,
                action : 'unmark_email'
            },
            function(data){
                if(data.result == 'OK'){
                    alert('Emails Unmarked')
                }
                oTable.draw();
            }
            ,'json');

            e.preventDefault();
        });

    });

</script>