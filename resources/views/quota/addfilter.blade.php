<?php
use App\Helpers\Prefs;
?>

<div id="update-quota-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Update Quota</span></h3>
    </div>
    <div class="modal-body" >
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <h5>Province</h5>
                <h4 id="province-name" class="form-static"></h4>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <h5>Quota</h5>
                {!! Former::text('provinceQuota', '' )->v_model('provincequota')->id('total-quota')  !!}
            </div>
        </div>
        <div class="row">
            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                {!! Former::text('deviceNum', 'Jumlah Device' )->v_model('devicenum')->id('device-num')  !!}
            </div>
            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            X
            </div>
            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                {!! Former::text('deviceCap', 'Kapasitas per Device' )->v_model('devicecap')->id('device-cap')  !!}
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button class="btn btn-raised btn-primary" id="do-update-quota">Update</button>
    </div>
</div>

<div id="print-modal" class="modal fade large" tabindex="-1" role="dialog" aria-labelledby="myPrintModalLabel" aria-hidden="true">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myPrintModalLabel">Print Selected Codes</span></h3>
    </div>
    <div class="modal-body" style="overflow:auto;" >
        <h6>Print options</h6>
        <div style="border-bottom:thin solid #ccc;" class="row clearfix">
            <div class="col-md-2">
                {!! Former::text('label_columns','Number of columns')->value('4')->id('label_columns')->class('form-control input-sm')  !!}
                {!! Former::text('label_res','Resolution')->value('150')->id('label_res')->class('form-control input-sm')  !!}
            </div>
            <div class="col-md-2">
                {!! Former::text('label_cell_height','Label height')->value('230')->id('label_cell_height')->class('form-control input-sm')  !!}
                {!! Former::text('label_cell_width','Label width')->value('200')->id('label_cell_width')->class('form-control input-sm')  !!}
            </div>
            <div class="col-md-2">
                {!! Former::text('label_margin_right','Label margin right')->value('8')->id('label_margin_right')->class('form-control input-sm')  !!}
                {!! Former::text('label_margin_bottom','Label margin bottom')->value('10')->id('label_margin_bottom')->class('form-control input-sm')  !!}
            </div>
            <div class="col-md-2">
                {!! Former::text('label_offset_right','Page left offset')->value('40')->id('label_offset_right')->class('form-control input-sm')  !!}
                {!! Former::text('label_offset_bottom','Page top offset')->value('20')->id('label_offset_bottom')->class('form-control input-sm')  !!}
            </div>
            <div class="col-md-2">
                {!! Former::text('font_size','Font size')->value('12')->id('font_size')->class('form-control input-sm')  !!}
                {!! Former::select('code_type','Code type')->id('code_type')->options(array('qr'=>'QR','barcode'=>'Barcode') ) !!}
            </div>
            <div class="col-md-2">
                <button id="label_default" class="btn btn-raised btn-primary btn-sm" ><i class="fa fa-save"></i> make default</button>
                <button id="label_refresh" class="btn btn-raised btn-primary btn-sm" ><i class="fa fa-refresh"></i> refresh</button>
            </div>
        </div>
        <input type="hidden" value="" id="session_name" />
        <input type="hidden" value="" id="label_id" />

        <div style="height:100%;width:100%;">
            <iframe id="label_frame" name="label_frame" width="100%" height="90%"
            marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
            title="Dialog Title">Your browser does not suppr</iframe>

        </div>
    </div>
    <div class="modal-footer" style="z-index:20000;">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button class="btn btn-raised btn-primary" id="do-print">Print</button>
    </div>
</div>

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

#ui-datepicker-div{
    z-index: 100000 !important;
}

.datepicker{
    z-index: 100000 !important;
}
</style>

<script type="text/javascript">
        $(document).ready(function(){

        $.ajaxSetup({
           'beforeSend': function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}" );
            }
        });

        vm = new Vue({
            data:{
                devicecap:0,
                devicenum:0
            },
            computed:{
                provincequota: function(){
                    return parseInt(this.devicecap) * parseInt(this.devicenum);
                }
            }
        }).$mount('#update-quota-modal');

        $('#do-update-quota').on('click',function(){

            $.post('{{ URL::to('pickup/quota/savequota')}}',
                {
                    devnum : $('#device-num').val(),
                    devcap : $('#device-cap').val(),
                    province : $('#province-name').html(),
                    quota : parseInt( $('#total-quota').val() )
                },
                function(data){
                    if(data.result == 'OK'){
                        oTable.draw();
                        $('#update-quota-modal').modal('hide');
                    }
                }
                ,'json');

        });

        $('#update-quota-modal').on('hidden.bs.modal',function(){
            $('#total-quota').val(0);
        });

        $('#refresh_filter').on('click',function(){
            oTable.draw();
        });

        $('#outlet_filter').on('change',function(){
            oTable.draw();
        });

        $('#move_orders').on('click',function(e){
            $('#move-order-modal').modal();
            e.preventDefault();
        });

        $('#set_pickup').on('click',function(e){
            $('#set-pickup-date-modal').modal();
            e.preventDefault();
        });

        $('#label_refresh').on('click',function(){

            var sessionname = $('#session_name').val();

            var col = $('#label_columns').val();
            var res = $('#label_res').val();
            var cell_width = $('#label_cell_width').val();
            var cell_height = $('#label_cell_height').val();
            var margin_right = $('#label_margin_right').val();
            var margin_bottom = $('#label_margin_bottom').val();
            var font_size = $('#font_size').val();
            var code_type = $('#code_type').val();
            var offset_left = $('#label_offset_left').val();
            var offset_top = $('#label_offset_top').val();
            var src = '{{ URL::to('incoming/printlabel')}}/' + sessionname + '/' + col + ':' + res + ':' + cell_width + ':' + cell_height + ':' + margin_right + ':' + margin_bottom + ':' + font_size + ':' + code_type + ':' + offset_left + ':' + offset_top;

            $('#label_frame').attr('src',src);

            e.preventDefault();
        });

        $('#print_barcodes').on('click',function(){

            var props = $('.selector:checked');
            var ids = [];
            $.each(props, function(index){
                ids.push( $(this).val() );
            });

            console.log(ids);

            if(ids.length > 0){
                $.post('{{ URL::to('ajax/sessionsave')}}',
                    {
                        data_array : ids
                    },
                    function(data){
                        if(data.result == 'OK'){
                            $('#session_name').val(data.sessionname);

                            var col = $('#label_columns').val();
                            var res = $('#label_res').val();
                            var cell_width = $('#label_cell_width').val();
                            var cell_height = $('#label_cell_height').val();
                            var margin_right = $('#label_margin_right').val();
                            var margin_bottom = $('#label_margin_bottom').val();
                            var font_size = $('#font_size').val();
                            var code_type = $('#code_type').val();
                            var offset_left = $('#label_offset_left').val();
                            var offset_top = $('#label_offset_top').val();
                            var src = '{{ URL::to('incoming/printlabel')}}/' + data.sessionname + '/' + col + ':' + res + ':' + cell_width + ':' + cell_height + ':' + margin_right + ':' + margin_bottom + ':' + font_size + ':' + code_type + ':' + offset_left + ':' + offset_top;
                            $('#label_frame').attr('src',src);
                            $('#print-modal').modal('show');
                        }else{
                            $('#print-modal').modal('hide');
                        }
                    }
                    ,'json');

            }else{
                alert('No item selected.');
                $('#print-modal').modal('hide');
            }
ß
        });

        $('#do-print').click(function(){

            var pframe = document.getElementById('label_frame');
            var pframeWindow = pframe.contentWindow;
            pframeWindow.print();

        });


        $('#label_default').on('click',function(){
            var col = $('#label_columns').val();
            var res = $('#label_res').val();
            var cell_width = $('#label_cell_width').val();
            var cell_height = $('#label_cell_height').val();
            var margin_right = $('#label_margin_right').val();
            var margin_bottom = $('#label_margin_bottom').val();
            var font_size = $('#font_size').val();
            var code_type = $('#code_type').val();
            var offset_left = $('#label_offset_left').val();
            var offset_top = $('#label_offset_top').val();

            $.post('{{ URL::to('ajax/printdefault')}}',
                {
                    col : col,
                    res : res,
                    cell_width : cell_width,
                    cell_height : cell_height,
                    margin_right : margin_right,
                    margin_bottom : margin_bottom,
                    font_size : font_size,
                    code_type : code_type,
                    offset_left : offset_left,
                    offset_top : offset_top
                },
                function(data){
                    if(data.result == 'OK'){
                        alert('Print parameters set as default');
                    }else{
                        alert('Print parameters failed to set default');
                    }
                }
                ,'json');
                e.preventDefault();

        });

        $('#do-move').on('click',function(){
            var props = $('.selector:checked');
            var ids = [];
            $.each(props, function(index){
                ids.push( $(this).val() );
            });

            console.log(ids);

            if(ids.length > 0){
                $.post('{{ URL::to('ajax/moveorder')}}',
                    {
                        bucket : $('#move-to').val(),
                        ids : ids
                    },
                    function(data){
                        $('#move-order-modal').modal('hide');
                        oTable.draw();
                    }
                    ,'json');

            }else{
                alert('No shipment selected.');
                $('#move-order-modal').modal('hide');
            }

        });



    });
</script>