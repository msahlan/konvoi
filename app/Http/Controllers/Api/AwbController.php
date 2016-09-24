<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;

class AwbController extends \BaseController {

    public $controller_name = '';

    public $model;

    public $sql_connection;

    public $sql_table_name;

    public $ordermap = array(
        'ordertime'=>'',
        'buyerdeliveryzone'=>'',
        'buyerdeliverycity'=>'',
        'buyerdeliveryslot'=>1,
        'buyerdeliverytime'=>1,
        'box_count'=>1,
        'assigntime'=>'',
        'timeslot'=>1,
        'assignment_zone'=>'',
        'assignment_city'=>'',
        'assignment_seq'=>'',
        'delivery_id'=>'',
        'delivery_cost'=>'',
        'cod_cost'=>'',
        'width'=>'',
        'height'=>'',
        'length'=>'',
        'weight'=>'',
        'actual_weight'=>'',
        'delivery_type'=>'',
        'currency'=>'IDR',
        'total_price'=>'',
        //'fixed_discount'=>'',
        //'total_discount'=>'',
        //'total_tax'=>'',
        'chargeable_amount'=>'',
        'delivery_bearer'=>'',
        'cod_bearer'=>'',
        'cod_method'=>'',
        'ccod_method'=>'',
        'application_id'=>'',
        'application_key'=>'',
        'buyer_id'=>'',
        'merchant_id'=>'',
        'merchant_trans_id'=>'',
        'fulfillment_code'=>'',
        //'courier_id'=>'',
        //'device_id'=>'',
        'buyer_name'=>'',
        'email'=>'',
        'recipient_name'=>'',
        'shipping_address'=>'',
        'shipping_zip'=>'',
        'directions'=>'',
        //'dir_lat'=>'',
        //'dir_lon'=>'',
        'phone'=>'',
        'mobile1'=>'',
        'mobile2'=>'',
        'status'=>'pending',
        'laststatus'=>'pending',
        //'change_actor'=>'',
        //'actor_history'=>'',
        'delivery_note'=>'',
        //'reciever_name'=>'',
        //'reciever_picture'=>'',
        //'undersign'=>'',
        //'latitude'=>'',
        //'longitude'=>'',
        //'reschedule_ref'=>'',
        //'revoke_ref'=>'',
        //'reattemp'=>'',
        //'show_merchant'=>'',
        //'show_shop'=>'',
        'is_pickup'=>0,
        'is_import'=>1
    );


    public function  __construct()
    {
        date_default_timezone_set('Asia/Jakarta');

        //$this->model = "Member";
        $this->controller_name = strtolower( str_replace('Controller', '', get_class()) );

        $this->sql_table_name =  \Config::get('jayon.incoming_delivery_table') ;
        $this->sql_connection = 'mysql';

        $this->model = \DB::connection($this->sql_connection)->table($this->sql_table_name);

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $key = Input::get('key');
        $order_id = Input::get('orderid');
        $ff_id = Input::get('ffid');

        if( is_null($key) || $key == ''){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'empty key'));

            return \Response::json(array('status'=>'ERR:EMPTYKEY', 'timestamp'=>time(), 'message'=>'Empty Key' ));
        }


        $app = \Application::where('key','=',$key)->first();

        if($app){
            if($order_id != '' && $ff_id != ''){
                $order = \Shipment::where('merchant_trans_id','=',trim($order_id))
                            ->where('fulfillment_code','=',trim($ff_id))
                            ->where('application_key','=',trim($key))
                            ->first();
            }else if($order_id != '' && $ff_id == ''){
                $order = \Shipment::where('merchant_trans_id','=',trim($order_id))
                            //->where('fulfillment_code','=',trim($ff_id))
                            ->where('application_key','=',trim($key))
                            ->first();
            }else{
                $order = false;
            }

            if($order){
                //print_r($order);
                $actor = 'merchant id :'.$app->merchant_id;
                \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'awb '.$order->delivery_id));

                return \Response::json(array('status'=>'OK',
                    'awb'=>$order->delivery_id,
                        'timestamp'=>date('Y-m-d H:i:s',time()) ,
                        'pickup_time'=>$order->pickuptime,
                        'pending'=>$order->pending_count,
                        'order_status'=>$order->status,
                        'note'=>$order->delivery_note ));
            }else{
                $actor = 'merchant id :'.$app->merchant_id;
                \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'order not found'));

                return \Response::json(array('status'=>'ERR:NOTFOUND', 'timestamp'=>time(), 'message'=>'Record Not Found' ));

            }

        }else{

            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'account not found'));

            return \Response::json(array('status'=>'ERR:INVALIDACC', 'timestamp'=>time(), 'message'=>'Invalid Account' ));

        }


        //
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function old_tore()
    {

        $key = Input::get('key');

        $json = Input::all();

        //print_r($json);

        /*
                    ->join('members as m','d.merchant_id=m.id','left')
                    ->where('assignment_date',$indate)
                    ->where('device_id',$dev->id)
                    ->and_()
                    ->group_start()
                        ->where('status',$this->config->item('trans_status_admin_courierassigned'))
                        ->or_()
                        ->group_start()
                            ->where('status',$this->config->item('trans_status_new'))
                            ->where('pending_count >', 0)
                        ->group_end()
                    ->group_end()


        */

        if( is_null($key) || $key == ''){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'empty key'));

            return \Response::json(array('status'=>'ERR:EMPTYKEY', 'timestamp'=>time(), 'message'=>'Empty Key' ));
        }


        $app = \Application::where('key','=',$key)->first();

        if($app){

            $model = new \Shipment();

            $merchant_id = $app->merchant_id;

            $result = array();

            foreach( $json as $j){

                if(is_array($j)){

                    $model = $model->orWhere(function($q) use($j, $key){
                        $order_id = $j['order_id'];
                        $ff_id = $j['ff_id'];
                        $q->where('merchant_trans_id','=',trim($order_id))
                                    ->where('fulfillment_code','=',trim($ff_id))
                                    //->where('merchant_id','',$merchant_id)
                                    ->where('application_key','=',trim($key))
                                    ->whereNotNull('delivery_id');

                    });

                }

            }

            $awbs = $model->get();


            if($awbs){
                foreach($awbs as $awb){
                    $result[] = array('order_id'=>$awb->merchant_trans_id,
                        'ff_id'=>$awb->fulfillment_code,
                        'awb'=>$awb->delivery_id,
                        'timestamp'=>date('Y-m-d H:i:s',time()),
                        'pending'=>$awb->pending_count,
                        'status'=>$awb->status,'note'=>$awb->delivery_note
                        );
                }
            }

            //print_r($result);

            //die();
            $actor = $app->key.' : '.$app->merchant_id;

            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'awb search with array'));

            return Response::json($result);

        }else{

            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'account not found'));

            return \Response::json(array('status'=>'ERR:INVALIDACC', 'timestamp'=>time(), 'message'=>'Invalid Account' ));

        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {

        $key = Input::get('key');

        $json = Input::all();

        //print_r($json);

        /*
                    ->join('members as m','d.merchant_id=m.id','left')
                    ->where('assignment_date',$indate)
                    ->where('device_id',$dev->id)
                    ->and_()
                    ->group_start()
                        ->where('status',$this->config->item('trans_status_admin_courierassigned'))
                        ->or_()
                        ->group_start()
                            ->where('status',$this->config->item('trans_status_new'))
                            ->where('pending_count >', 0)
                        ->group_end()
                    ->group_end()


        */

        if( is_null($key) || $key == ''){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'empty key'));

            return \Response::json(array('status'=>'ERR:EMPTYKEY', 'timestamp'=>time(), 'message'=>'Empty Key' ));
        }


        $app = \Application::where('key','=',$key)->first();

        if($app){

            $jsons = Input::json();


            $model = new \Shipment();

            $merchant_id = $app->merchant_id;

            $app_id = $app->id;

            $app_key = $app->key;

            $result = array();

            /*
            foreach ($jsons as $json) {

                $order = $this->ordermap;

                print_r($json);

            }

            die();
            */

            foreach($jsons as $json){

                //print_r($json);

                $order = $this->ordermap;

                if(isset($json['pick_up_date'])){
                    if(is_array($json['pick_up_date']) && isset($json['pick_up_date']['sec']) ){
                        $pick_up_date = date('Y-m-d H:i:s', $json['pick_up_date']['sec']);
                    }else{
                        $pick_up_date = $json['pick_up_date'];
                    }
                }else{
                    $pick_up_date = date('Y-m-d H:i:s',time());
                }

                $codval = doubleval($json['cod']);

                //$codval = floor($codval * 100) / 100;

                $codval = round($codval, 0, PHP_ROUND_HALF_UP);


                $order['buyerdeliveryzone'] = (isset($json['district']))?$json['district']:'';
                $order['merchant_trans_id'] = $json['no_sales_order'];
                $order['buyerdeliverytime'] = $pick_up_date;
                $order['fulfillment_code'] = $json['consignee_olshop_orderid'];
                $order['box_count'] = $json['number_of_package'];
                $order['delivery_type'] = trim($json['delivery_type']);
                $order['total_price'] = $codval;
                $order['email'] = $json['email'];
                $order['buyer_name'] = $json['consignee_olshop_name'];
                $order['recipient_name'] = $json['consignee_olshop_name'];
                $order['shipping_address'] = $json['consignee_olshop_addr'];
                $order['buyerdeliverycity'] = $json['consignee_olshop_city'];
                $order['shipping_zip'] = $json['consignee_olshop_zip'];
                $order['phone'] = $json['consignee_olshop_phone'];

                $order['delivery_bearer'] = 'merchant';
                $order['cod_bearer'] = 'merchant';

                $order['actual_weight'] = strval($json['w_v']);

                $weight = $json['w_v'];
                $delivery_type = trim($json['delivery_type']);

                $order['weight'] = \Prefs::get_weight_tariff($weight, $delivery_type ,$app_id, $order['ordertime']);

                $order['merchant_id'] = $merchant_id;
                $order['application_id'] = $app_id;
                $order['application_key'] = $app_key;

                $trx_detail = array();
                $trx_detail[0]['unit_description'] = $json['consignee_olshop_desc'];
                $trx_detail[0]['unit_price'] = $json['cod'];
                $trx_detail[0]['unit_quantity'] = 1;
                $trx_detail[0]['unit_total'] = $json['cod'];
                $trx_detail[0]['unit_discount'] = 0;

                $order['trx_detail'] = $trx_detail;

                $trx_id = $order['merchant_trans_id'];

                $trx = json_encode($order);

                $order['merchant_trans_id'] = $json['no_sales_order'];
                $order['fulfillment_code'] = $json['consignee_olshop_orderid'];

                $inlog = $json;
                $inlog['ts'] = new \MongoDate();
                unset($inlog['_id']);
                $inlog['merchant_api_id'] = $merchant_id;

                \Orderapilog::insert($inlog);

                $check = \Shipment::where('merchant_trans_id','=',$json['no_sales_order'])
                                    ->where('fulfillment_code','=',$json['consignee_olshop_orderid'])
                                    ->first();

                if($check){

                    $result[] = array(
                        'order_id'=>$check->merchant_trans_id,
                        'ff_id'=>$check->fulfillment_code,
                        'awb'=>$check->delivery_id,
                        'timestamp'=>$check->created,
                        'pickup_time'=>$check->pickuptime,
                        'delivery_time'=>$check->deliverytime,
                        'pending'=>$check->pending_count,
                        'status'=>$check->status,
                        'note'=>$check->delivery_note
                    );

                }else{
                    $saved = $this->order_save($trx,$app_key,$trx_id);

                    $result[] = array(
                        'order_id'=>$saved['merchant_trans_id'],
                        'ff_id'=>$saved['fulfillment_code'],
                        'awb'=>$saved['delivery_id'],
                        'timestamp'=>$saved['created'],
                        'pickup_time'=>'0000-00-00 00:00:00',
                        'delivery_time'=>'0000-00-00 00:00:00',
                        'pending'=>$saved['pending_count'],
                        'status'=>$saved['status'],
                        'note'=>$saved['delivery_note']
                    );

                }



                //$order[] = [w_v] => 0.9
                //$order[] = [awb] =>
                //$order[] = [consignee_olshop_service] => REG
                //$order[] = [position] => BPDU
                //$order[] = [updated_at] => 2015-11-24 16:44:25
                //$order[] = [created_at] => 2015-11-24 16:44:25
                //$order[] = [createdDate] => stdClass Object
                //$order[] = [lastUpdate] => stdClass Object
                //$order[] = [consignee_olshop_province] =>
                //$order[] = [trip] => 1
                //$order[] = [bucket] => incoming
                //$order[] = [order_id] => 100282527
                //$order[] = [fulfillment_code] => 249977
                //$order[] = [status] => confirmed
                //$order[] = [logistic_status] =>
                //$order[] = [pending_count] => 0
                //$order[] = [courier_status] => at_initial_node
                //$order[] = [warehouse_status] => at_initial_node
                //$order[] = [pickup_status] => to_be_picked_up
                //$order[] = [device_key] =>
                //$order[] = [device_name] =>
                //$order[] = [device_id] =>
                //$order[] = [courier_name] =>
                //$order[] = [courier_id] =>

            }
                /*
                buyerdeliverytime
                delivery_type
                buyer_name
                recipient_name
                shipping_address
                direction
                email
                mobile1
                mobile2
                phone
                weight
                package_description
                merchant_trans_id
                fulfillment_code
                logistic_awb
                total_price
                cod_bearer
                delivery_bearer
                buyerdeliveryzone
                buyerdeliverycity
                width
                height
                length
                box_count
                */

        //$result = $json;

            /*
        (
            [_id] => 56543184ccae5b6112004278
            [district] =>
            [no_sales_order] => 100282527
            [pick_up_date] => stdClass Object
                (
                    [sec] => 1431104400
                    [usec] => 0
                )

            [consignee_olshop_orderid] => 249977
            [number_of_package] => 1
            [delivery_type] => REG
            [cod] => 0
            [email] => bagus_sulaiman@india.com
            [consignee_olshop_name] => 106191 bagus sulaiman
            [consignee_olshop_addr] => bagus sulaiman
                jl.bakti rt.004 rw.008 no.10b cililitan kramatjati 13640
                Jakarta Timur JK 13640
                Indonesia
            [consignee_olshop_city] => Jakarta Timur
            [consignee_olshop_region] => JK
            [consignee_olshop_zip] => 13640
            [consignee_olshop_phone] => 81317857612
            [contact] => 106191 bagus sulaiman
            [consignee_olshop_desc] => Susu dan Perlengkapan Bayi
            [w_v] => 0.9
            [awb] =>
            [consignee_olshop_cust] => 7735
            [consignee_olshop_service] => REG
            [position] => BPDU
            [updated_at] => 2015-11-24 16:44:25
            [created_at] => 2015-11-24 16:44:25
            [createdDate] => stdClass Object
                (
                    [sec] => 1448358276
                    [usec] => 411000
                )

            [lastUpdate] => stdClass Object
                (
                    [sec] => 1448358276
                    [usec] => 411000
                )

            [logistic] => JEX
            [logistic_type] => external
            [consignee_olshop_province] =>
            [trip] => 1
            [bucket] => incoming
            [delivery_id] => 24-112015-YAMYZ
            [order_id] => 100282527
            [fulfillment_code] => 249977
            [status] => confirmed
            [logistic_status] =>
            [pending_count] => 0
            [courier_status] => at_initial_node
            [warehouse_status] => at_initial_node
            [pickup_status] => to_be_picked_up
            [device_key] =>
            [device_name] =>
            [device_id] =>
            [courier_name] =>
            [courier_id] =>
        )

            $awbs = $model->get();


            if($awbs){
                foreach($awbs as $awb){
                    $result[] = array(
                        'order_id'=>$awb->merchant_trans_id,
                        'ff_id'=>$awb->fulfillment_code,
                        'awb'=>$awb->delivery_id,
                        'timestamp'=>date('Y-m-d H:i:s',time()),
                        'pending'=>$awb->pending_count,
                        'status'=>$awb->status,
                        'note'=>$awb->delivery_note
                    );
                }
            }
            */
            //print_r($result);

            //die();
            /*
            foreach($jsons as $sheet_id=>$rows){

                $app_key = $app_entry[$sheet_id];
                $app_id = get_app_id_from_key($app_key);

                $order = $this->ordermap;
                foreach ($rows['data'] as $key => $line) {
                    if(in_array($key, $entry)){

                        $line['delivery_type'] = ($line['delivery_type'] == 'DO')?'Delivery Only':$line['delivery_type'];
                        $line['actual_weight'] = $line['weight'];
                        $line['weight'] = get_weight_tariff($line['weight'], $line['delivery_type'] ,$app_id);

                        $trx_detail = array();
                        $trx_detail[0]['unit_description'] = $line['package_description'];
                        $trx_detail[0]['unit_price'] = $line['total_price'];
                        $trx_detail[0]['unit_quantity'] = 1;
                        $trx_detail[0]['unit_total'] = $line['total_price'] ;
                        $trx_detail[0]['unit_discount'] = 0;

                        unset($line['package_description']);
                        unset($line['no']);

                        foreach($line as $k=>$v){
                            $order[$k] = $v;
                        }

                        $order['zip'] = '-';

                        $order['merchant_id'] = $merchant_id;
                        $order['application_id'] = $app_id;
                        $order['application_key'] = $app_key;
                        $order['trx_detail'] = $trx_detail;

                        $trx_id = 'TRX_'.$merchant_id.'_'.str_replace(array(' ','.'), '', microtime());

                        //print "order input: \r\n";
                        //print_r($order);

                        $trx = json_encode($order);
                        $result = $this->order_save($trx,$app_key,$trx_id);

                        //print $result;

                    }

                }

            }
            */


            $actor = $app->key.' : '.$app->merchant_id;

            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'order create array'));

            return Response::json($result);

        }else{

            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'account not found'));

            return \Response::json(array('status'=>'ERR:INVALIDACC', 'timestamp'=>time(), 'message'=>'Invalid Account' ));

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $in = Input::get();
        if(isset($in['key']) && $in['key'] != ''){
            print $in['key'];
        }else{
            print 'no key';
        }
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


    public function postWv()
    {
        $json = \Input::json();

        try{
            $json = $json->all();

            $res = array();

            foreach($json as $j){
                $ord = \Shipment::where('delivery_id','=',$j['awb'])->first();
                if($ord){
                    if($ord->actual_weight == '' || is_null($ord->actual_weight)){
                        $ord->actual_weight = $j['w_v'];
                        $ord->save();

                        $res[] = array('awb'=>$j['awb'],'status'=>'updated');
                    }else{
                        $res[] = array('awb'=>$j['awb'],'status'=>'not updated');
                    }
                }else{
                    $res[] = array('awb'=>$j['awb'],'status'=>'not found');
                }
            }

            return \Response::json($res);

        }catch(Exception $e){
            //print $e->getMessage();
            return \Response::json(array('err'=>'json error'));

        }


    }

    public function underscoreToCamelCase( $string, $first_char_caps = false)
    {

        $strings = explode('_', $string);

        if(count($strings) > 1){
            for($i = 0; $i < count($strings);$i++){
                if($i == 0){
                    if($first_char_caps == true){
                        $strings[$i] = ucwords($strings[$i]);
                    }
                }else{
                    $strings[$i] = ucwords($strings[$i]);
                }
            }

            return implode('', $strings);
        }else{
            return $string;
        }

    }

    public function boxList($field,$val){

        $boxes = \Box::where($field,'=',$val)->get();

        $bx = array();

        foreach($boxes as $b){
            $bx[] = $b->box_id;
        }

        if(count($bx) > 0){
            return implode(',',$bx);
        }else{
            return '1';
        }

    }

    public function order_save($indata,$api_key,$transaction_id)
    {

        date_default_timezone_set('Asia/Jakarta');

        $args = '';

        //$api_key = $this->get('key');
        //$transaction_id = $this->get('trx');

        if(is_null($api_key)){
            $result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
            return $result;
        }else{
            $app = \Prefs::get_key_info(trim($api_key));

            if($app == false){
                $result = json_encode(array('status'=>'ERR:INVALIDKEY','timestamp'=>now()));
                return $result;
            }else{
                //$in = $this->input->post('transaction_detail');
                //$in = file_get_contents('php://input');
                $in = $indata;

                //print $in;

                $buyer_id = 1;

                $args = 'p='.$in;

                $in = json_decode($in);

                //print "order input to save: \r\n";
                //print_r($in);

                $is_new = false;

                $in->phone = ( isset( $in->phone ) && $in->phone != '')?\Prefs::normalphone( $in->phone ):'';
                $in->mobile1 = ( isset( $in->mobile1 ) && $in->mobile1 != '' )?\Prefs::normalphone( $in->mobile1 ):'';
                $in->mobile2 = ( isset( $in->mobile2 ) && $in->mobile2 != '' )?\Prefs::normalphone( $in->mobile2 ):'';


                if(isset($in->buyer_id) && $in->buyer_id != '' && $in->buyer_id > 1){

                    $buyer_id = $in->buyer_id;
                    $is_new = false;

                }else{

                    if($in->email == '' || $in->email == '-' || !isset($in->email) || $in->email == 'noemail'){

                        $in->email = 'noemail';
                        $is_new = true;
                        if( trim($in->phone.$in->mobile1.$in->mobile2) != ''){
                            if($buyer = \Prefs::check_phone($in->phone,$in->mobile1,$in->mobile2)){
                                $buyer_id = $buyer['id'];
                                $is_new = false;
                            }
                        }

                    }else if($buyer = \Prefs::check_email($in->email)){

                        $buyer_id = $buyer['id'];
                        $is_new = false;

                    }else if($buyer = \Prefs::check_phone($in->phone,$in->mobile1,$in->mobile2)){

                        $buyer_id = $buyer['id'];
                        $is_new = false;

                    }

                }

                if(isset($in->merchant_trans_id) && $in->merchant_trans_id != ""){
                    $transaction_id = $in->merchant_trans_id;
                }


                if($is_new){

                    $random_string = str_random(5);

                    $buyer_username = substr(strtolower(str_replace(' ','',$in->buyer_name)),0,6).$random_string;
                    $dataset['username'] = $buyer_username;
                    $dataset['email'] = $in->email;
                    $dataset['phone'] = $in->phone;
                    $dataset['mobile1'] = $in->mobile1;
                    $dataset['mobile2'] = $in->mobile2;
                    $dataset['fullname'] = $in->buyer_name;
                    $password = str_random(8);
                    $dataset['password'] = $password;
                    $dataset['created'] = date('Y-m-d H:i:s',time());

                    /*
                    $dataset['province'] =
                    $dataset['mobile']
                    */

                    $dataset['street'] = $in->shipping_address;
                    $dataset['district'] = $in->buyerdeliveryzone;
                    $dataset['city'] = $in->buyerdeliverycity;
                    $dataset['country'] = 'Indonesia';
                    $dataset['zip'] = (isset($in->zip))?$in->zip:'';

                    //$buyer_id = $this->register_buyer($dataset);
                    $is_new = true;
                }

                $order['created'] = date('Y-m-d H:i:s',time());
                $order['ordertime'] = date('Y-m-d H:i:s',time());
                $order['application_id'] = $app->id;
                $order['application_key'] = $app->key;
                $order['buyer_id'] = $buyer_id;
                $order['merchant_id'] = $app->merchant_id;
                $order['merchant_trans_id'] = trim($transaction_id);

                $order['buyer_name'] = $in->buyer_name;
                $order['recipient_name'] = $in->recipient_name;
                $order['email'] = $in->email;
                $order['directions'] = $in->directions;
                //$order['dir_lat'] = $in->dir_lat;
                //$order['dir_lon'] = $in->dir_lon;
                $order['buyerdeliverytime'] = $in->buyerdeliverytime;
                $order['buyerdeliveryslot'] = $in->buyerdeliveryslot;
                $order['buyerdeliveryzone'] = $in->buyerdeliveryzone;
                $order['buyerdeliverycity'] = (is_null($in->buyerdeliverycity) || $in->buyerdeliverycity == '')?'Jakarta':$in->buyerdeliverycity;

                $order['currency'] = $in->currency;
                $order['total_price'] = (isset($in->total_price))?$in->total_price:0;
                $order['total_discount'] = (isset($in->total_discount))?$in->total_discount:0;
                $order['total_tax'] = (isset($in->total_tax))?$in->total_tax:0;

                if(in_array( strtoupper(trim($in->delivery_type)) , array('COD','CCOD','PS' ) )){
                    $in->delivery_type = 'COD';
                }

                if(in_array( strtoupper(trim($in->delivery_type)) , array('REG','DO', 'DELIVERY ONLY' ) )){
                    $in->delivery_type = 'Delivery Only';
                }

                $order['delivery_type'] = $in->delivery_type;

                if($in->delivery_type == 'DO' || $in->delivery_type == 'Delivery Only'){
                    $order['cod_cost'] = 0;
                }else{
                    $order['cod_cost'] = \Prefs::get_cod_tariff($order['total_price'],$app->id,$order['ordertime']);
                }

                $order['box_count'] = (isset($in->box_count))?$in->box_count:1;
                $order['pending_count'] = (isset($in->pending_count))?$in->pending_count:0;
                $order['delivery_note'] = (isset($in->delivery_note))?$in->delivery_note:'';

                $order['shipping_address'] = $in->shipping_address;
                $order['shipping_zip'] = $in->shipping_zip;
                $order['phone'] = $in->phone;
                $order['mobile1'] = $in->mobile1;
                $order['mobile2'] = $in->mobile2;
                $order['status'] = $in->status;

                $order['width'] = $in->width;
                $order['height'] = $in->height;
                $order['length'] = $in->length;
                $order['weight'] = (isset($in->weight))?$in->weight:0;
                $order['actual_weight'] = (isset($in->actual_weight))?$in->actual_weight:0;


                $order['delivery_cost'] = $order['weight'];

                $order['cod_bearer'] = (isset($in->cod_bearer))?$in->cod_bearer:'merchant';
                $order['delivery_bearer'] = (isset($in->delivery_bearer))?$in->delivery_bearer:'merchant';

                $order['cod_method'] = (isset($in->cod_method))?$in->cod_method:'cash';
                $order['ccod_method'] = (isset($in->ccod_method))?$in->ccod_method:'full';

                $order['fulfillment_code'] = (isset($in->fulfillment_code))?$in->fulfillment_code:'';

                // check out who is bearing the cost
                if($order['delivery_type'] == 'COD' || $order['delivery_type'] == 'CCOD'){
                    if($order['delivery_bearer'] == 'merchant'){
                        $dcost = 0;
                    }else{
                        $dcost = $order['delivery_cost'];
                    }

                    if($order['cod_bearer'] == 'merchant'){
                        $codcost = 0;
                    }else{
                        $codcost = $order['cod_cost'];
                    }

                    $order['chargeable_amount'] = $order['total_price'] + $dcost + $codcost;
                }else{

                    if($order['delivery_bearer'] == 'merchant'){
                        $dcost = 0;
                    }else{
                        $dcost = $order['delivery_cost'];
                    }

                    $order['chargeable_amount'] = $dcost;
                }

                if(isset($in->show_shop)){
                    $order['show_shop'] = $in->show_shop;
                }

                if(isset($in->show_merchant)){
                    $order['show_merchant'] = $in->show_merchant;
                }

                $order['is_api'] = 1;

                $ship = new \Shipment();

                foreach ($order as $k=>$v){
                    $ship->{$k} = $v;
                }

                $ship->save();

                $sequence = $ship->id;

                if(isset($in->delivery_id) ){
                    if(is_null($in->delivery_id) || $in->delivery_id == ''){
                        $delivery_id = \Prefs::get_delivery_id($sequence,$app->merchant_id);
                    }else{
                        $delivery_id = \Prefs::get_delivery_id($sequence,$app->merchant_id, $in->delivery_id);
                    }
                }else{
                    $delivery_id = \Prefs::get_delivery_id($sequence,$app->merchant_id);
                }

                $ship->delivery_id = $delivery_id;

                //print_r($ship);

                $ship->save();

                //die();


                if(isset($in->box_count)){
                    $box_count = $in->box_count;
                }else{
                    $box_count = 1;
                }

                \Prefs::save_box($delivery_id, trim($transaction_id), $order['fulfillment_code'],$box_count);

                $nedata['fullname'] = $in->buyer_name;
                $nedata['merchant_trx_id'] = trim($transaction_id);
                $nedata['delivery_id'] = $delivery_id;
                $nedata['merchantname'] = $app->application_name;
                $nedata['app'] = $app;

                $order['delivery_id'] = $delivery_id;

                $buyer_id = \Prefs::save_buyer($order);

                /*
                $this->db->where('id',$sequence)->update($this->config->item('incoming_delivery_table'),array('delivery_id'=>$delivery_id));
                */

                /*
                    $this->table_tpl = array(
                        'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">'
                    );
                    $this->table->set_template($this->table_tpl);


                    $this->table->set_heading(
                        'No.',
                        'Description',
                        'Quantity',
                        'Total'
                        ); // Setting headings for the table
                */
                    $d = 0;
                    $gt = 0;


                if($in->trx_detail){
                    $seq = 0;

                    foreach($in->trx_detail as $it){
                        $item = new \Deliverydetail();

                        $item->ordertime = $order['ordertime'];
                        $item->delivery_id = $delivery_id;
                        $item->unit_sequence = $seq++;
                        $item->unit_description = $it->unit_description;
                        $item->unit_price = $it->unit_price;
                        $item->unit_quantity = $it->unit_quantity;
                        $item->unit_total = $it->unit_total;
                        $item->unit_discount = $it->unit_discount;

                        $item->save();

                        /*
                        $this->table->add_row(
                            (int)$item['unit_sequence'] + 1,
                            $item['unit_description'],
                            $item['unit_quantity'],
                            $item['unit_total']
                        );

                        $u_total = str_replace(array(',','.'), '', $item['unit_total']);
                        $u_discount = str_replace(array(',','.'), '', $item['unit_discount']);
                        $gt += (int)$u_total;
                        $d += (int)$u_discount;
                        */
                    }

                    $total = (isset($in->total_price) && $in->total_price > 0)?$in->total_price:0;
                    $total = str_replace(array(',','.'), '', $total);
                    $total = (int)$total;
                    $gt = ($total < $gt)?$gt:$total;

                    $disc = (isset($in->total_discount))?$in->total_discount:0;
                    $tax = (isset($in->total_tax))?$in->total_tax:0;
                    $cod = (isset($in->cod_cost))?$in->cod_cost:'Paid by merchant';

                    $disc = str_replace(array(',','.'), '', $disc);
                    $tax = str_replace(array(',','.'), '',$tax);
                    $cod = str_replace(array(',','.'), '',$cod);

                    $disc = (int)$disc;
                    $tax = (int)$tax;
                    $cod = (int)$cod;

                    $chg = ($gt - $disc) + $tax + $cod;

                    /*
                    $this->table->add_row(
                        '',
                        '',
                        'Total Price',
                        number_format($gt,2,',','.')
                    );

                    $this->table->add_row(
                        '',
                        '',
                        'Total Discount',
                        number_format($disc,2,',','.')
                    );

                    $this->table->add_row(
                        '',
                        '',
                        'Total Tax',
                        number_format($tax,2,',','.')
                    );


                    if($cod == 0){
                        $this->table->add_row(
                            '',
                            '',
                            'COD Charges',
                            'Paid by Merchant'
                        );
                    }else{
                        $this->table->add_row(
                            '',
                            '',
                            'COD Charges',
                            number_format($cod,2,',','.')
                        );
                    }


                    $this->table->add_row(
                        '',
                        '',
                        'Total Charges',
                        number_format($chg,2,',','.')
                    );

                    $nedata['detail'] = $this->table;


                    $result = json_encode(array('status'=>'OK:ORDERPOSTED','timestamp'=>now(),'delivery_id'=>$delivery_id,'buyer_id'=>$buyer_id));
                    */

                    //return $ship->toArray();
                }else{
                    //$nedata['detail'] = false;

                    //$result = json_encode(array('status'=>'OK:ORDERPOSTEDNODETAIL','timestamp'=>now(),'delivery_id'=>$delivery_id));

                    //return $order;
                }

                return $ship->toArray();

                //print_r($app);
                /*

                if($app->notify_on_new_order == 1){
                    send_notification('New Delivery Order - Jayon Express COD Service',$in->email,$app->cc_to,$app->reply_to,'order_submit',$nedata,null);
                }

                if($is_new == true){
                    $edata['fullname'] = $dataset['fullname'];
                    $edata['username'] = $buyer_username;
                    $edata['password'] = $password;
                    if($app->notify_on_new_member == 1 && $in->email != 'noemail'){
                        send_notification('New Member Registration - Jayon Express COD Service',$in->email,null,null,'new_member',$edata,null);
                    }

                }*/

            }
        }

        //$this->log_access($api_key, __METHOD__ ,$result,$args);
    }


}
