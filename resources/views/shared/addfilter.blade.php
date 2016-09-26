<?php
    $dperiod = date('Y-m-d', time());
?>
{!!Former::open_for_files_vertical(URL::to($submit_url),'GET',array('class'=>'filter-form'))->id('filter-form')!!}
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {!! Former::select('device','Device')
                    ->selected(Input::get('device'))
                    ->options(Prefs::getDevice()->deviceToSelection('id','identifier'))
                !!}
            {!! Former::select('courier','Courier')
                    ->selected(Input::get('courier'))
                    ->options(Prefs::getCourier()->courierToSelection('id','fullname'))
                !!}
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {!! Former::select('merchant','Merchant')
                    ->selected(Input::get('merchant'))
                    ->options(Prefs::getMerchant()->merchantToSelection('id','merchantname' ))
                !!}

            {!! Former::select('logistic','Logistic')
                    ->selected(Input::get('logistic'))
                    ->options(Prefs::getLogistic()->logisticToSelection('logistic_code','name'))
                !!}

        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {!! Former::text('date-from', 'From Date')
                    ->value(Input::get('date-from',$dperiod))
                    ->class('form-control input-sm p-datepicker')
                    ->id('date-from');
            !!}
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {!! Former::text('date-to', 'To Date')
                    ->value(Input::get('date-to',$dperiod))
                    ->class('form-control input-sm p-datepicker')
                    ->id('date-to');
            !!}
        </div>
        {!!--
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {!! Former::text('date-from', 'Delivery Date')
                    ->value(Input::get('date-from',$dperiod))
                    ->class('form-control input-sm p-datepicker')
                    ->id('date-from');
            !!}
        </div>
        --!!}
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {!! Former::text('manifest-date', 'Publish Date')
                    ->value(Input::get('manifest-date',$dperiod))
                    ->class('form-control input-sm p-datepicker')
                    ->id('manifest-date');
            !!}
            {!!--
                Former::text('date-to', 'To')
                    ->value(Input::get('date-to',$dperiod))
                    ->class('form-control input-sm p-datepicker')
                    ->id('date-to');
            --!!}
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            {!! Form::submit('Generate',array('class'=>'btn btn-raised btn-sm btn-primary'))!!}
            <br />
            <a class="btn btn-raised btn-sm" href="{!! URL::to($submit_url) !!}">Reset</a>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            {!! Former::text('status','Delivery Status')
                    ->value(Input::get('status', implode(',', Config::get('jayon.devmanifest_default_status') ) ))
                    ->class('form-control tokenfield-status') !!}
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            {!! Former::text('courier-status','Courier Status')
                    ->value(Input::get('courier-status', implode(',', Config::get('jayon.devmanifest_default_courier_status') ) ))
                    ->class('form-control tokenfield-courierstatus') !!}
        </div>
    </div>

{!!--
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            <h5>Account</h5>
        </div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            {!! Former::select('acc-code-from', '')
                    ->options(Prefs::getCoa()->CoaToSelection('ACNT_CODE', 'ACNT_CODE', false ), Input::get('acc-code-from')  )
                    ->class('form-control form-white input-sm')
                    ->id('acc-code-from');
            !!}

        </div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            {!! Former::select('acc-code-to', '')
                    ->options(Prefs::getCoa()->CoaToSelection('ACNT_CODE', 'ACNT_CODE', false ), Input::get('acc-code-to') )
                    ->class('form-control form-white input-sm')
                    ->id('acc-code-to');
            !!}
        </div>
    </div>
    --!!}


</form>

<span class="syncing" style="display:none;">Processing...</span>


<style type="text/css">

.modal.large {
    width: 80%; /* respsonsive width */
    margin-left:-40%; /* width/2) */
}

.modal.large .modal-body{
    max-height: 800px;
    height: 500px;
    overflow: auto;
}

button#label_refresh{
    margin-top: 27px;
}

button#label_default{
    margin-top: 28px;
}

</style>

<script type="text/javascript">
        $(document).ready(function(){

        $.ajaxSetup({
           'beforeSend': function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", "{!! csrf_token() !!}" );
            }
        });



        layout.form

        $('#company-code').on('change',function(){
            oTable.draw();
        });

        $('#assign-product').on('click',function(e){
            $('#assign-modal').modal();
            e.preventDefault();
        });

        $('#do-generate').on('click',function(){
            oTable.draw();
            e.preventDefault();
        });

        $('#acc-company').select2().on('change',function(){
            $.post('{!! URL::to('ajax/afe') !!}',
                {
                    code: $('#acc-company').val()
                },
                function(data) {
                    if(data.result == 'OK'){
                        var output = '';
                        $.each(data.selection, function(key, value) {
                            output += '<option value="' + key + '" >' + value + '</option>';
                        });
                        $('#acc-afe').select2('destroy');
                        $('#acc-afe').html(output);
                        $('#acc-afe').select2();


                    }
                },'json');
        });

    });
</script>