<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;

class StatusController extends \BaseController {

    public $controller_name = '';

    public $model;

    public $sql_connection;

    public $sql_table_name;

    public function  __construct()
    {
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
        $awb = Input::get('awb');

        if( is_null($key) || $key == ''){
            $actor = 'no id : no name';
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'empty key'));

            return \Response::json(array('status'=>'ERR:EMPTYKEY', 'timestamp'=>time(), 'message'=>'Empty Key' ));
        }


        $app = \Application::where('key','=',$key)->first();

        if($app){
            if($order_id != '' && $ff_id != ''){
                $order = \Shipment::where('delivery_id','=',trim($awb))
                            //->where('fulfillment_code','=',trim($ff_id))
                            ->where('application_key','=',trim($key))
                            ->first();
            }else{
                $order = false;
            }

            if($order){
                //print_r($order);
                $actor = 'merchant id :'.$app->merchant_id;
                \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'status awb: '.$awb));

                $status = array('awb'=>$order->delivery_id,'timestamp'=>date('Y-m-d H:i:s',time()) ,
                    'pending'=>$order->pending_count,
                    'status'=>$order->status,'note'=>$order->delivery_note);

                return \Response::json(array('status'=>'OK','order_status'=>$status));
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

            $model = new \Shipment();

            $merchant_id = $app->merchant_id;

            $result = array();

            foreach( $json as $j){

                if(is_array($j)){

                    $model = $model->orWhere(function($q) use($j, $key){
                        $awb = $j['awb'];
                        $q->where('delivery_id','=',trim($awb))
                            //->where('merchant_trans_id','=',trim($order_id))
                            //->where('fulfillment_code','=',trim($ff_id))
                            //->where('merchant_id','',$merchant_id)
                            ->where('application_key','=',trim($key));
                            //->whereNotNull('delivery_id');

                    });

                }

            }

            $awbs = $model->get();


            if($awbs){
                foreach($awbs as $awb){

                    $pod = array();

                    $pictures = \Uploaded::where('parent_id','=',$awb->delivery_id)->get();


                    if($pictures){

                        foreach($pictures as $pic){
                            $p = new \stdClass();
                            $p->thumbnail = $pic->thumbnail_url;
                            $p->pictures = $pic->large_url;
                            $p->is_signature = $pic->is_signature;
                            $pod[] = $p;
                        }
                    }

                    $result[] = array('awb'=>$awb->delivery_id,
                        'timestamp'=>date('Y-m-d H:i:s',time()) ,
                        'pending'=>$awb->pending_count,
                        'district'=>$awb->buyerdeliveryzone,
                        'status'=>$awb->status,
                        'pickup_time'=>$awb->pickuptime,
                        'delivery_time'=>$awb->deliverytime,
                        'pod'=>$pod,
                        'note'=>$awb->delivery_note);
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

}
