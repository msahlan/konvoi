<?php
return array(

        'api_page_size'=>50,

        'tracker_app'=>'com.kickstartlab.android.jcrider',
        'hub_app'=>'com.kickstartlab.android.jexwarehouse',
        'pickup_app'=>'com.kickstartlab.android.jexpickup',

        'mapdefstatus'=>array(
                'cr_assigned',
                'delivered',
                'pending',
                'returned'
            ),

        'jakartafence'=>array(
            '-5.946085 106.571599',
            '-5.944719 107.237645',
            '-6.593128 107.082463',
            '-6.456390 106.597563',
            '-5.946085 106.571599'),

        'filter_time_base'=>array(
            'delivery_order_active.created'=>'Created Date',
            'assignment_date'=>'Assignment Date',
            'pickuptime'=>'Pickup Date',
            'deliverytime'=>'Delivered Time'),

        'pending_count_choice'=>array(
                ''=>'All',
                '0'=>'0',
                '1'=>'1',
                '2'=>'2',
                '3'=>'3',
                '4'=>'>3'
            ),

        'data_tool_default_status'=>array('delivered'),

        'buckets'=>array(
                'incoming'=>'Incoming',
                'dispatcher'=>'Dispatcher',
                'tracker'=>'Tracker'
            ),
        'move_options'=>array(
                'dispatched'=>'Dispatcher',
                'inprogress'=>'Tracker'
            ),

        'credit_type'=>array(
            'Elektronik'=>'Elektronik',
            'Motor'=>'Motor',
            'Mobil'=>'Mobil',
            'Rumah'=>'Rumah',
            'Apartment'=>'Apartment'
        ),


        'node_type'=>array(
            'hub'=>'Hub',
            'warehouse'=>'Warehouse',
            'courier'=>'Courier',
            '3pl'=>'3PL'),

        'default_incoming_heads'=>array(
            array('Payment Time',array('search'=>true,'sort'=>true, 'style'=>'min-width:90px;','daterange'=>true)),
            array('City',array('search'=>true,'sort'=>true)),
            array('Zone',array('search'=>true,'sort'=>true)),
            array('Address',array('search'=>true,'sort'=>true, 'style'=>'min-width:200px;width:200px;' )),
            array('ZIP',array('search'=>true,'sort'=>true)),
            array('Transaction ID',array('search'=>true,'sort'=>true)),
            array('Status',array('search'=>true,'sort'=>true)),
            array('Nomor Kontrak',array('search'=>true,'sort'=>true)),
            array('Nama dalam Kontrak',array('search'=>true,'sort'=>true)),
            array('Nama Program Cicilan',array('search'=>true,'sort'=>true,'select'=> array_merge([''=>'All'] ,config('credit.credit_type') )   )),
            array('Installment',array('search'=>true,'sort'=>true)),
            array('Pickup Fee',array('search'=>true,'sort'=>true)),
            //array('Device',array('search'=>true,'sort'=>true)),
            //array('Rider',array('search'=>true,'style'=>'min-width:100px;','sort'=>true)),
            //array('Pending',array('search'=>true,'sort'=>true)),
        ),


        'default_incoming_fields'=>array(
            array('assignmentDateTs',array('kind'=>'daterange', 'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupCity',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDistrict',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupAddress',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupZIP',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('transactionId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('status',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true, 'multi'=>array('status','warehouse_status','pickup_status'), 'multirel'=>'OR'  )),
            array('contractNumber',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('contractName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('programName',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('installmentAmt',array('kind'=>'currency','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupFee',array('kind'=>'currency','query'=>'like','pos'=>'both','show'=>true)),
            //array(config('jayon.jayon_devices_table').'.identifier',array('kind'=>'text', 'alias'=>'device', 'query'=>'like','pos'=>'both','show'=>true)),
            //array(config('jayon.jayon_couriers_table').'.fullname',array('kind'=>'text', 'alias'=>'courier' ,'query'=>'like','pos'=>'both','show'=>true )),
            //array('pending_count',array('kind'=>'numeric','query'=>'like','pos'=>'both','show'=>true)),
        ),



        'default_zoning_heads'=>array(
            array('Delivery Time',array('search'=>true,'sort'=>true, 'style'=>'min-width:100px;','daterange'=>true)),
            array('City',array('search'=>true,'style'=>'min-width:100px;','sort'=>true)),
            array('Zone',array('search'=>true,'style'=>'min-width:100px;','sort'=>true)),
            array('Delivery ID',array('search'=>true,'style'=>'','sort'=>true)),
            array('Type',array('search'=>true,'select'=>config('jayon.deliverytype_selector_legacy') ,'style'=>'min-width:125px;','sort'=>true)),
            array('Buyer',array('search'=>true,'sort'=>true)),
            array('W x H x L',array('search'=>true,'sort'=>true)),
            array('Volume',array('search'=>true,'sort'=>true)),
            array('Weight Range',array('search'=>true,'sort'=>true, 'style'=>'min-width:200px;width:200px;' )),
            array('Merchant',array('search'=>true,'sort'=>true,'select'=>config('jayon.deliverytype_selector_legacy') )),
            array('No Kode Penjualan Toko',array('search'=>true,'sort'=>true)),
            array('Fulfillment ID',array('search'=>true,'sort'=>true)),
            array('Shipping Address',array('search'=>true,'sort'=>true)),
            array('Phone',array('search'=>true,'sort'=>true)),
            array('Status',array('search'=>true,'sort'=>true)),
        ),

        'default_zoning_fields'=>array(
            array('assignment_date',array('kind'=>'daterange', 'query'=>'like','pos'=>'both','show'=>true)),
            array('buyerdeliverycity',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('buyerdeliveryzone',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true, 'multi'=>array('pickup_dev_id','pickup_person'), 'multirel'=>'OR' )),
            array('delivery_id',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true )),
            array('delivery_type',array('kind'=>'text','callback'=>'colorizetype', 'query'=>'like','pos'=>'both','show'=>true)),
            array('buyer_name',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('width',array('kind'=>'text' ,'query'=>'like','callback'=>'showWHL' ,'pos'=>'both','show'=>true)),
            array('width',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('weight',array('kind'=>'text','query'=>'like','callback'=>'weightRange' ,'pos'=>'both','show'=>true)),
            array(config('jayon.jayon_members_table').'.merchantname',array('kind'=>'text','alias'=>'merchant_name','callback'=>'merchantInfo' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('merchant_trans_id',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('fulfillment_code',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('shipping_address',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('phone',array('kind'=>'text' ,'callback'=>'phoneList' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('status',array('kind'=>'text','callback'=>'statusList','query'=>'like','pos'=>'both','show'=>true, 'multi'=>array('status','warehouse_status','pickup_status'), 'multirel'=>'OR'  )),
        ),

        'default_courier_heads'=>array(
            array('Pickup Time',array('search'=>true,'sort'=>true, 'style'=>'min-width:90px;','daterange'=>true)),
            array('Device',array('search'=>true,'sort'=>true, 'style'=>'min-width:90px;','daterange'=>true)),
            array('City',array('search'=>true,'sort'=>true)),
            array('Zone',array('search'=>true,'sort'=>true)),
            array('Address',array('search'=>true,'sort'=>true, 'style'=>'min-width:200px;width:200px;' )),
            array('ZIP',array('search'=>true,'sort'=>true)),
            array('Transaction ID',array('search'=>true,'sort'=>true)),
            array('Status',array('search'=>true,'sort'=>true)),
            array('Contract Number',array('search'=>true,'sort'=>true)),
            array('Name on Contract',array('search'=>true,'sort'=>true)),
            array('Type',array('search'=>true,'sort'=>true,'select'=> array_merge([''=>'All'] ,config('credit.credit_type') )   )),
            array('Installment',array('search'=>true,'sort'=>true)),
        ),

        'default_courier_fields'=>array(
            array('assignmentDateTs',array('kind'=>'daterange', 'query'=>'like','pos'=>'both','show'=>true)),
            array('deviceName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupCity',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDistrict',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupAddress',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupZIP',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('transactionId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('status',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true, 'multi'=>array('status','warehouse_status','pickup_status'), 'multirel'=>'OR'  )),
            array('contractNumber',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('contractName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('Type',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('installmentAmt',array('kind'=>'currency','query'=>'like','pos'=>'both','show'=>true)),
        ),


        'default_heads'=>array(
            array('Timestamp',array('search'=>true,'sort'=>true, 'style'=>'min-width:90px;','daterange'=>true)),
            array('Status',array('search'=>true,'sort'=>true)),
            array('PU Time',array('search'=>true,'sort'=>true, 'style'=>'min-width:100px;','daterange'=>true)),
            array('PU Person & Device',array('search'=>true,'style'=>'min-width:100px;','sort'=>true)),
            array('Box',array('search'=>true,'style'=>'','sort'=>true)),
            array('Requested Delivery Date',array('search'=>true,'style'=>'min-width:125px;','sort'=>true, 'daterange'=>true )),
            array('Slot',array('search'=>true,'sort'=>true)),
            array('Zone',array('search'=>true,'sort'=>true)),
            array('City',array('search'=>true,'sort'=>true)),
            array('Shipping Address',array('search'=>true,'sort'=>true, 'style'=>'min-width:200px;width:200px;' )),
            array('No Kode Penjualan Toko',array('search'=>true,'sort'=>true)),
            array('Fulfillment ID',array('search'=>true,'sort'=>true)),
            array('Type',array('search'=>true,'sort'=>true,'select'=>config('jayon.deliverytype_selector_legacy') )),
            array('Merchant & Shop Name',array('search'=>true,'sort'=>true)),
            array('Delivery ID',array('search'=>true,'sort'=>true)),
            array('Directions',array('search'=>true,'sort'=>true)),
            array('TTD Toko',array('search'=>true,'sort'=>true)),
            array('Delivery Charge',array('search'=>true,'sort'=>true)),
            array('COD Surcharge',array('search'=>true,'sort'=>true)),
            array('COD Value',array('search'=>true,'sort'=>true)),
            array('Buyer',array('search'=>true,'sort'=>true)),
            array('ZIP',array('search'=>true,'sort'=>true)),
            array('Phone',array('search'=>true,'sort'=>true)),
            array('W x H x L = V',array('search'=>true,'sort'=>true)),
            array('Weight Range',array('search'=>true,'sort'=>true)),
        ),


        'default_fields'=>array(
            array('ordertime',array('kind'=>'daterange', 'query'=>'like','pos'=>'both','show'=>true)),
            array('status',array('kind'=>'text','callback'=>'statusList','query'=>'like','pos'=>'both','show'=>true, 'multi'=>array('status','warehouse_status','pickup_status'), 'multirel'=>'OR'  )),
            array('pickuptime',array('kind'=>'daterange', 'query'=>'like','pos'=>'both','show'=>true)),
            array('pickup_person',array('kind'=>'text', 'callback'=>'puDisp' ,'query'=>'like','pos'=>'both','show'=>true, 'multi'=>array('pickup_dev_id','pickup_person'), 'multirel'=>'OR' )),
            array('box_count',array('kind'=>'numeric', 'query'=>'like','pos'=>'both','show'=>true)),
            array('buyerdeliverytime',array('kind'=>'daterange','query'=>'like','pos'=>'both','show'=>true)),
            array('buyerdeliveryslot',array('kind'=>'text' , 'query'=>'like', 'pos'=>'both','show'=>true)),
            array('buyerdeliveryzone',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('buyerdeliverycity',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('shipping_address',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('merchant_trans_id',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('fulfillment_code',array('kind'=>'text','callback'=>'dispFBar' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('delivery_type',array('kind'=>'text','callback'=>'colorizetype' ,'query'=>'like','pos'=>'both','show'=>true)),
            array(config('jayon.jayon_members_table').'.merchantname',array('kind'=>'text','alias'=>'merchant_name','query'=>'like','callback'=>'merchantInfo','pos'=>'both','show'=>true)),
            array('delivery_id',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('directions',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('delivery_id',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('delivery_cost',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('cod_cost',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('total_price',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('buyer_name',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('shipping_zip',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('phone',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('volume',array('kind'=>'numeric','query'=>'like','pos'=>'both','show'=>true)),
            array('weight',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
        ),

        'default_dispatched_heads'=>array(
            array('Pickup Time',array('search'=>true,'sort'=>true, 'style'=>'min-width:90px;','daterange'=>true)),
            array('Device',array('search'=>true,'sort'=>true, 'style'=>'min-width:90px;')),
            array('Rider',array('search'=>true,'style'=>'min-width:100px;','sort'=>true)),
            array('City',array('search'=>true,'sort'=>true)),
            array('Zone',array('search'=>true,'sort'=>true)),
            array('Address',array('search'=>true,'sort'=>true, 'style'=>'min-width:200px;width:200px;' )),
            array('ZIP',array('search'=>true,'sort'=>true)),
            array('Transaction ID',array('search'=>true,'sort'=>true)),
            array('Status',array('search'=>true,'sort'=>true)),
            array('Contract Number',array('search'=>true,'sort'=>true)),
            array('Name on Contract',array('search'=>true,'sort'=>true)),
            array('Type',array('search'=>true,'sort'=>true,'select'=> array_merge([''=>'All'] ,config('credit.credit_type') )   )),
            array('Installment',array('search'=>true,'sort'=>true)),

        ),


        'default_dispatched_fields'=>array(
            array('assignmentDateTs',array('kind'=>'daterange', 'query'=>'like','pos'=>'both','show'=>true)),
            array('deviceName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('courierName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupCity',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDistrict',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupAddress',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupZIP',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('transactionId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('status',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true, 'multi'=>array('status','warehouse_status','pickup_status'), 'multirel'=>'OR'  )),
            array('contractNumber',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('contractName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('Type',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('installmentAmt',array('kind'=>'currency','query'=>'like','pos'=>'both','show'=>true)),
        ),

        'default_delivered_heads'=>array(
            array('Transaction ID',array('search'=>true,'sort'=>true)),
            array('POP',array('search'=>true,'sort'=>true)),
            array('Status',array('search'=>true,'sort'=>true)),
            array('Pickup Time',array('search'=>true,'sort'=>true, 'style'=>'min-width:90px;','daterange'=>true)),
            array('Device',array('search'=>true,'sort'=>true, 'style'=>'min-width:90px;')),
            array('Rider',array('search'=>true,'style'=>'min-width:100px;','sort'=>true)),
            array('City',array('search'=>true,'sort'=>true)),
            array('Zone',array('search'=>true,'sort'=>true)),
            array('Address',array('search'=>true,'sort'=>true, 'style'=>'min-width:200px;width:200px;' )),
            array('ZIP',array('search'=>true,'sort'=>true)),
            array('Contract Number',array('search'=>true,'sort'=>true)),
            array('Name on Contract',array('search'=>true,'sort'=>true)),
            array('Type',array('search'=>true,'sort'=>true,'select'=> array_merge([''=>'All'] ,config('credit.credit_type') )   )),
            array('Installment',array('search'=>true,'sort'=>true)),

        ),


        'default_delivered_fields'=>array(
            array('transactionId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('transactionId',array('kind'=>'text', 'callback'=>'picList','query'=>'like','pos'=>'both','show'=>true)),
            array('status',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true, 'multi'=>array('status','warehouse_status','pickup_status'), 'multirel'=>'OR'  )),
            array('assignmentDateTs',array('kind'=>'daterange', 'query'=>'like','pos'=>'both','show'=>true)),
            array('deviceName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('courierName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupCity',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDistrict',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupAddress',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('pickupZIP',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('contractNumber',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('contractName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('Type',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('installmentAmt',array('kind'=>'currency','query'=>'like','pos'=>'both','show'=>true)),

        ),

    );