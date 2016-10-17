<?php
    use App\Helpers\Prefs;
    use App\Helpers\Ks;
?>
<div class="row">
    <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
        @if(Ks::is('Creditor'))
            {!! Former::hidden('creditor', Prefs::getCreditorByPic( Auth::user()->id )->id ) !!}
            {!! Former::hidden('creditorName', Prefs::getCreditorByPic( Auth::user()->id )->coName ) !!}
        @else
            {!! Former::select('creditor','Creditor')->options(Prefs::getCreditor()->CreditorToSelection('_id','coName') ) !!}
        @endif
    </div>
    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        <img src="{{ URL::to('images/loading.gif') }}" class="th-loading" style="display:none;">
    </div>
</div>

<script type="text/javascript">
	    $(document).ready(function(){

        $.ajaxSetup({
           'beforeSend': function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}" );
            }
        });



	});
</script>