<div class="row">
    <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
        {{ Former::text('merchant_name','Merchant Name')->class('form-control auto-merchant') }}
        <span class="small-help">please wait for autocomplete</span><br />
        {{ Former::text('merchant_id','Merchant ID')->class('form-control merchant-id')}}

        {{ Former::select('merchant_app','Merchant Application')->class('form-control merchant-app')}}
    </div>
    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        <img src="{{ URL::to('images/loading.gif') }}" class="th-loading" style="display:none;">
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){

	});
</script>