<a class="btn btn-transparent btn-info btn-sm" id="assign_to_device"><i class="fa fa-phone-square"></i> Assign to Device</a>

<div id="device-assign-modal" class="modal fade large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Assign Selected</span></h3>
    </div>
    <div class="modal-body" >
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <table class="table" id="shipment_list">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="dev_select_all" /></th>
                            <th>Order ID</th>
                            <th>Fulfillment</th>
                            <th>City</th>
                            <th>Package Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <td colspan="3">Loading shipment data...</td>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="scroll:auto;height:100%;">
                <table class="table" id="device_list">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Device Name</th>
                            <th>Current Load</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3">Loading available devices...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button class="btn btn-primary" id="do-assign">Assign</button>
    </div>
</div>

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

        $('#dev_select_all').on('click',function(){
            if($('#dev_select_all').is(':checked')){
                $('.shipselect').prop('checked', true);
            }else{
                $('.shipselect').prop('checked',false);
            }
        });

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

        $('#assign_to_device').on('click',function(e){
            var date = $('input[name=date_select]:checked').val();
            var city = $('input[name=city_select]:checked').val();

            if(date == '' || city == ''){
                alert('Please select date AND city');
            }else{
                $('#device-assign-modal').modal();
            }

            e.preventDefault();
        });

        $('#device-assign-modal').on('shown',function(){
            var date = $('input[name=date_select]:checked').val();
            var city = $('input[name=city_select]:checked').val();

            $('table#shipment_list tbody').html('<tr><td colspan="3">Loading shipment data...</td></tr>');
            $('table#device_list tbody').html('<tr><td colspan="3">Loading available devices...</td></tr>');


            $.post('{{ URL::to($ajaxdeviceurl)}}',
                {
                    date : date,
                    city : city
                },
                function(data){
                    if(data.result == 'OK'){

                        var device_list = data.device;
                        var shipment_list = data.shipment;

                        $('table#shipment_list tbody').html('');
                        $('table#device_list tbody').html('');

                        if(shipment_list.length > 0){
                            $('table#shipment_list tbody').html('');
                        }

                        if(device_list.length > 0){
                            $('table#device_list tbody').html('');
                        }
                        $.each(device_list, function(index, val) {
                            $('table#device_list tbody').prepend('<tr><td><input type="radio" name="dev" class="devselect" value="' + val.id + '" ></td><td><b>' + val.identifier + '</b></td><td>' + val.count + '</td></tr>' + '<tr><td>&nbsp;</td><td colspan="2">' + val.city + '</td></tr>');
                        });

                        $.each(shipment_list, function(index, val) {

                            $('table#shipment_list tbody').prepend('<tr><td><input type="checkbox" class="shipselect" name="ship" value="' + val.delivery_id + '" ></td><td>' + val.delivery_id + '</td><td>' + val.fulfillment_code + '</td><td>' + val.buyerdeliverycity + '</td><td>' + val.box_count + '</td></tr>');
                        });

                    }else{

                    }
                }
                ,'json');

            console.log(date);

        });


        $('#do-assign').on('click',function(){
            var ships = $('.shipselect:checked');

            var device = $('.devselect:checked');

            //var props = $('.selector:checked');
            var ids = [];
            $.each(ships, function(index){
                ids.push( $(this).val() );
            });

            console.log(ids);

            console.log(device.val());

            if(ids.length > 0){
                $.post('{{ URL::to('zoning/assigndevice')}}',
                    {
                        device : device.val(),
                        ship_ids : ids
                    },
                    function(data){
                        $('#device-assign-modal').modal('hide');
                        oTable.draw();
                    }
                    ,'json');

            }else{
                alert('No product selected.');
                $('#device-assign-modal').modal('hide');
            }

        });



    });
</script>