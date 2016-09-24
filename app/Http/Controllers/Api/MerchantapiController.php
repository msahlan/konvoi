<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;

class MerchantapiController extends \BaseController {

    public $controller_name = '';

    public $model;

    public $sql_connection;

    public $sql_table_name;

    public function  __construct()
    {
        //$this->model = "Member";
        $this->controller_name = strtolower( str_replace('Controller', '', get_class()) );

        $this->sql_table_name =  \Config::get('jayon.jayon_members_table') ;
        $this->sql_connection = 'mysql';

        $this->model = \DB::connection($this->sql_connection)->table($this->sql_table_name);

        //$this->model = new \Merchant();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $key = Input::get('key');
        $deliverydate = Input::get('date');

        $dev = \Device::where('key','=',$key)->first();

        $model = $this->model;

        $merchants = $model->where('group_id','=',4)->get();

        //print_r($merchants);

        //die();

        for($n = 0; $n < count($merchants);$n++){
            $or = new \stdClass();
            foreach( $merchants[$n] as $k=>$v ){
                $nk = $this->underscoreToCamelCase($k);
                $or->$nk = (is_null($v))?'':$v;
            }

            $or->extId = $or->id;
            unset($or->id);

            //$or->boxList = $this->boxList('delivery_id',$or->deliveryId,$key);
            //$or->boxObjects = $this->boxList('delivery_id',$or->deliveryId, $key , true);

            $merchants[$n] = $or;
        }


        $actor = $key;
        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'logged out'));

        return $merchants;
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

    public function boxList($field,$val, $device_key ,$obj = false){

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
                unset($ob->_id);

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
                                ->where('appname','=',\Config::get('jex.pickup_app'))
                                //->where('fulfillmentCode'.'=',$fulfillment_code)
                                ->where('boxId','=',strval($box_id))
                                ->orderBy('mtimestamp', 'desc')
                                ->first();
        //print_r($last);

        if($last){
            return $last->status;
        }else{
            return 'belum diambil';
        }
    }


}
