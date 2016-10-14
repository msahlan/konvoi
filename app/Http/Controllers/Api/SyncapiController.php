<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Scanlog;
use App\Models\Geolog;
use App\Models\Boxstatus;
use App\Models\Box;
use App\Models\Boxid;
use App\Models\Shipment;
use App\Models\Orderstatuslog;
use App\Models\Orderlog;
use App\Models\Imagemeta;
use App\Models\Uploaded;
use App\Models\History;
use App\Models\Shipmentlog;
use App\Models\Pickup;


use App\Models\Deliverydetail;
use App\Models\Deliverynote;

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

class SyncapiController extends Controller {
    public $controller_name = '';

    public function  __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        //$this->model = "Member";
        $this->controller_name = strtolower( str_replace('Controller', '', get_class()) );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postScanlog()
    {
        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';
        //$user = \Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'no id' ));
        }


        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();

        $logIds = array();
        foreach( $json as $j){
            if(isset( $j['logId'] )){
                $logIds[] = $j['logId'];
            }
        }

        $logIds = array_unique($logIds);

        $exLogId = Scanlog::whereIn('logId', $logIds )->get(array('logId'));

        $existLog = array();
        foreach ($exLogId as $ex ) {
            $existLog[] = $ex->logId;
        }

        foreach( $json as $j){

            if(isset( $j['logId'] )){

                $j['appname'] = $appname;

                $j['deviceActor'] = (isset($user->identifier))?$user->identifier:'';

                $j['deliveryDevId'] = $user->identifier;

                if(isset($j['timestamp'])){
                    $j['mtimestamp'] = new MongoDate(strtotime($j['timestamp']));
                }

                //$log = Scanlog::where('logId', $j['logId'] )->first();

                if( in_array($j['logId'], $existLog )){
                //if($log){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }else{
                    Scanlog::insert($j);
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }
            }
        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postBoxstatus()
    {
        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';

        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Unauthorized Device' ));
        }

        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();

        foreach( $json as $j){

            if(isset( $j['logId'] )){
                if(isset($j['datetimestamp'])){
                    $j['mtimestamp'] = new MongoDate(strtotime($j['datetimestamp']));
                }

                $log = Boxstatus::where('logId', $j['logId'] )->first();

                if($log){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }else{
                    /*
                    $bs = array();
                    foreach($j as $k=>$v){
                        $bs[$this->camel_to_underscore($k)] = $v;
                    }*/
                    $j['appname'] = $appname;
                    Boxstatus::insert($j);
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }
            }
        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postPickupstatus()
    {
        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';
        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Device Unregistered' ));
        }

        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();



        foreach( $json as $j){

            //$j['mtimestamp'] = new MongoDate();

            if(is_array($j)){


                $olog = new Orderstatuslog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new MongoDate(time());

                if($olog->disposition == $key && isset($user->node_id)){

                    $olog->position = $user->node_id;
                }

                $r = $olog->save();

                $shipment = Pickup::where('transactionId','=',$olog->deliveryId)->first();

                if($shipment){

                    $ts = new MongoDate();
                    $pre = clone $shipment;

                    //$shipment->status = $olog->status;
                    $shipment->pickup_status = $olog->pickupStatus;

                    if($olog->disposition == $key && isset($user->node_id)){

                        $shipment->position = $user->node_id;
                    }

                    //$shipment->save();

                    $hdata = array();
                    $hdata['historyTimestamp'] = $ts;
                    $hdata['historyAction'] = 'api_pickup_change_status';
                    $hdata['historySequence'] = 1;
                    $hdata['historyObjectType'] = 'shipment';
                    $hdata['historyObject'] = $shipment->toArray();
                    $hdata['actor'] = $user->identifier;
                    $hdata['actor_id'] = $user->key;

                    History::insert($hdata);

                    $sdata = array();
                    $sdata['timestamp'] = $ts;
                    $sdata['action'] = 'api_pickup_change_status';
                    $sdata['reason'] = 'api_update';
                    $sdata['objectType'] = 'shipment';
                    $sdata['object'] = $shipment->toArray();
                    $sdata['preObject'] = $pre->toArray();
                    $sdata['actor'] = $user->identifier;
                    $sdata['actor_id'] = $user->key;
                    //Shipmentlog::insert($sdata);


                }

                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }

            /*
            if( Orderstatuslog::insert($j) ){
                $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
            }else{
                $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
            }
            */

        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postHubstatus()
    {
        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';
        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Device Unregistered' ));
        }

        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();



        foreach( $json as $j){

            //$j['mtimestamp'] = new MongoDate();

            if(is_array($j)){


                $olog = new Orderstatuslog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new MongoDate(time());

                if($olog->disposition == $key && isset($user->node_id)){

                    $olog->position = $user->node_id;
                }

                $r = $olog->save();

                $shipment = Pickup::where('transactionId','=',$olog->deliveryId)->first();

                if($shipment){

                    $ts = new MongoDate();
                    $pre = clone $shipment;

                    $shipment->warehouse_status = $olog->warehouseStatus;

                    $shipment->warehouse_in = date('Y-m-d H:i:s',time());

                    if($olog->disposition == $key && isset($user->node_id)){

                        $shipment->position = $user->node_id;
                    }

                    $shipment->save();

                    //$shipment->status = $olog->status;

                    $hdata = array();
                    $hdata['historyTimestamp'] = $ts;
                    $hdata['historyAction'] = 'api_hub_change_status';
                    $hdata['historySequence'] = 1;
                    $hdata['historyObjectType'] = 'shipment';
                    $hdata['historyObject'] = $shipment->toArray();
                    $hdata['actor'] = $user->identifier;
                    $hdata['actor_id'] = $user->key;

                    History::insert($hdata);

                    $sdata = array();
                    $sdata['timestamp'] = $ts;
                    $sdata['action'] = 'api_hub_change_status';
                    $sdata['reason'] = 'api_update';
                    $sdata['objectType'] = 'shipment';
                    $sdata['object'] = $shipment->toArray();
                    $sdata['preObject'] = $pre->toArray();
                    $sdata['actor'] = $user->identifier;
                    $sdata['actor_id'] = $user->key;
                    //Shipmentlog::insert($sdata);


                }

                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }

            /*
            if( Orderstatuslog::insert($j) ){
                $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
            }else{
                $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
            }
            */

        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postOrderstatus()
    {
        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';
        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Unauthorized Device' ));
        }

        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();



        foreach( $json as $j){

            //$j['mtimestamp'] = new MongoDate();

            if(is_array($j)){


                $olog = new Orderstatuslog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new MongoDate(time());

                $olog->appname = $appname;

                $r = $olog->save();

                $shipment = Pickup::where('transactionId','=',$olog->deliveryId)
                                //->where('status','!=','delivered')
                                ->first();

                if($shipment){

                    $ts = new MongoDate();
                    $pre = clone $shipment;

                    $changes = false;

                    if($appname == Config::get('jex.pickup_app')){
                        $shipment->pickup_status = $olog->pickupStatus;
                        $changes = true;

                    }elseif($appname == Config::get('jex.hub_app')){
                        $shipment->warehouse_status = $olog->warehouseStatus;
                        $changes = true;

                    }elseif($appname == Config::get('jex.tracker_app')){

                        /*
                        if($shipment->status == 'delivered' || $shipment->status == 'returned'){
                            $changes = false;
                        }else{
                            $shipment->status = $olog->status;
                            $shipment->courier_status = $olog->courierStatus;

                            if($olog->status == 'pending'){
                                //$shipment->pending_count = $shipment->pending_count + 1;
                            }elseif($olog->status == 'delivered'){
                                if($olog->deliverytime == '' || $olog->deliverytime == '0000-00-00 00:00:00'){
                                    $shipment->deliverytime = date('Y-m-d H:i:s',time());
                                }else{
                                    $shipment->deliverytime = $olog->deliverytime;
                                }
                            }

                            $changes = true;

                        }
                        */

                        $changes = false;

                    }

                    if($changes == true){
                        $shipment->save();
                    }

                    $hdata = array();
                    $hdata['historyTimestamp'] = $ts;
                    $hdata['historyAction'] = 'api_shipment_change_status';
                    $hdata['historySequence'] = 1;
                    $hdata['historyObjectType'] = 'shipment';
                    $hdata['historyObject'] = $shipment->toArray();
                    $hdata['actor'] = $user->identifier;
                    $hdata['actor_id'] = $user->key;

                    History::insert($hdata);

                    $sdata = array();
                    $sdata['timestamp'] = $ts;
                    $sdata['action'] = 'api_shipment_change_status';
                    $sdata['reason'] = 'api_update';
                    $sdata['objectType'] = 'shipment';
                    $sdata['object'] = $shipment->toArray();
                    $sdata['preObject'] = $pre->toArray();
                    $sdata['actor'] = $user->identifier;
                    $sdata['actor_id'] = $user->key;
                    //Shipmentlog::insert($sdata);

                }

                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }

            /*
            if( Orderstatuslog::insert($j) ){
                $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
            }else{
                $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
            }
            */

        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postMeta()
    {

        $key = Request::input('key');

        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        $appname = (Request::has('app'))?Request::input('app'):'app.name';

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image meta failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Device Unregistered' ));
        }

        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();

        foreach( $json as $j){

            //$j['mtimestamp'] = new MongoDate(time());

            if(is_array($j)){
                $blog = new Imagemeta();

                foreach ($j as $k=>$v) {
                    $blog->{$k} = $v;
                }

                $blog->appname = $appname;
                $blog->mtimestamp = new MongoDate(time());

                $r = $blog->save();

                $upl = Uploaded::where('_id','=',new MongoId($blog->extId))->first();

                if($upl){
                   $upl->is_signature = $blog->isSignature;
                   $upl->latitude = $blog->latitude;
                   $upl->longitude = $blog->longitude;
                   $upl->delivery_id = $blog->parentId;
                   $upl->photo_time = $blog->photoTime;
                   $upl->save();
                }

                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['extId'] );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }


        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postBox()
    {
        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';

        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Unauthorized Device' ));
        }

        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();

        foreach( $json as $j){

            //$j['mtimestamp'] = new MongoDate(time());

            if(is_array($j)){
                $blog = new Boxid();

                foreach ($j as $k=>$v) {
                    $blog->{$k} = $v;
                }

                $blog->appname = $appname;

                //$blog->mtimestamp = new MongoDate(time());
                $blog->mtimestamp = date('Y-m-d H:i:s',time());

                $box = Box::where('delivery_id','=',$blog->deliveryId)
                        ->where('merchant_trans_id','=',$blog->merchantTransId)
                        ->where('fulfillment_code','=',$blog->fulfillmentCode)
                        ->where('box_id','=',$blog->boxId)
                        ->first();


                if($box){

                    if($appname == Config::get('jex.pickup_app')){
                        $box->pickupStatus = $blog->pickupStatus;
                    }elseif($appname == Config::get('jex.hub_app')){
                        $box->warehouseStatus = $blog->warehouseStatus;
                    }else{
                        $box->deliveryStatus = $blog->deliveryStatus;
                        $box->courierStatus = $blog->courierStatus;
                    }

                    $box->save();
                }



                $r = $blog->save();

                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }


        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postHuborder()
    {
        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';
        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Device Unregistered' ));
        }

        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();

        foreach( $json as $j){

            //$j['mtimestamp'] = new MongoDate(time());

            if(is_array($j)){
                $olog = new Orderlog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->appname = $appname;

                $olog->deviceActor = (isset($user->identifier))?$user->identifier:'';

                $olog->mtimestamp = new MongoDate(time());

                $olog->warehouseDevId = $user->identifier;

                if($olog->warehouseStatus == Config::get('jayon.trans_status_pu2wh') ){
                    if($olog->warehouseIn == '' || $olog->warehouseIn == '0000-00-00 00:00:00'){
                        $olog->warehouseIn = date('Y-m-d H:i:s', time());
                    }
                }

                if($olog->disposition == $key && isset($user->node_id)){

                    $olog->position = $user->node_id;
                }


                $r = $olog->save();

                $shipment = Pickup::where('transactionId','=',$olog->deliveryId)->first();

                if($shipment){
                    //$shipment->status = $olog->status;

                    //$check = $this->checkPickedUp($olog->deliveryId, 'warehouseStatus' ,'   diterima di gudang' ,Config::get('jex.hub_app') , $key  );

                    $shipment->warehouse_status = $olog->warehouseStatus;

                    if($olog->warehouseStatus == Config::get('jayon.trans_status_pu2wh') ){
                        if($olog->warehouseIn == '' || $olog->warehouseIn == '0000-00-00 00:00:00'){
                            $shipment->warehouse_in = date('Y-m-d H:i:s', time());
                        }else{
                            $shipment->warehouse_in = $olog->warehouseIn;
                        }
                    }
                    /*
                    if($olog->disposition == $key && isset($user->node_id)){

                        $shipment->position = $user->node_id;
                    }*/

                    /*
                    $shipment->pending_count = new MongoInt32($olog->pendingCount) ;

                    if($olog->courierStatus == Config::get('jayon.trans_cr_oncr') || $olog->courierStatus == Config::get('jayon.trans_cr_oncr_partial'))
                    {
                        $shipment->pickup_status = Config::get('jayon.trans_status_pickup');
                    }
                    */
                    $shipment->save();
                }


                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }


        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postPickuporder()
    {

        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';
        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Device Unregistered' ));
        }

        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();

        foreach( $json as $j){

            //$j['mtimestamp'] = new MongoDate(time());

            if(is_array($j)){
                $olog = new Orderlog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new MongoDate(time());

                $olog->appname = $appname;

                $olog->deviceActor = (isset($user->identifier))?$user->identifier:'';

                $olog->pickupDevId = $user->identifier;

                if($olog->disposition == $key && isset($user->node_id)){

                    $olog->position = $user->node_id;
                }

                $r = $olog->save();

                $shipment = Pickup::where('transactionId','=',$olog->deliveryId)->first();

                if($shipment){

                    //$check = $this->checkPickedUp($olog->deliveryId, 'pickupStatus' ,'sudah diambil' ,Config::get('jex.pickup_app') , $user->identifier  );

                    $changes = false;

                    if($olog->pickuptime == '' || $olog->pickuptime == '0000-00-00 00:00:00' ){
                        $pickuptime = date('Y-m-d H:i:s',time());
                    }else{
                        $pickuptime = $olog->pickuptime;
                    }

                    $shipment->pickup_dev_id = $user->identifier;
                    $shipment->pickup_status = $olog->pickupStatus;
                    $shipment->pickuptime = $pickuptime;

                    if(trim($olog->deliveryNote) != ''){
                        $shipment->delivery_note = trim($olog->deliveryNote);
                    }

                    if($olog->pickupStatus == Config::get('jayon.trans_status_pickup')){
                        $changes = true;
                    }else if($olog->pickupStatus == Config::get('jayon.trans_status_no_pickup')){
                        if(trim($olog->deliveryNote) != ''){
                            $changes = true;
                        }
                    }

                    if($changes == true){
                        $shipment->save();
                    }

                    /*
                    //order currently already pick up
                    if($shipment->pickup_status == Config::get('jayon.trans_status_pickup')){

                        if($olog->pickupStatus == Config::get('jayon.trans_status_pickup')){

                            if(trim($olog->deliveryNote) != ''){
                                $shipment->delivery_note = trim($olog->deliveryNote);
                            }
                            $shipment->pickup_status = $olog->pickupStatus;
                            $shipment->pickuptime = $pickuptime;


                            $changes = true;

                        }else{
                            // tries to cancel pick up
                            // Note should not empty
                            if(trim($olog->deliveryNote) != ''){
                                //OK , allow status change

                                if( trim($olog->deliveryNote) != '' ){
                                    $shipment->delivery_note = trim($olog->deliveryNote);
                                }
                                $shipment->pickup_status = $olog->pickupStatus;
                                $shipment->pickuptime = $pickuptime;

                                $changes = true;

                            }
                        }

                    }else{

                       if($olog->pickupStatus == Config::get('jayon.trans_status_pickup')){
                            if( trim($olog->deliveryNote) != '' ){
                                $shipment->delivery_note = trim($olog->deliveryNote);
                            }
                            $shipment->pickup_status = $olog->pickupStatus;
                            $shipment->pickuptime = $pickuptime;

                            $changes = true;

                       }else{

                       }

                    }
                    */


                    //if($shipment->pickup_status != Config::get('jayon.trans_status_pickup') ||
                    //    ($olog->pickupStatus != Config::get('jayon.trans_status_pickup') && trim($olog->deliveryNote) != '' )
                    //){
                    /*
                    if($olog->pickup_status == Config::get('jayon.trans_status_pickup') ||
                        ($olog->pickupStatus != Config::get('jayon.trans_status_pickup') && trim($olog->deliveryNote) != '' )
                     ){

                        $shipment->pickup_status = $olog->pickupStatus;

                        if( $olog->pickupStatus == Config::get('jayon.trans_status_pickup')){

                            if($olog->pickuptime == '' || $olog->pickuptime == '0000-00-00 00:00:00' ){
                                $pickuptime = date('Y-m-d H:i:s',time());
                            }else{
                                $pickuptime = $olog->pickuptime;
                            }

                            if( trim($olog->deliveryNote) != '' ){
                                $shipment->delivery_note = trim($olog->deliveryNote);
                            }

                            if($shipment->pickuptime == '' || $shipment->pickuptime == '0000-00-00 00:00:00' ){
                                $shipment->pickuptime = $pickuptime;
                            }

                            $shipment->pickup_dev_id = $user->identifier;

                        }

                        $shipment->save();
                    }
                    */
                    //$shipment->status = $olog->status;
                }


                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }


        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postOrder()
    {
        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';
        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Unauthorized Device' ));
        }

        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();

        foreach( $json as $j){

            //$j['mtimestamp'] = new MongoDate(time());

            if(is_array($j)){
                $olog = new Orderlog();

                foreach ($j as $k=>$v) {
                    $olog->{$k} = $v;
                }

                $olog->mtimestamp = new MongoDate(time());

                $olog->appname = $appname;

                $olog->deviceActor = (isset($user->identifier))?$user->identifier:'';

                $olog->deliveryDevId = $user->identifier;

                $r = $olog->save();

                $shipment = Pickup::where('transactionId','=',$olog->transactionId)
                                //->where('status','!=','delivered')
                                ->first();

                $changes = false;

                if($shipment){


                    //|| $shipment->change_actor != 'APP'
                    if($shipment->status == 'success' || $shipment->status == 'failed'
                            //|| ( $shipment->status != 'delivered' && $shipment->status != 'returned' && $shipment->change_actor != 'APP')
                      ){
                        $changes = false;
                    }else{
                        $changes = true;
                    }

                    $shipment->courier_status = $olog->courierStatus;

                    if($olog->deliveryNote != ''){
                        $shipment->delivery_note = $olog->deliveryNote;
                    }

                    if($olog->latitude != ''){
                        $shipment->latitude = doubleval($olog->latitude);
                        $shipment->dir_lat = doubleval($olog->latitude);
                    }

                    if($olog->longitude != ''){
                        $shipment->longitude = doubleval($olog->longitude);
                        $shipment->dir_lon = doubleval($olog->longitude);
                    }

                    $shipment->save();

                    /*
                    if($olog->status == 'pending'){
                        $shipment->status = $olog->status;
                        if($shipment->delivery_note != $olog->deliveryNote){
                            $shipment->pending_count = $shipment->pending_count + 1;
                        }
                        $shipment->delivery_note = $olog->deliveryNote;

                    }else
                    */
                    if($olog->status == 'success' || $olog->status == 'failed' || $olog->status == 'pending'){

                        if($olog->status == 'pending'){
                            if($shipment->delivery_note != $olog->deliveryNote){
                                //$shipment->pending_count = $shipment->pending_count + 1;
                            }
                        }

                        if( $olog->status == 'success' && $shipment->status != 'success' ){
                            if($olog->deliverytime == '' || $olog->deliverytime == '0000-00-00 00:00:00'){
                                $shipment->deliverytime = date('Y-m-d H:i:s',time());
                                $shipment->eventtime = date('Y-m-d H:i:s',time());
                                $shipment->deliverytimeTs = new MongoDate(time());
                                $shipment->eventtimeTs = new MongoDate(time());
                            }else{
                                $shipment->deliverytime = $olog->deliverytime;
                                $shipment->eventtime = $olog->deliverytime;

                                $shipment->deliverytimeTs = new MongoDate(strtotime($olog->deliverytime));
                                $shipment->eventtimeTs = new MongoDate(strtotime($olog->deliverytime));

                            }
                        }else{
                            if($olog->deliverytime == '' || $olog->deliverytime == '0000-00-00 00:00:00'){
                                $shipment->eventtime = date('Y-m-d H:i:s',time());
                                $shipment->eventtimeTs = new MongoDate(time());
                            }else{
                                $shipment->eventtime = $olog->deliverytime;
                                $shipment->eventtimeTs = new MongoDate(strtotime($olog->deliverytime));
                            }
                        }

                        $shipment->change_actor = 'APP';

                        $shipment->status = $olog->status;

                    }

                    //$shipment->status = $olog->status;
                    //$shipment->courier_status = $olog->courierStatus;

                    /*
                    if($olog->disposition == $key && isset($user->node_id)){

                        $shipment->position = $user->node_id;
                    }*/

                    /*
                    $shipment->pending_count = new MongoInt32($olog->pendingCount) ;

                    if($olog->courierStatus == Config::get('jayon.trans_cr_oncr') || $olog->courierStatus == Config::get('jayon.trans_cr_oncr_partial'))
                    {
                        $shipment->pickup_status = Config::get('jayon.trans_status_pickup');
                    }
                    */
                    if($changes == true){
                        $shipment->save();
                    }

                    $is_there = Geolog::where('datetimestamp','=',$shipment->deliverytime)
                                        ->where('deliveryId' ,'=',  $shipment->delivery_id)
                                        ->where('deviceId' ,'=',  $user->identifier)
                                        ->where('appname','=', $appname )
                                        ->where('status','=', $olog->status)
                                        ->where('sourceSensor','=','gps')
                                        ->count();

                    if($is_there == 0){

                        $geolog = array(
                                'datetimestamp' => $shipment->deliverytime,
                                'deliveryId' => $shipment->delivery_id,
                                'deviceId' => $user->identifier,
                                'status'=>$olog->status,
                                'deviceKey' => $user->key,
                                'extId' => 'noid',
                                'latitude' => doubleval($olog->latitude),
                                'longitude' => doubleval($olog->longitude),
                                'sourceSensor' => 'gps',
                                'timestamp' => strval( strtotime($shipment->deliverytime)),
                                'uploaded' => 1,
                                'appname'=>$appname,
                                'mtimestamp' => new MongoDate( strtotime($shipment->deliverytime) )
                            );

                        Geolog::insert($geolog);

                    }


                }


                if( $r ){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>'log inserted' );
                }else{
                    $result[] = array('status'=>'NOK', 'timestamp'=>time(), 'message'=>'insertion failed' );
                }

            }


        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postGeolog()
    {
        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';
        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Unauthorized Device' ));
        }

        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();

        $dids = array();

        foreach( $json as $j){
            if( isset($j['deliveryId']) && $j['deliveryId'] != ''){
                $dids[] = $j['deliveryId'];
            }
        }

        $dids = array_unique($dids);

        $ships = Shipment::whereIn('delivery_id', $dids)->get();

        $shipments = array();
        foreach($ships->toArray() as $sh){
            $shipments[$sh['delivery_id']] = $sh;
        }

        $logIds = array();
        foreach( $json as $j){
            if(isset( $j['logId'] )){
                $logIds[] = $j['logId'];
            }
        }

        $logIds = array_unique($logIds);

        $exLogId = Geolog::whereIn('logId', $logIds )->get(array('logId'));

        $existLog = array();
        foreach ($exLogId as $ex ) {
            $existLog[] = $ex->logId;
        }

        //print_r($existLog);
        //die();


        foreach( $json as $j){

            if(isset( $j['logId'] )){

                $j['appname'] = $appname;

                if(isset($shipments[ $j['deliveryId'] ]))
                {
                    $j['deliveryType'] = $shipments[ $j['deliveryId'] ]['delivery_type'];
                    $j['merchantTransId'] = $shipments[ $j['deliveryId'] ]['merchant_trans_id'];
                    $j['fulfillmentCode'] = $shipments[ $j['deliveryId'] ]['fulfillment_code'];
                }

                if(isset($j['datetimestamp'])){
                    $j['mtimestamp'] = new MongoDate(strtotime($j['datetimestamp']));
                }

                //$log = Geolog::where('logId', $j['logId'] )->first();

                if( in_array($j['logId'], $existLog )){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'], 'insert'=>'0' );
                }else{
                    Geolog::insert($j);
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'], 'insert'=>'1' );
                }
            }
        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync scan log'));

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postNote()
    {
        date_default_timezone_set('Asia/Jakarta');

        $key = Request::input('key');

        $appname = (Request::has('app'))?Request::input('app'):'app.name';
        //$user = Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>'Unauthorized Device' ));
        }


        $json = Request::input();

        $batch = Request::input('batch');

        $result = array();

        foreach( $json as $j){

            if(isset( $j['logId'] )){

                $j['appname'] = $appname;

                if(isset($j['datetimestamp'])){
                    $j['mtimestamp'] = new MongoDate(strtotime($j['datetimestamp']));
                }

                $log = Deliverynote::where('logId', $j['logId'] )->first();

                if($log){
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }else{
                    Deliverynote::insert($j);
                    $result[] = array('status'=>'OK', 'timestamp'=>time(), 'message'=>$j['logId'] );
                }

                $pending = Deliverynote::where('deliveryId','=',$j['deliveryId'])
                                ->where('status','=','pending')
                                ->count();

                if($pending > 0){
                    $ord = Pickup::where('transactionId','=',$j['deliveryId'])->first();
                    $ord->pending_count = $pending;
                    $ord->save();
                }

            }
        }

        //print_r($result);

        //die();
        $actor = $user->identifier.' : '.$user->devname;

        Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'sync note'));

        return Response::json($result);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function putAssets()
    {

        $json = Request::input();

        $key = Request::input('key');

        $json['mode'] = 'edit';

        $batch = Request::input('batch');

        Dumper::insert($json);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function putRacks()
    {

        $json = Request::input();

        $key = Request::input('key');

        $json['mode'] = 'edit';

        $batch = Request::input('batch');

        Dumper::insert($json);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function putLocations()
    {

        $json = Request::input();

        $key = Request::input('key');

        $json['mode'] = 'edit';

        $batch = Request::input('batch');

        Dumper::insert($json);

    }

    public function camel_to_underscore($str)
    {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }

    public function checkPickedUp($delivery_id, $status_field ,$status ,$appname, $devicename  )
    {
        $exist = Orderlog::where('deliveryId','=',$delivery_id)
                        ->where($status_field,'=',$status)
                        ->where('appname','=', $appname)
                        ->where('pickupDevId','!=',$devicename)
                        ->count();

        if($exist > 0){
            return true;
        }else{
            return false;
        }
    }

}