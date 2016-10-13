<?php
    use App\Helpers\Prefs;
?>
<a class="btn btn-raised btn-transparent btn-danger btn-sm" id="generate-data"><i class="fa fa-calendar"></i> Generate Today's Data</a>

<div id="generate-data-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Generate Today's Data</span></h3>
    </div>
    <div class="modal-body" >
    	{!! Former::select('quotaScope','Quota by')->id('quota-scope')->options([ 'province'=>'Province', 'city'=>'City', 'district'=>'District' ]) !!}
        {!! Former::select('creditor','Creditor')->id('creditor-id')->options( Prefs::getCreditor()->CreditorToSelection('_id','coName') ) !!}

    </div>
    <div class="modal-footer">
        <span id="result-msg"></span>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button class="btn btn-raised btn-primary" id="do-generate-data">Confirm</button>
	    <i id="loading-indicator" style="display:none;" class="fa fa-spinner fa-spin fa-2x fa-fw"></i>
    </div>
</div>

<script type="text/javascript">
        $(document).ready(function(){

        $.ajaxSetup({
           'beforeSend': function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", "{!! csrf_token()  !!}" );
            }
        });



        $('#generate-data').on('click',function(e){
            $('#generate-data-modal').modal();
            e.preventDefault();
        });

        $('#do-generate-data').on('click',function(){
            var scope = $('#quota-scope').val();
            var creditor = $('#creditor-id').val();

            if(scope == '' ){

                alert('Please select scope');
            }else{
            	$('#loading-indicator').show();
                $.post('{!! URL::to('ajax/generatedata') !!}',
                    {
	                    day : {!! date('d',time()) !!},
	                    month : {!! date('m',time()) !!},
	                    scope : scope,
                        creditor : creditor
                    },
                    function(data){
                        if(data.status == 'OK'){
                            $('#result-msg').html(data.msg);
                            $('#loading-indicator').hide();
                            $('#generate-data-modal').modal('hide');
                            oTable.draw();
                        }
                        $('#loading-indicator').hide();
                    }
                    ,'json');
            }

        });


    });

</script>
