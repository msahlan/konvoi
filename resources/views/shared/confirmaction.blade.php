<a class="btn btn-raised btn-transparent btn-info btn-sm" id="confirm_data"><i class="fa fa-calendar"></i> Confirm Data</a>

<div id="confirm-data-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Confirm Data</span></h3>
    </div>
    <div class="modal-body" >
        {{ Former::textarea('confirm_reason', 'Reason' )->id('confirm-reason')->class('form-control') }}
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button class="btn btn-raised btn-primary" id="do-confirm-data">Confirm</button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        $('#confirm_data').on('click',function(e){
            $('#confirm-data-modal').modal();
            e.preventDefault();
        });

        $('#do-confirm-data').on('click',function(){
            var props = $('.selector:checked');
            var ids = [];
            var reason = $('#confirm-reason').val();

            $.each(props, function(index){
                ids.push( $(this).val() );
            });

            console.log(ids);

            if(ids.length > 0){
                if(reason == '' ){

                    alert('Please specify reason for cancelation');
                }else{
                    $.post('{{ URL::to('ajax/confirmdata')}}',
                        {
                            ids : ids,
                            reason : reason
                        },
                        function(data){
                            $('#confirm-data-modal').modal('hide');
                            oTable.draw();
                        }
                        ,'json');
                }

            }else{
                alert('No item selected.');
            }

        });


    });

</script>