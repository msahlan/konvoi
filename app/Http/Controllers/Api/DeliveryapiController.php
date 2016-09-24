<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

//use Illuminate\Support\Facades\Hash;
//use Illuminate\Database\Eloquent\ModelNotFoundException;
//use Illuminate\Support\Facades\Response;

use App\Models\Device;
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

class DeliveryapiController extends BaseController {

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

        $this->model = DB::connection($this->sql_connection)->table($this->sql_table_name);

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

        $dev = Device::where('key','=',$key)->first();

        //print_r($dev);

        $txtab = config('jayon.incoming_delivery_table');

        /*
        $orders = $this->model
                    ->select(
                            DB::raw(
                                config('jayon.incoming_delivery_table').'.* ,'.
                                config('jayon.jayon_members_table').'.merchantname as merchant_name ,'.
                                config('jayon.applications_table').'.application_name as app_name ,'.
                                '('.$txtab.'.width * '.$txtab.'.height * '.$txtab.'.length ) as volume'
                            )
                    )
                    ->leftJoin(config('jayon.jayon_members_table'), config('jayon.incoming_delivery_table').'.merchant_id', '=', config('jayon.jayon_members_table').'.id' )
                    ->leftJoin(config('jayon.applications_table'), config('jayon.incoming_delivery_table').'.application_id', '=', config('jayon.applications_table').'.id' )

                    ->where('device_id','=',$dev->id)
                    ->where('assignment_date','=',$deliverydate)

                    ->where(function($q){
                        $q->where('status','=', config('jayon.trans_status_new') )
                            ->orWhere(function($ql){
                                $ql->where('status','=', config('jayon.trans_status_new') )
                                    ->where('pending_count','>',0);
                            });
                    })
                    ->orderBy('ordertime','desc')
                    ->get();
        */

        $orders = $this->model
                ->select(
                    DB::raw(
                        config('jayon.incoming_delivery_table').'.* ,'.
                        config('jayon.jayon_couriers_table').'.fullname as courier ,'.
                        config('jayon.jayon_devices_table').'.identifier as device ,'.
                        config('jayon.jayon_members_table').'.merchantname as merchant_name ,'.
                        config('jayon.applications_table').'.application_name as app_name ,'.
                        '('.$txtab.'.width * '.$txtab.'.height * '.$txtab.'.length ) as volume'
                )
            )
            ->leftJoin(config('jayon.jayon_couriers_table'), config('jayon.incoming_delivery_table').'.courier_id', '=', config('jayon.jayon_couriers_table').'.id' )
            ->leftJoin(config('jayon.jayon_devices_table'), config('jayon.incoming_delivery_table').'.device_id', '=', config('jayon.jayon_devices_table').'.id' )
            ->leftJoin(config('jayon.jayon_members_table'), config('jayon.incoming_delivery_table').'.merchant_id', '=', config('jayon.jayon_members_table').'.id' )
            ->leftJoin(config('jayon.applications_table'), config('jayon.incoming_delivery_table').'.application_id', '=', config('jayon.applications_table').'.id' )

            ->where(function($q) use($dev, $deliverydate){
                    $q->where('device_id','=',$dev->id)
                    ->where('assignment_date','=',$deliverydate);

            })
            ->where(function($query){
                $query->where('status','=', config('jayon.trans_status_admin_courierassigned') )
                    ->orWhere('status','=', config('jayon.trans_status_mobile_pickedup') )
                    ->orWhere('status','=', config('jayon.trans_status_mobile_enroute') )
                    ->orWhere(function($q){
                            $q->where('status', config('jayon.trans_status_new'))
                                ->where(config('jayon.incoming_delivery_table').'.pending_count', '>', 0);
                    });

            })

            ->orderBy('ordertime','desc')
            ->get();

        $total_billing = 0;
        $total_delivery = 0;
        $total_cod = 0;

        for($n = 0; $n < count($orders);$n++){
            $or = new \stdClass();
            foreach( $orders[$n] as $k=>$v ){
                $nk = $this->underscoreToCamelCase($k);
                $or->$nk = (is_null($v))?'':$v;
            }

            $or->extId = $or->id;
            unset($or->id);

            $bc = \Box::where('delivery_id','=',$or->deliveryId)->count();

            if($bc == 0){
                $this->createBox($or->deliveryId,$or->merchantTransId, $or->fulfillmentCode, $or->boxCount );
            }

            $or->boxList = $this->boxList('delivery_id',$or->deliveryId,$key,$or->merchantId);
            //$or->boxList = $this->boxList('delivery_id',$or->deliveryId);
            $or->boxObjects = $this->boxList('delivery_id',$or->deliveryId, $key, $or->merchantId , true);
            $or->merchantObject = $this->merchantObject($or->merchantId);

            /* chargeable */

            $total = doubleval( $or->totalPrice );
            $dsc = doubleval( $or->totalDiscount );
            $tax = doubleval( $or->totalTax );
            $dc = doubleval( $or->deliveryCost );
            $cod = doubleval( $or->codCost );

            $total = (is_nan($total))?0:$total;
            $dsc = (is_nan($dsc))?0:$dsc;
            $tax = (is_nan($tax))?0:$tax;
            $dc = (is_nan($dc))?0:$dc;
            $cod = (is_nan($cod))?0:$cod;

            //print $total.' '.$dsc.' '.$tax.' '.$dc.' '.$cod."\r\n";

            $payable = 0;

            $details = \Deliverydetail::where('delivery_id','=',$or->deliveryId)->orderBy('unit_sequence','asc')->get();

            $details = $details->toArray();


            $d = 0;
            $gt = 0;

            foreach($details as $value => $key)
            {

                $u_total = doubleval($key['unit_total']);
                $u_discount = doubleval($key['unit_discount']);
                $gt += (is_nan($u_total))?0:$u_total;
                $d += (is_nan($u_discount))?0:$u_discount;

            }


            if($gt == 0 ){
                if($total > 0 && $payable)
                $gt = $total;
            }

            //print $gt.' '.$dsc.' '.$tax.' '.$dc.' '.$cod."\r\n";

            $payable = $gt;

            $db = '';
            if($or->deliveryBearer == 'merchant'){
                $dc = 0;
            }

            //force all DO to zero

            $cb = '';
            if($or->codBearer == 'merchant'){
                $cod = 0;
            }

            $codclass = '';


            if($or->deliveryType == 'COD' || $or->deliveryType == 'CCOD'){
                $chg = ($gt - $dsc) + $tax + $dc + $cod;
            }else{
                $dc = 0;
                $cod = 0;
                $chg = $dc;
            }

            $or->totalPrice = strval($payable);
            $or->deliveryCost = strval($dc);
            $or->codCost = strval($cod);
            $or->chargeableAmount = strval($chg);

            $orders[$n] = $or;
        }


        $actor = $key;
        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'logged out'));

        return $orders;
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
        $merchant = \Merchant::where('id','=',$merchant_id)->first();
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

        $boxes = \Box::where($field,'=',$val)
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
        $last = \Boxstatus::where('deliveryId','=',$delivery_id)
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
