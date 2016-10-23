<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Device;
use App\Models\Scanlog;
use App\Models\Geolog;
use App\Models\Boxstatus;
use App\Models\Boxid;
use App\Models\Box;
use App\Models\Merchant;
use App\Models\Pickup;

use App\Models\Deliverydetail;

use App\Helpers\Prefs;


use Config;

use Auth;
use Event;
use View;
use Input;
use Request;
use Response;
use Mongomodel;
use \MongoRegex;
use \MongoDate;
use \MongoId;
use \MongoInt32;
use DB;
use HTML;
use Excel;
use Validator;

class PaymentpickupapiController extends BaseController {

    public $controller_name = '';

    public $model;

    public $sql_connection;

    public $sql_table_name;

    public $order_unset = array(

            'assignmentDate',
            'assignmentTimeslot',
            'assignmentZone',
            'assignmentCity',
            'assignmentSeq',
            'paymentProvider',
            'toscan',
            'directions',
            'phone',
            'mobile1',
            'mobile2',
            'picAddress',
            'pic1',
            'pic2',
            'pic3',
            'undersign',
            'rescheduleRef',
            'revokeRef',
            'sameEmail',
            'samePhone',
            'showMerchant',
            'showShop',
            'isPickup',
            'isImport',
            'isApi',
            'courier',
            'device',
            'merchantName',
            'appName',
            'volume',

        );

    public $merchant_unset = array(
            'username',
            'email',
            'password',
            'fullname',
            'created',
            'updated',
            'district',
            'province',
            'country',
            'bank',
            'account_number',
            'account_name',
            'same_as_personal_address',
            'group_id',
            'token',
            'identifier',
            'merchant_request',
            'success',
            'fail',
            'mc_email',
            'mc_street',
            'mc_district',
            'mc_city',
            'mc_province',
            'mc_country',
            'mc_zip',
            'mc_phone',
            'mc_mobile',
            'mc_first_order',
            'mc_last_order',
            'mc_unlimited_time',
            'mc_toscan',
            'mc_pickup_time',
            'mc_pickup_cutoff',
            'mc_delivery_bearer',
            'mc_cod_bearer'
        );


    public function  __construct()
    {
        //$this->model = "Member";
        $this->controller_name = strtolower( str_replace('Controller', '', get_class()) );

        $this->sql_table_name =  config('jayon.incoming_delivery_table') ;
        $this->sql_connection = 'mysql';

        //$this->model = DB::connection($this->sql_connection)->table($this->sql_table_name);

        $this->model = new Pickup();

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $key = Request::input('key');
        $deliverydate = Request::input('date');

        $dev = Device::where('key','=',$key)->first();

        if(!$dev){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'no id' ));
        }

        $orders = $this->model

            ->where(function($q) use($dev, $deliverydate){
                    $q->where('deviceKey','=',$dev->key)
                    ->where('assignmentDate','=',$deliverydate.' 00:00:00');

            })
            ->where('status','=', config('jayon.trans_status_admin_courierassigned') )
            /*
            ->where(function($query){
                $query->where('status','=', config('jayon.trans_status_admin_courierassigned') )
                    ->orWhere('status','=', config('jayon.trans_status_mobile_pickedup') )
                    ->orWhere('status','=', config('jayon.trans_status_mobile_enroute') )
                    ->orWhere(function($q){
                            $q->where('status', config('jayon.trans_status_new'))
                                ->where(config('jayon.incoming_delivery_table').'.pending_count', '>', 0);
                    });

            })
            */

            ->orderBy('assignmentDateTs','desc')
            ->get();

        //print_r($orders->toArray());

        $out = [];

        $orders  = $orders->toArray();

        for($n = 0; $n < count($orders);$n++){
            $or = new \stdClass();
            foreach( $orders[$n] as $k=>$v ){
                $nk = $this->underscoreToCamelCase($k);
                $or->$nk = (is_null($v))?'':$v;
            }

            $or->extId = $or->Id;
            unset($or->Id);
            unset($or->assignmentDateTs);

            //$or->merchantObject = $this->merchantObject($or->creditor);

            $out[$n] = $or;
        }


        $actor = $key;
        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'logged out'));

        return $out;
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
    public function store()
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $in = Request::input();
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

    public function merchantObject($merchant_id)
    {
        $merchant = Creditor::find($merchant_id);
        if($merchant){

            $merchant = $merchant->toArray();

            $nm = array();
            foreach ($merchant as $key => $value) {
                if(in_array($key, $this->merchant_unset)){

                }else{
                    $nk = $this->underscoreToCamelCase($key);
                    $nm[$nk] = (is_null($value))?'':$value;
                }
            }

            $nm['extId'] = $nm['id'];
            unset($nm['id']);

            return $nm;
        }else{
            return array();
        }
    }

    public function createBox($delivery_id, $order_id, $fulfillment_code, $boxcount )
    {
        $boxcount = intval($boxcount);

        for ($i=0; $i < $boxcount ; $i++) {
            $box = new \Box();
            $box->delivery_id = $delivery_id;
            $box->merchant_trans_id = $order_id;
            $box->fulfillment_code = $fulfillment_code;
            $box->box_id = strval($i + 1);
            $box->save();
        }
    }


    public function boxList($field,$val, $device_key , $merchant_id ,$obj = false){

        $boxes = Box::where($field,'=',$val)
                        //->where('deliveryStatus','!=','delivered')
                        //->where('deliveryStatus','!=','returned')
                        ->get();

        $bx = array();

        if($obj == true){

            $boxes = $boxes->toArray();

            for($n = 0; $n < count($boxes);$n++){


                $ob = new \stdClass();

                foreach( $boxes[$n] as $k=>$v ){
                    if($k != '_id' && $k != 'id'){
                        $nk = $this->underscoreToCamelCase($k);
                    }else{
                        $nk = $k;
                    }

                    $ob->$nk = (is_null($v))?'':$v;
                }

                //print_r($ob);
                $ob->extId = $ob->id;
                $ob->merchantId = $merchant_id;
                unset($ob->id);

                $ob->status = $this->lastBoxStatus($device_key, $ob->deliveryId, $ob->fulfillmentCode ,$ob->boxId);

                $boxes[$n] = $ob;
            }

            return $boxes;

        }else{
            foreach($boxes as $b){
                $bx[] = $b->box_id;
            }

            if(count($bx) > 0){
                return implode(',',$bx);
            }else{
                return '1';
            }
        }

    }

    public function lastBoxStatus($device_key, $delivery_id, $fulfillment_code ,$box_id){
        $last = Boxstatus::where('deliveryId','=',$delivery_id)
                                ->where('deviceKey','=',$device_key)
                                ->where('appname','=',config('jex.pickup_app'))
                                //->where('fulfillmentCode'.'=',$fulfillment_code)
                                ->where('boxId','=',strval($box_id))
                                ->orderBy('mtimestamp', 'desc')
                                ->first();
        //print_r($last);

        if($last){
            return $last->status;
        }else{
            return 'out';
        }
    }

    /*
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
    */
}
