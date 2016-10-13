<?php
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

use App\Models\Shipment;
use App\Models\Deliveryfee;
use App\Models\Codsurcharge;
use App\Models\Printsession;
use App\Models\Merchant;
use App\Models\Application;
use App\Models\Geolog;
use App\Models\Uploaded;
use App\Models\Company;
use App\Models\Device;
use App\Models\Courier;
use App\Models\History;
use App\Models\Shipmentlog;
use App\Models\Creditaccount;
use App\Models\Pickup;

use App\Helpers\Prefs;
use App\Helpers\PointLocation;

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
use DB;
use HTML;

class AjaxController extends BaseController {

    /*
    |--------------------------------------------------------------------------
    | The Default Controller
    |--------------------------------------------------------------------------
    |
    | Instead of using RESTful routes and anonymous functions, you might wish
    | to use controllers to organize your application API. You'll love them.
    |
    | This controller responds to URIs beginning with "home", and it also
    | serves as the default controller for the application, meaning it
    | handles requests to the root of the application.
    |
    | You can respond to GET requests to "/home/profile" like so:
    |
    |       public function action_profile()
    |       {
    |           return "This is your profile!";
    |       }
    |
    | Any extra segments are passed to the method as parameters:
    |
    |       public function action_profile($id)
    |       {
    |           return "This is the profile for user {$id}.";
    |       }
    |
    */

    public function __construct(){

    }

    public function getIndex()
    {
    }

    public function postIndex()
    {

    }

    public function postDelfile(){
        $_id = Request::input('id');

        $file = Uploaded::find($_id);

        if($file){
            $file->deleted = 1;
            $file->save();
            return Response::json( array('status'=>'OK', 'timestamp'=>time() ));
        }else{
            return Response::json( array('status'=>'NOK', 'timestamp'=>time() ));
        }

    }

    public function postGeneratedata()
    {
        $in = Request::input();

        $date = $in['day'];

        $month = $in['month'];

        $scope = $in['scope'];

        $creditor = $in['creditor'];

        $accounts = Creditaccount::where('pickupDate','=',intval($date));
        if($creditor != ''){
            $accounts = $accounts->where('creditor','=',$creditor);
        }

        $accounts = $accounts->get();


        $cnt = 0;

        foreach($accounts as $acc){
            $acc = $acc->toArray();
            $acc['accountId'] = $acc['_id'];
            $acc['periodMonth'] = intval($month);
            $acc['assignmentDate'] = date( 'Y-m-d 00:00:00', time() );
            $acc['assignmentDateTs'] = new MongoDate( strtotime($acc['assignmentDate']) );
            $acc['status'] = 'new';
            $acc['transactionId'] = Prefs::getDeliveryId();

            unset($acc['_id']);
            unset($acc['updated_at']);
            unset($acc['created_at']);

            if(Pickup::where('accountId', '=', $acc['accountId'])->where('status','=','new')->where( 'assignmentDate' ,'=', $acc['assignmentDate'])->count() > 0 ){

            }else{
                $cnt++;
                Pickup::create($acc);
            }

        }

        return Response::json(['status'=>'OK', 'msg'=>$cnt.' pickup order created' ]);

    }

    public function getOrg()
    {
        $orgs = Company::all();

        return Response::json($orgs);
    }

    public function postRoutelist()
    {
        $in = Request::input();

        $ts = $in['timestamp'];
        $device = $in['devname'];
        $devid = $in['devid'];

        $orders = Shipment::where('assignment_date','=',$ts)
                    ->where('device_id','=',$devid)
                    ->orderBy('assignment_seq','desc')
                    ->get();

        //print_r($orders);

        $rtlist = array();

        if($orders){
            foreach($orders as $ord){
                $rtlist[] = View::make('route.rtdetail')->with('order',$ord)->render();
            }
        }

        print implode('',$rtlist);

    }

    public function postSaveroutelist()
    {

    }

    public function postSaveloc()
    {
        $delivery_id = Request::input('delivery_id');
        $lat = Request::input('lat');
        $lon = Request::input('lon');

        if( $delivery_id != ''){

            $shipment = Shipment::where('delivery_id','=',$delivery_id)->first();

            if($shipment){

                if($lat != ''){
                    $shipment->latitude = doubleval($lat);
                    $shipment->dir_lat = doubleval($lat);
                }

                if($lon != ''){
                    $shipment->longitude = doubleval($lon);
                    $shipment->dir_lon = doubleval($lon);
                }

                print json_encode(array('result'=>'ok'));

            }else{

                print json_encode(array('result'=>'nok','message'=>'order not found' ));

            }

        }else{

            print json_encode(array('result'=>'nok','message'=>'order not found' ));

        }

    }


    public function postLocationlog()
    {


        $device_name = Request::input('device_identifier');
        $timestamp = Request::input('timestamp');
        $courier = Request::input('courier');
        $status = Request::input('status');

        $stepping = Request::input('stepping');

        $device_name = ($device_name == '')?'all':$device_name;
        $timestamp = ($timestamp == '')? date('Y-m-d',time()) :$timestamp;
        $courier = ($courier == '')?'all':$courier;
        $status = ($status == '')?'all':$status;

        $statuses = config('jex.mapdefstatus');


        $locations = array();

        $paths = array();

        $pathdummy = array();

        $timestamps = explode(' - ',$timestamp);

        if(count($timestamps) == 2){
            $daystart = trim($timestamps[0]).' 00:00:00';
            $dayend = trim($timestamps[1]).' 23:59:59';
        }else{
            $daystart = $timestamp.' 00:00:00';
            $dayend = $timestamp.' 23:59:59';
        }

        $model = new Geolog();

        if($device_name == 'all'){
            $devices = Geolog::distinct('deviceId')
                        ->where('appname','=', config('jex.tracker_app'))
                        //->whereBetween('mtimestamp',array($daystart,$dayend))
                        ->get('deviceId');
        }else{
            $devices = Geolog::distinct('deviceId')
                ->where('appname','=', config('jex.tracker_app'))
                //->whereBetween('mtimestamp',array($daystart,$dayend))
                ->where('deviceId','regexp','/'.$device_name.'/i')
                ->get('deviceId');
        }

        $daystart = new MongoDate( strtotime($daystart) );
        $dayend = new MongoDate( strtotime($dayend) );

        $polyfence = new PointLocation();

        $polygon = config('jex.jakartafence');

        foreach($devices->toArray() as $d){

            $deviceId = $d[0];

            $mapcolor = Prefs::get_device_color($deviceId);

            $locv = array();

            $locv = $this->devLocation($deviceId,$daystart,$dayend,$status,$stepping);

            if( count( $locv ) > 0){

                $devpath = array();
                $devlocations = array();

                foreach($locv as $t=>$l){

                    $lat = (isset($l->latitude))?doubleval($l->latitude):0;
                    $lng = (isset($l->longitude))?doubleval($l->longitude):0;
                    $dstatus = (isset($l->status))?$l->status:'report';


                    $point = $lat.' '.$lng;
                    $inside = $polyfence->pointInPolygon($point, $polygon);

                    if($lat != 0 && $lng != 0 && $inside){

                        $pod = 0;
                        if(in_array($dstatus,$statuses)){
                            $pod = $this->getImages($l->deliveryId);
                        }

                        $devlocations[] = array(
                            'data'=>array(
                                    'id'=>$l->_id,
                                    'lat'=>$lat,
                                    'lng'=>$lng,
                                    'timestamp'=>$l->datetimestamp,
                                    'identifier'=>$l->deviceId,
                                    'delivery_id'=>$l->deliveryId,
                                    'status'=>$dstatus,
                                    'pod'=>$pod
                                )
                            );
                        $devpath[] = array(
                                $lat,
                                $lng
                            );
                        $pathdummy[] = array(
                                $l->deviceId,
                                $l->timestamp,
                                $lat,
                                $lng
                            );
                    }
                }
                $locations[] = $devlocations;
                $paths[]=array('color'=>$mapcolor,'poly'=>$devpath);
            }
        }

        print json_encode(array('result'=>'ok','locations'=>$locations,'paths'=>$paths, 'pathdummy'=>$pathdummy, 'q'=>'' ));

    }

    public function getCourier()
    {
        $q = Request::input('term');

        $q = '%'.$q.'%';

        $couriers = Courier::where('fullname','like',$q)
                            //->where('status','=','active')
                            ->get();

        $result = array();

        foreach($couriers as $d){
            $result[] = array('id'=>$d->id,'value'=>$d->fullname,'name'=>$d->fullname,'label'=>$d->fullname.' ( '.$d->id.' )');
        }

        return Response::json($result);
    }


    public function getImages($delivery_id)
    {
        $images = Uploaded::where('parent_id','=',$delivery_id)->get();

        $imagelist = array();
        $sign = 0;
        $photo = 0;
        $total = 0;

        if($images){
            foreach ($images as $image) {
                $imagelist[] =  '<img src="'.$image->thumbnail_url.'" alt="pod" style="width:75px;height:auto;" />';
                if($image->is_signature == '1'){
                    $sign++;
                }else{
                    $photo++;
                }
                $total++;
            }
        }

        return array('images'=>implode('',$imagelist), 'sign'=>$sign, 'photo'=>$photo, 'total'=>$total);
    }

    public function devLocation($deviceId,$daystart,$dayend,$status,$stepping = 0)
    {

        $statuses = config('jex.mapdefstatus');

        $devmodel = new Geolog();
        $devmodel = $devmodel
                    ->where('deviceId','=',$deviceId)
                    ->whereBetween('mtimestamp',array($daystart,$dayend));

        if($status != 'all'){
            $devmodel = $devmodel->where('status','regexp','/'.$status.'/i');
        }

        $devmodel = $devmodel->where('appname','=', config('jex.tracker_app'));

        $locs = $devmodel
                    ->orderBy('mtimestamp','desc')
                    ->get();

        $locv = array();

        if($locs && count($locs->toArray()) > 0){

            $locarr = $locs->toArray();

            $locstat = array();

            //print 'device id : '.$deviceId."\r\n";

            // collect all non report points
            foreach($locarr as $n){

                if(isset($n['status'])){
                    //print 'st : '.$deviceId.' : '.$n['status']."\r\n";

                    if(in_array($n['status'], $statuses)){
                        //print 'st nr : '.$deviceId.' : '.$n['status']."\r\n";
                        //$key = strtotime($n['datetimestamp']);
                        $key = doubleval($n['timestamp']);
                        $locstat[$key] = (object) $n;

                    }

                }

            }

            //return $locstat;

            if($stepping > 0){

                $locstep = array();

                $curr = null;
                $next = 1;


                while(!is_null($next)){

                    if(is_null($curr)){
                        $curr = array_shift($locarr);
                        $locstep[] = (object) $curr;
                    }

                    $next = array_shift($locarr);

                    if(!is_null($next)){

                        $st = true;

                        //$key = strtotime($next['datetimestamp']);

                        $key = doubleval($next['timestamp']);

                        $span = doubleval($next['timestamp']) - doubleval($curr['timestamp']);

                        if( abs($span) >= ( doubleval($stepping) * 60)){
                            $curr = $next;
                            if(isset($locstep[$key])){

                            }else{
                                $locstep[$key] = (object) $next;
                            }
                        }
                    }

                }

                $locr = array_merge($locstat, $locstep);

                $locv = array();

                foreach ($locr as $lv) {
                    $locv[ intval($lv->timestamp)  ] = $lv;
                }

                krsort($locv);

                return $locv;

            }else{

                $locv = array();

                $locarr = $locs->toArray();

                foreach ($locarr as $lv) {
                    $locv[ intval($lv['timestamp'])  ] = (object)$lv;
                }

                krsort($locv);

                return $locv;

            }

        }else{
            return $locv;
        }


    }

    public function postShipmentlist()
    {
        $in = Request::input();

        $ids = $in['ids'];

        $shipments = Shipment::whereIn('_id', $in['ids'] )->get();

        $shipments = $shipments->toArray();

        $caps = array();

        for($i = 0; $i < count($shipments); $i++){

            $pick_up_date = $shipments[$i]['pick_up_date'];

            $shipments[$i]['pick_up_date'] = date('Y-m-d', $shipments[$i]['pick_up_date']->sec );

            $city = $shipments[$i]['consignee_olshop_city'];
            $devices = Device::where('city','regex', new MongoRegex('/'.$city.'/i'))->get();

            foreach($devices as $d){
                $caps[$d->key]['identifier'] = $d->identifier;
                $caps[$d->key]['key'] = $d->key;
                $caps[$d->key]['city'] = $d->city;
                $caps[$d->key]['count'] = Shipment::where('device_key',$d->key)->where('pick_up_date',$pick_up_date)->count();
            }

        }


        return Response::json( array('result'=>'OK', 'shipment'=>$shipments, 'device'=>$caps ) );
        //print_r($caps);

    }

    public function postReassigncourier()
    {
        //courier_name:Shia Le Beouf
        //courier_id:5605512bccae5b64010041b6
        //device_key:0f56deadbc6df60740ef5e2c576876b0e3310f7d
        //device_name:JY-002
        //pickup_date:28-09-2

        $in = Request::input();

        $ids = $in['ids'];

        $shipments = Shipment::whereIn('_id', $ids )
                        ->get();


        //print_r($shipments->toArray());

        foreach($shipments as $sh){

            $dev = Shipment::where('courier_id','=', $in['courier_id'])
                        ->where('pick_up_date','=',$sh->pick_up_date)
                        ->orderBy('pick_up_date','desc')
                        ->first();

            //print_r($dev);

            $ts = new MongoDate();

            if($dev){
                $sh->device_name = $dev->device_name;
                $sh->device_key = $dev->device_key;
                $sh->device_id = $dev->device_id;
            }

            $sh->courier_id = $in['courier_id'];
            $sh->courier_name = $in['courier_name'];
            $sh->last_action_ts = $ts;
            $sh->last_action = 'Change Courier';
            $sh->last_reason = $in['reason'];
            $sh->save();

            //print_r(Auth::user());

            $hdata = array();
            $hdata['historyTimestamp'] = $ts;
            $hdata['historyAction'] = 'change_courier';
            $hdata['historySequence'] = 1;
            $hdata['historyObjectType'] = 'shipment';
            $hdata['historyObject'] = $sh->toArray();
            $hdata['actor'] = Auth::user()->name;
            $hdata['actor_id'] = Auth::user()->_id;

            //print_r($hdata);

            History::insert($hdata);

            $sdata = array();
            $sdata['timestamp'] = $ts;
            $sdata['action'] = 'change_courier';
            $sdata['reason'] = $in['reason'];
            $sdata['objectType'] = 'shipment';
            $sdata['object'] = $sh->toArray();
            $sdata['actor'] = Auth::user()->name;
            $sdata['actor_id'] = Auth::user()->_id;
            Shipmentlog::insert($sdata);
            //print_r($sh);
        }

        return Response::json( array('result'=>'OK', 'shipment'=>$shipments ) );

    }

    public function postReassigndevice()
    {
        //courier_name:Shia Le Beouf
        //courier_id:5605512bccae5b64010041b6
        //device_key:0f56deadbc6df60740ef5e2c576876b0e3310f7d
        //device_name:JY-002
        //pickup_date:28-09-2

        $in = Request::input();

        $shipments = Shipment::whereIn('delivery_id', $in['ship_ids'] )->get();


        $device = Device::where('key','=',$in['device'])->first()->toArray();

        //print_r($shipments->toArray());


        foreach($shipments as $sh){

            $cr = Shipment::where('device_key','=', $in['device'])
                    ->where('pick_up_date','=',$sh->pick_up_date)
                    ->orderBy('pick_up_date','desc')
                    ->first();

            //print_r($dev);



            $ts = new MongoDate();

            if($cr){
                $sh->courier_name = $cr->courier_name;
                $sh->courier_id = $cr->courier_id;
            }

            $sh->device_key = $device['key'];
            $sh->device_id = $device['_id'];
            $sh->device_name = $device['identifier'];
            $sh->last_action_ts = $ts;
            $sh->last_action = 'Change Device';
            $sh->last_reason = $in['reason'];
            $sh->save();

            //print_r(Auth::user());

            $hdata = array();
            $hdata['historyTimestamp'] = $ts;
            $hdata['historyAction'] = 'change_device';
            $hdata['historySequence'] = 1;
            $hdata['historyObjectType'] = 'shipment';
            $hdata['historyObject'] = $sh->toArray();
            $hdata['actor'] = Auth::user()->name;
            $hdata['actor_id'] = Auth::user()->_id;

            //print_r($hdata);

            History::insert($hdata);

            $sdata = array();
            $sdata['timestamp'] = $ts;
            $sdata['action'] = 'change_device';
            $sdata['reason'] = $in['reason'];
            $sdata['objectType'] = 'shipment';
            $sdata['object'] = $sh->toArray();
            $sdata['actor'] = Auth::user()->name;
            $sdata['actor_id'] = Auth::user()->_id;
            Shipmentlog::insert($sdata);
            //print_r($sh);
        }

        return Response::json( array('result'=>'OK', 'shipment'=>$shipments ) );

    }

    public function postReschedule(){
        $in = Request::input();
        $results = Shipment::whereIn('_id', $in['ids'])->get();

        date_default_timezone_set('Asia/Jakarta');

        //print_r($results->toArray());

        //if($results){
            $res = false;
        //}else{

            $ts = new MongoDate();

            foreach($results as $sh){
                $sh->pick_up_date = new MongoDate(strtotime($in['date'])) ;

                $sh->last_action_ts = $ts;
                $sh->last_action = 'Reschedule';
                $sh->last_reason = $in['reason'];
                $sh->save();

                //print_r(Auth::user());

                $hdata = array();
                $hdata['historyTimestamp'] = $ts;
                $hdata['historyAction'] = 'change_delivery_date';
                $hdata['historySequence'] = 1;
                $hdata['historyObjectType'] = 'shipment';
                $hdata['historyObject'] = $sh->toArray();
                $hdata['actor'] = Auth::user()->name;
                $hdata['actor_id'] = Auth::user()->_id;

                //print_r($hdata);

                History::insert($hdata);

                $sdata = array();
                $sdata['timestamp'] = $ts;
                $sdata['action'] = 'change_delivery_date';
                $sdata['reason'] = $in['reason'];
                $sdata['objectType'] = 'shipment';
                $sdata['object'] = $sh->toArray();
                $sdata['actor'] = Auth::user()->name;
                $sdata['actor_id'] = Auth::user()->_id;
                Shipmentlog::insert($sdata);


            }
            $res = true;
        //}

        if($res){
            return Response::json(array('result'=>'OK:RESCHED' ));
        }else{
            return Response::json(array('result'=>'ERR:RESCHEDFAILED' ));
        }

    }

    public function postMark(){
        $in = Request::input();

        $action = $in['action'];

        $results = Shipment::whereIn('delivery_id', $in['ids'])->get();

        date_default_timezone_set('Asia/Jakarta');

        //print_r($results->toArray());

        //if($results){
            $res = false;
        //}else{

            $ts = new MongoDate();

            foreach($results as $sh){

                if($action == 'mark_email'){
                    $sh->same_email = 1;
                }else if($action == 'mark_phone'){
                    $sh->same_phone = 1;
                }

                if($action == 'unmark_email'){
                    $sh->same_email = 0;
                }else if($action == 'unmark_phone'){
                    $sh->same_phone = 0;
                }

                $sh->save();

                //print_r($sh);
                //print_r(Auth::user());

                $hdata = array();
                $hdata['historyTimestamp'] = $ts;
                $hdata['historyAction'] = $action;
                $hdata['historySequence'] = 1;
                $hdata['historyObjectType'] = 'shipment';
                $hdata['historyObject'] = $sh->toArray();
                $hdata['actor'] = Auth::user()->name;
                $hdata['actor_id'] = (isset(Auth::user()->_id))?Auth::user()->_id:Auth::user()->id;

                //print_r($hdata);

                History::insert($hdata);
                /*
                $sdata = array();
                $sdata['timestamp'] = $ts;
                $sdata['action'] = 'confirm_data';
                $sdata['reason'] = $in['reason'];
                $sdata['objectType'] = 'shipment';
                $sdata['object'] = $sh->toArray();
                $sdata['actor'] = Auth::user()->name;
                $sdata['actor_id'] = (isset(Auth::user()->_id))?Auth::user()->_id:Auth::user()->id;
                Shipmentlog::insert($sdata);
                */
                $res = true;

            }
        //}

        if($res){
            return Response::json(array('result'=>'OK' ));
        }else{
            return Response::json(array('result'=>'ERR:MARKINGFAILED' ));
        }

    }

    public function postUnmark(){
        $ids = Request::input('ids');
        $action = Request::input('action');

        if($action == 'mark_email'){
            $dataset = array('same_email'=>0);
        }else{
            $dataset = array('same_phone'=>0);
        }



        if($this->db->where_in('delivery_id', $ids)->update($this->config->item('incoming_delivery_table'),$dataset) == TRUE){
            $result = json_encode(array('status'=>'OK','timestamp'=>now()));
        }else{
            $result = json_encode(array('status'=>'ERR:NODUPE','timestamp'=>now() ));
        }

        print $result;

    }


    public function postConfirmdata(){
        $in = Request::input();
        $results = Shipment::whereIn('delivery_id', $in['ids'])->get();

        date_default_timezone_set('Asia/Jakarta');

        //print_r($results->toArray());

        //if($results){
            $res = false;
        //}else{

            $ts = new MongoDate();

            foreach($results as $sh){
                $sh->status = config('jayon.trans_status_confirmed') ;

                //$sh->last_action_ts = $ts;
                //$sh->last_action = 'Cancel Data';
                $sh->delivery_note = $in['reason'];
                $sh->save();

                //print_r($sh);
                //print_r(Auth::user());

                $hdata = array();
                $hdata['historyTimestamp'] = $ts;
                $hdata['historyAction'] = 'confirm_data';
                $hdata['historySequence'] = 1;
                $hdata['historyObjectType'] = 'shipment';
                $hdata['historyObject'] = $sh->toArray();
                $hdata['actor'] = Auth::user()->name;
                $hdata['actor_id'] = (isset(Auth::user()->_id))?Auth::user()->_id:Auth::user()->id;

                //print_r($hdata);

                History::insert($hdata);

                $sdata = array();
                $sdata['timestamp'] = $ts;
                $sdata['action'] = 'confirm_data';
                $sdata['reason'] = $in['reason'];
                $sdata['objectType'] = 'shipment';
                $sdata['object'] = $sh->toArray();
                $sdata['actor'] = Auth::user()->name;
                $sdata['actor_id'] = (isset(Auth::user()->_id))?Auth::user()->_id:Auth::user()->id;
                Shipmentlog::insert($sdata);

                $res = true;

            }
        //}

        if($res){
            return Response::json(array('result'=>'OK' ));
        }else{
            return Response::json(array('result'=>'ERR:CONFIRMFAILED' ));
        }

    }

    public function postCanceldata(){
        $in = Request::input();
        $results = Shipment::whereIn('delivery_id', $in['ids'])->get();

        date_default_timezone_set('Asia/Jakarta');

        //print_r($results->toArray());

        //if($results){
            $res = false;
        //}else{

            $ts = new MongoDate();

            foreach($results as $sh){
                $sh->status = config('jayon.trans_status_canceled') ;

                //$sh->last_action_ts = $ts;
                //$sh->last_action = 'Cancel Data';
                $sh->delivery_note = $in['reason'];
                $sh->save();

                //print_r($sh);
                //print_r(Auth::user());

                $hdata = array();
                $hdata['historyTimestamp'] = $ts;
                $hdata['historyAction'] = 'cancel_data';
                $hdata['historySequence'] = 1;
                $hdata['historyObjectType'] = 'shipment';
                $hdata['historyObject'] = $sh->toArray();
                $hdata['actor'] = Auth::user()->name;
                $hdata['actor_id'] = (isset(Auth::user()->_id))?Auth::user()->_id:Auth::user()->id;

                //print_r($hdata);

                History::insert($hdata);

                $sdata = array();
                $sdata['timestamp'] = $ts;
                $sdata['action'] = 'cancel_data';
                $sdata['reason'] = $in['reason'];
                $sdata['objectType'] = 'shipment';
                $sdata['object'] = $sh->toArray();
                $sdata['actor'] = Auth::user()->name;
                $sdata['actor_id'] = (isset(Auth::user()->_id))?Auth::user()->_id:Auth::user()->id;
                Shipmentlog::insert($sdata);

                $res = true;

            }
        //}

        if($res){
            return Response::json(array('result'=>'OK' ));
        }else{
            return Response::json(array('result'=>'ERR:CANCELFAILED' ));
        }

    }
    public function postScan()
    {
        $in = Request::input();

        $code = $in['txtin'];
        $outlet_id = $in['outlet_id'];
        $use_outlet = $in['search_outlet'];
        $action = strtolower( $in['action'] );

        $outlets = Prefs::getOutlet()->OutletToSelection('_id', 'name', false);

        $outlet_name = $outlets[$outlet_id];

        $res = 'OK';
        $msg = '';

        if(strripos($code, '|')){
            $code = explode('|', $code);
            $SKU = $code[0];
            $unit_id = $code[1];
        }else{
            $SKU = trim($code);
            $unit_id = null;
        }

        switch($action){
            case 'sell':
                if(is_null($unit_id)){
                    $res = 'NOK';
                    $msg = 'SKU: '.$SKU.' <br />Unit ID: NOT FOUND';
                    break;
                }
                $u = Stockunit::where('SKU','=', $SKU)
                    ->where('_id', 'like', '%'.$unit_id )
                    ->first();

                if($u){

                    $ul = $u->toArray();

                    $ul['scancheckDate'] = new MongoDate();
                    $ul['createdDate'] = new MongoDate();
                    $ul['lastUpdate'] = new MongoDate();
                    $ul['action'] = $action;
                    $ul['status'] = 'sold';
                    $ul['deliverTo'] = $outlet_name;
                    $ul['deliverToId'] = $outlet_id;
                    $ul['returnTo'] = $outlet_name;
                    $ul['returnToId'] = $outlet_id;

                    $unit_id = $ul['_id'];

                    unset($ul['_id']);

                    $ul['unitId'] = $unit_id;

                    Stockunitlog::insert($ul);

                    $history = array(
                        'datetime'=>new MongoDate(),
                        'action'=>$action,
                        'price'=>$ul['productDetail']['priceRegular'],
                        'status'=>$ul['status'],
                        'outletName'=>$ul['outletName']
                    );

                    //change status to sold
                    $u->status = 'sold';
                    $u->push('history', $history);

                    $u->save();

                    $res = 'OK';
                    $msg = 'SKU: '.$ul['SKU'].' <br />Unit ID: '.$unit_id.' <br />Outlet: '.$ul['outletName'];

                }else{
                    $res = 'NOK';
                    $msg = 'SKU: '.$SKU.' <br />Unit ID: '.$unit_id.' <br />NOT FOUND in this outlet';
                }

                break;
            case 'check':
                    if(is_null($unit_id)){
                        $res = 'NOK';
                        $msg = 'SKU: '.$SKU.' <br />Unit ID: NOT FOUND';
                        break;
                    }

                    $u = Stockunit::where('SKU','=', $SKU)
                        ->where('_id', 'like', '%'.$unit_id )
                        ->first();

                    if($u){

                        $ul = $u->toArray();

                        $ul['scancheckDate'] = new MongoDate();
                        $ul['createdDate'] = new MongoDate();
                        $ul['lastUpdate'] = new MongoDate();
                        $ul['action'] = $action;
                        $ul['deliverTo'] = $outlet_name;
                        $ul['deliverToId'] = $outlet_id;
                        $ul['returnTo'] = $outlet_name;
                        $ul['returnToId'] = $outlet_id;

                        $unit_id = $ul['_id'];

                        unset($ul['_id']);

                        $ul['unitId'] = $unit_id;

                        Stockunitlog::insert($ul);

                        $history = array(
                            'datetime'=>new MongoDate(),
                            'action'=>$action,
                            'price'=>$ul['productDetail']['priceRegular'],
                            'status'=>$ul['status'],
                            'outletName'=>$ul['outletName']
                        );

                        $u->push('history', $history);

                        $u->save();

                        $res = 'OK';
                        $msg = 'SKU: '.$ul['SKU'].' <br />Unit ID: '.$unit_id.' <br />Outlet: '.$ul['outletName'];

                    }else{
                        $res = 'NOK';
                        $msg = 'SKU: '.$SKU.' <br />Unit ID: '.$unit_id.' <br />NOT FOUND in this outlet';
                    }

                break;
            case 'deliver':
                    if(is_null($unit_id)){
                        $res = 'NOK';
                        $msg = 'SKU: '.$SKU.' <br />Unit ID: NOT FOUND';
                        break;
                    }

                    $u = Stockunit::where('SKU','=', $SKU)
                        ->where('_id', 'like', '%'.$unit_id )
                        ->first();

                    if($u){

                        $ul = $u->toArray();

                        $ul['scancheckDate'] = new MongoDate();
                        $ul['createdDate'] = new MongoDate();
                        $ul['lastUpdate'] = new MongoDate();
                        $ul['action'] = $action;
                        $ul['deliverTo'] = $outlet_name;
                        $ul['deliverToId'] = $outlet_id;
                        $ul['returnTo'] = $outlet_name;
                        $ul['returnToId'] = $outlet_id;

                        $unit_id = $ul['_id'];

                        unset($ul['_id']);

                        $ul['unitId'] = $unit_id;

                        Stockunitlog::insert($ul);

                        $history = array(
                            'datetime'=>new MongoDate(),
                            'action'=>$action,
                            'price'=>$ul['productDetail']['priceRegular'],
                            'status'=>$ul['status'],
                            'outletName'=>$ul['outletName'],
                            'deliverTo'=>$outlet_name,
                            'deliverToId'=>$outlet_id
                        );

                        $u->push('history', $history);

                        $u->outletId = $outlet_id;
                        $u->outletName = $outlet_name;

                        $u->save();

                        $res = 'OK';
                        $msg = 'SKU: '.$ul['SKU'].' <br />Unit ID: '.$unit_id.' <br />Outlet: '.$ul['outletName'];

                    }else{
                        $res = 'NOK';
                        $msg = 'SKU: '.$SKU.' <br />Unit ID: '.$unit_id.' <br />NOT FOUND in this outlet';
                    }

                break;
            case 'return':
                    if(is_null($unit_id)){
                        $res = 'NOK';
                        $msg = 'SKU: '.$SKU.' <br />Unit ID: NOT FOUND';
                        break;
                    }

                    $u = Stockunit::where('SKU','=', $SKU)
                        ->where('_id', 'like', '%'.$unit_id )
                        ->first();

                    if($u){

                        $ul = $u->toArray();

                        $ul['scancheckDate'] = new MongoDate();
                        $ul['createdDate'] = new MongoDate();
                        $ul['lastUpdate'] = new MongoDate();
                        $ul['action'] = $action;
                        $ul['deliverTo'] = $outlet_name;
                        $ul['deliverToId'] = $outlet_id;
                        $ul['returnTo'] = $outlet_name;
                        $ul['returnToId'] = $outlet_id;

                        $unit_id = $ul['_id'];

                        unset($ul['_id']);

                        $ul['unitId'] = $unit_id;

                        Stockunitlog::insert($ul);

                        $history = array(
                            'datetime'=>new MongoDate(),
                            'action'=>$action,
                            'price'=>$ul['productDetail']['priceRegular'],
                            'status'=>$ul['status'],
                            'outletName'=>$ul['outletName'],
                            'returnTo'=>$outlet_name,
                            'returnToId'=>$outlet_id
                        );

                        $u->push('history', $history);

                        $u->outletId = $outlet_id;
                        $u->outletName = $outlet_name;

                        $u->save();

                        $res = 'OK';
                        $msg = 'SKU: '.$ul['SKU'].' <br />Unit ID: '.$unit_id.' <br />Outlet: '.$ul['outletName'];

                    }else{
                        $res = 'NOK';
                        $msg = 'SKU: '.$SKU.' <br />Unit ID: '.$unit_id.' <br />NOT FOUND in this outlet';
                    }

                break;
            default:
                break;
        }

        $result = array(
            'result'=>$res,
            'msg'=>$msg
        );

        return Response::json($result);
    }


    public function postScancheck()
    {
        $in = Request::input();

        $code = $in['txtin'];
        $outlet_id = $in['outlet_id'];
        $use_outlet = $in['search_outlet'];

        $res = 'OK';


        if(strripos($code, '|')){
            $code = explode('|', $code);
            $SKU = $code[0];
            $unit_id = $code[1];

                if($use_outlet == 1){
                    $u = Stockunit::where('outletId','=', $outlet_id)
                        ->where('SKU','=', $SKU)
                        ->where('_id', 'like', '%'.$unit_id )
                        ->first();
                }else{
                    $u = Stockunit::where('SKU','=', $SKU)
                        ->where('_id', 'like', '%'.$unit_id )
                        ->first();
                }

            if($u){

                $ul = $u->toArray();

                $ul['scancheckDate'] = new MongoDate();
                $ul['createdDate'] = new MongoDate();
                $ul['lastUpdate'] = new MongoDate();

                $unit_id = $ul['_id'];

                unset($ul['_id']);

                $ul['unitId'] = $unit_id;

                Stockunitlog::insert($ul);

                $history = array(
                    'datetime'=>new MongoDate(),
                    'action'=>'scan',
                    'price'=>$ul['productDetail']['priceRegular'],
                    'status'=>$ul['status'],
                    'outletName'=>$ul['outletName']
                );

                $u->push('history', $history);

                $res = 'OK';
                $msg = 'SKU: '.$ul['SKU'].' <br />Unit ID: '.$unit_id.' <br />Outlet: '.$ul['outletName'];

            }else{
                $res = 'NOK';
                $msg = 'SKU: '.$SKU.' <br />Unit ID: '.$unit_id.' <br />NOT FOUND in this outlet';
            }

        }else{
            $SKU = trim($code);

            if($use_outlet){
                $u = Stockunit::where('outletId','=',$outlet_id)
                    ->where('SKU','=',$SKU)
                    ->first();
            }else{
                $u = Stockunit::where('SKU','=',$SKU)
                    ->first();
            }


            if($u){


                $ul = $u->toArray();

                $ul['scancheckDate'] = new MongoDate();
                $ul['createdDate'] = new MongoDate();
                $ul['lastUpdate'] = new MongoDate();

                $unit_id = $ul['_id'];

                unset($ul['_id']);

                $ul['unitId'] = $unit_id;

                Stockunitlog::insert($ul);

                $history = array(
                    'datetime'=>new MongoDate(),
                    'action'=>'scan',
                    'price'=>$ul['productDetail']['priceRegular'],
                    'status'=>$ul['status'],
                    'outletName'=>$ul['outletName']
                    );

                $u->push('history', $history);

                $res = 'OK';
                $msg = 'SKU: '.$ul['SKU'].' <br />Unit ID: '.$unit_id.' <br />Outlet: '.$ul['outletName'];

            }else{
                $res = 'NOK';
                $msg = 'SKU: '.$SKU.' <br />Unit ID: '.$unit_id.' <br />NOT FOUND in this outlet';
            }

        }


        $result = array(
            'result'=>$res,
            'msg'=>$msg
        );

        return Response::json($result);
    }

    public function postUpdateinventory(){
        $in = Request::input();

        //$id = $in['upinv_id'];

        //$unitdata = array_merge(array('id'=>$id),$in);

        //$this->updateStock($unitdata);
        $in['id'] = new MongoId($in['id']);

        Commerce::updateStock($in);

        return Response::json(array('result'=>'OK:UPDATED' ));
    }

    public function postInventoryinfo(){
        $pid = Request::input('product_id');

        $p = Product::find($pid);

        foreach( Prefs::getOutlet()->OutletToArray() as $o){

            $av = Stockunit::where('outletId', $o->_id )
                    ->where('productId', new MongoId($pid) )
                    ->where('status','available')
                    ->count();

            $hd = Stockunit::where('outletId', $o->_id)
                    ->where('productId',new MongoId($pid))
                    ->where('status','hold')
                    ->count();

            $rsv = Stockunit::where('outletId', $o->_id)
                    ->where('productId',new MongoId($pid))
                    ->where('status','reserved')
                    ->count();

            $sld = Stockunit::where('outletId', $o->_id)
                    ->where('productId',new MongoId($pid))
                    ->where('status','sold')
                    ->count();

            $avail['stocks'][$o->_id]['available'] = $av;
            $avail['stocks'][$o->_id]['hold'] = $hd;
            $avail['stocks'][$o->_id]['reserved'] = $rsv;
            $avail['stocks'][$o->_id]['sold'] = $sld;
        }

        $html = View::make('partials.stockform')->with('formdata', $avail)->render();

        if($p){
            return Response::json(array('result'=>'OK:FOUND', 'html'=>$html ));
        }else{
            return Response::json(array('result'=>'ERR:NOTFOUND'));
        }


    }


    public function postProductinfo(){
        $pid = Request::input('product_id');

        $p = Product::find($pid);

        if($p){
            return Response::json(array('result'=>'OK:FOUND', 'data'=>$p->toArray() ));
        }else{
            return Response::json(array('result'=>'ERR:NOTFOUND'));
        }
    }

    public function postPrintdefault()
    {
        $in = Request::input();

        $_id = Auth::user()->id;

        $def = Printdefault::where('ownerId',$_id)->where('type',$in['code_type'])->first();

        if($def){

        }else{
            $def = new Printdefault();
            $def['ownerId'] = Auth::user()->id;
        }

        foreach($in as $k=>$v){
            $def[$k] = $v;
        }

        $def->save();

        return Response::json(array('result'=>'OK'));

    }

    public function postSessionsave($sessionname = null)
    {
        if(is_null($sessionname)){
            $sessionname = 'pr_'.time();
        }
        $in = Request::input('data_array');

        $in['_id'] = $sessionname;

        Printsession::insert($in);
        return Response::json(array('result'=>'OK', 'sessionname'=>$sessionname));
    }


    public function postProductpicture(){
        $data = Request::input();


        $defaults = array();

        $files = array();

        if( isset($data['file_id']) && count($data['file_id'])){

            $data['defaultpic'] = (isset($data['defaultpic']))?$data['defaultpic']:$data['file_id'][0];
            $data['brchead'] = (isset($data['brchead']))?$data['brchead']:$data['file_id'][0];
            $data['brc1'] = (isset($data['brc1']))?$data['brc1']:$data['file_id'][0];
            $data['brc2'] = (isset($data['brc2']))?$data['brc2']:$data['file_id'][0];
            $data['brc3'] = (isset($data['brc3']))?$data['brc3']:$data['file_id'][0];


            for($i = 0 ; $i < count($data['file_id']); $i++ ){


                $files[$data['file_id'][$i]]['thumbnail_url'] = $data['thumbnail_url'][$i];
                $files[$data['file_id'][$i]]['large_url'] = $data['large_url'][$i];
                $files[$data['file_id'][$i]]['medium_url'] = $data['medium_url'][$i];
                $files[$data['file_id'][$i]]['full_url'] = $data['full_url'][$i];

                $files[$data['file_id'][$i]]['delete_type'] = $data['delete_type'][$i];
                $files[$data['file_id'][$i]]['delete_url'] = $data['delete_url'][$i];
                $files[$data['file_id'][$i]]['filename'] = $data['filename'][$i];
                $files[$data['file_id'][$i]]['filesize'] = $data['filesize'][$i];
                $files[$data['file_id'][$i]]['temp_dir'] = $data['temp_dir'][$i];
                $files[$data['file_id'][$i]]['filetype'] = $data['filetype'][$i];
                $files[$data['file_id'][$i]]['fileurl'] = $data['fileurl'][$i];
                $files[$data['file_id'][$i]]['file_id'] = $data['file_id'][$i];
                $files[$data['file_id'][$i]]['caption'] = $data['caption'][$i];

                if($data['defaultpic'] == $data['file_id'][$i]){
                    $defaults['thumbnail_url'] = $data['thumbnail_url'][$i];
                    $defaults['large_url'] = $data['large_url'][$i];
                    $defaults['medium_url'] = $data['medium_url'][$i];
                    $defaults['full_url'] = $data['full_url'][$i];
                }

                if($data['brchead'] == $data['file_id'][$i]){
                    $defaults['brchead'] = $data['large_url'][$i];
                }

                if($data['brc1'] == $data['file_id'][$i]){
                    $defaults['brc1'] = $data['large_url'][$i];
                }

                if($data['brc2'] == $data['file_id'][$i]){
                    $defaults['brc2'] = $data['large_url'][$i];
                }

                if($data['brc3'] == $data['file_id'][$i]){
                    $defaults['brc3'] = $data['large_url'][$i];
                }


            }

        }else{

            $data['thumbnail_url'] = array();
            $data['large_url'] = array();
            $data['medium_url'] = array();
            $data['full_url'] = array();
            $data['delete_type'] = array();
            $data['delete_url'] = array();
            $data['filename'] = array();
            $data['filesize'] = array();
            $data['temp_dir'] = array();
            $data['filetype'] = array();
            $data['fileurl'] = array();
            $data['file_id'] = array();
            $data['caption'] = array();

            $data['defaultpic'] = '';
            $data['brchead'] = '';
            $data['brc1'] = '';
            $data['brc2'] = '';
            $data['brc3'] = '';
        }


        $data['defaultpictures'] = $defaults;
        $data['files'] = $files;

        $p = Product::find($data['upload_id']);

        $p->thumbnail_url =  $data['thumbnail_url'];
        $p->large_url =  $data['large_url'];
        $p->medium_url =  $data['medium_url'];
        $p->full_url =  $data['full_url'];
        $p->delete_type =  $data['delete_type'];
        $p->delete_url =  $data['delete_url'];
        $p->filename =  $data['filename'];
        $p->filesize =  $data['filesize'];
        $p->temp_dir =  $data['temp_dir'];
        $p->filetype =  $data['filetype'];
        $p->fileurl =  $data['fileurl'];
        $p->file_id =  $data['file_id'];
        $p->caption =  $data['caption'];
        $p->defaultpic =  $data['defaultpic'];
        $p->brchead =  $data['brchead'];
        $p->brc1 =  $data['brc1'];
        $p->brc2 =  $data['brc2'];
        $p->brc3 =  $data['brc3'];
        $p->defaultpictures =  $data['defaultpictures'];
        $p->files =  $data['files'];

        if($p->save()){
            return Response::json(array('result'=>'OK:UPLOADED' ));
        }else{
            return Response::json(array('result'=>'ERR:UPDATEFAILED' ));
        }

    }

    public function postAssignstatus(){
        $in = Request::input();

        $status = $in['status'];

        $product_ids = $in['product_ids'];

        foreach($product_ids as $p){
            $prop = Product::find($p);
            $prop->status = $status;
            $prop->save();
        }

        return Response::json(array('result'=>'OK'));

    }

    public function postApproval(){
        $in = Request::input();

        $ticket_id = $in['ticket_id'];
        $approval_status = $in['approval_status'];

        $approval = Approval::find($ticket_id);
        if($approval){
            $approval->approvalStatus = $approval_status;
            $approval->save();

            $p = json_encode($approval);
            $actor = (isset(Auth::user()->email))?Auth::user()->name.' - '.Auth::user()->email:'guest';

            Event::fire('log.a',array('approval','change',$actor,$p));

            return Response::json(array('result'=>'OK'));
        }else{

            $p = 'asset not found';
            $actor = (isset(Auth::user()->email))?Auth::user()->email:'guest';

            Event::fire('log.a',array('approval','change',$actor,$p));

            return Response::json(array('result'=>'ERR::NOTFOUND'));
        }


    }


    public function postAssigncat(){
        $in = Request::input();

        $category = $in['category'];

        $cats = Prefs::getProductCategory()->ProductCatToSelection('slug', 'title', false);

        $product_ids = $in['product_ids'];

        foreach($product_ids as $p){
            $prop = Product::find($p);
            $prop->category = $cats[$category];
            $prop->categoryLink = $category;
            $prop->save();
        }

        return Response::json(array('result'=>'OK'));

    }

    public function postAssignshopcat(){
        $in = Request::input();

        $category = $in['category'];

        $cats = Prefs::getShopCategory()->ShopCatToSelection('slug', 'name', false);

        $product_ids = $in['product_ids'];

        foreach($product_ids as $p){
            $prop = Member::find($p);
            $prop->shopcategory = $cats[$category];
            $prop->shopcategoryLink = $category;
            $prop->save();
        }

        return Response::json(array('result'=>'OK'));

    }

    public function postAssignoutlet(){
        $in = Request::input();

        $category = $in['outlet'];

        $cats = Prefs::getOutlet()->OutletToSelection('name','_id',false);

        //print_r($cats);

        $product_ids = $in['product_ids'];

        foreach($product_ids as $p){
            $prop = Stockunit::find($p);
            $prop->outletId = $cats[$category];
            $prop->outletName = $category;
            $prop->save();
        }

        return Response::json(array('result'=>'OK'));

    }

    public function postUnassign(){
        $in = Request::input();

        $user_id = $in['user_id'];

        $prop_ids = $in['prop_ids'];

        foreach($prop_ids as $p){
            $prop = Property::find($p);

            if($prop){
                $prop->pull('assigned_user',$user_id);
                $prop->save();
            }

        }

        return Response::json(array('result'=>'OK'));

    }


    public function getPlaylist(){
        $mc = LMongo::collection('playlist');

        $video = $mc
            ->orderBy('sequence', 'asc')
            ->get();

        $playlist = array();

        foreach($video as $v){
            $playlist[] = array('file'=>$v['url']);
        }

        return Response::json($playlist);
    }

    public function getPush(){
        $lockfile = realpath('storage/lock').'/push';
        file_put_contents($lockfile, '1');
        return Response::json(array('push'=>1));
    }

    public function getChange(){
        $lockfile = realpath('storage/lock').'/push';

        $change = file_get_contents($lockfile);

        if($change == 1){
            file_put_contents($lockfile, '2');
            return Response::json(array('push'=>1));
        }else{
            return Response::json(array('push'=>0));
        }
    }

    public function getTag()
    {
        $q = Request::input('term');

        $qtag = new MongoRegex('/'.$q.'/i');

        $res = Tag::where('tag',$qtag)->distinct('tag')->get();

        //print_r($res->toArray());

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r[0],'label'=>$r[0],'value'=>$r[0]);
        }

        return Response::json($result);
    }


    public function getProduct()
    {
        $q = Request::input('term');

        $mreg = new MongoRegex('/'.$q.'/i');

        $res = Product::where('SKU', 'regex', $mreg)
                    ->orWhere('itemDescription', 'regex', $mreg)
                    ->orWhere('series', 'regex', $mreg)
                    ->get()->toArray();

                    //print_r($res);

        $result = array();

        foreach($res as $r){
            //print_r($r);

            if(isset($r['defaultpictures']['thumbnail_url'])){
                $display = HTML::image( $r['defaultpictures']['thumbnail_url'].'?'.time(),'thumb', array('id' => $r['_id']));
            }else{
                $display = HTML::image( url('images/no-thumb.jpg').'?'.time(),'thumb', array('id' => $r['_id']));
            }

            $label = $display.' '.$r['SKU'].'-'.$r['itemDescription'];
            $result[] = array('id'=>$r['_id'],'value'=>$r['SKU'],'link'=>$r['SKU'],'pic'=>$display,'description'=>$r['itemDescription'],'label'=>$label);
        }

        return Response::json($result);
    }

    public function getProductplain()
    {
        $q = Request::input('term');

        $user = new Product();
        $qemail = new MongoRegex('/'.$q.'/i');

        $res = $user->find(array('$or'=>array(array('name'=>$qemail),array('description'=>$qemail)) ));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'value'=>$r['permalink'],'description'=>$r['description'],'label'=>$r['name']);
        }

        return Response::json($result);
    }

    public function getStatus()
    {

        $q = Request::input('term');

        $status = config('jayon.delivery_status');

        $result = array();

        foreach($status as $k=>$v) {
            if(preg_match('/'.$q.'/i', $v)) {
                $result[] = array('id'=>$k,'value'=>$k,'label'=>$k);
            }
        }

        return Response::json($result);
    }

    public function getCourierstatus()
    {

        $q = Request::input('term');

        $status = config('jayon.courier_status');

        $result = array();

        foreach($status as $k=>$v) {
            if(preg_match('/'.$q.'/i', $v)) {
                $result[] = array('id'=>$k,'value'=>$k,'label'=>$k);
            }
        }

        return Response::json( $result );
    }

    public function getPickupstatus()
    {

        $q = Request::input('term');

        $status = config('jayon.pickup_status');

        $result = array();

        foreach($status as $k=>$v) {
            if(preg_match('/'.$q.'/i', $v)) {
                $result[] = array('id'=>$k,'value'=>$k,'label'=>$k);
            }
        }

        return Response::json( $result );
    }

    public function getDistrict()
    {
        $q = Request::input('term');

        $q = '%'.$q.'%';

        $devices = Coverage::where('district','like',$q)->get();

        $result = array();

        foreach($devices as $d){
            $result[] = array('id'=>$d->id,'value'=>$d->district,'name'=>$d->district,'label'=>$d->district);
        }

        return Response::json($result);
    }

    public function getCity()
    {
        $q = Request::input('term');

        $q = '%'.$q.'%';

        $devices = Coverage::distinct('city')->where('city','like',$q)->get();

        //print_r($devices->toArray());

        $result = array();

        $city = '';
        foreach($devices as $d){
            if($city != $d->city){
                $result[] = array('id'=>$d->id,'value'=>$d->city,'name'=>$d->city,'label'=>$d->city);
                $city = $d->city;
            }
        }

        return Response::json($result);
    }

    public function getDevice()
    {
        $q = Request::input('term');

        $q = '%'.$q.'%';

        $devices = Device::where('identifier','like',$q)->get();

        $result = array();

        foreach($devices as $d){
            $result[] = array('id'=>$d->key,'value'=>$d->identifier,'dev_id'=>$d->id,'name'=>$d->identifier,'label'=>$d->identifier.' ( '.$d->key.' )');
        }

        return Response::json($result);
    }


    public function getEmail()
    {
        $q = Request::input('term');

        $user = new User();
        $qemail = new MongoRegex('/'.$q.'/i');

        $res = $user->find(array('$or'=>array(array('email'=>$qemail),array('fullname'=>$qemail)) ),array('email','fullname'));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'value'=>$r['email'],'name'=>$r['fullname'],'label'=>$r['fullname'].' ( '.$r['email'].' )');
        }

        return Response::json($result);
    }

    public function getMerchant()
    {
        $q = Request::input('term');

        $user = Merchant::where('group_id',4)
                    ->where(function($query) use($q) {
                        $query->where('merchantname','like','%'.$q.'%')
                            ->orWhere('fullname','like','%'.$q.'%');
                    })->get();

        $result = array();

        foreach($user->toArray() as $r){
            $result[] = array('id'=>$r['id'],'value'=>$r['id'],'email'=>$r['email'],'label'=>$r['merchantname'] );
        }

        return Response::json($result);
    }

    public function postMerchantapp()
    {
        $q = Request::input('term');

        $user = Application::where('merchant_id','=',$q)->get();

        $result = array();

        foreach($user->toArray() as $r){
            $result[] = '<option value="'.$r['id'].'">'.$r['application_name'].'</option>';
        }

        print implode('',$result);
    }

    public function getUser()
    {
        $q = Request::input('term');

        $user = new User();
        $qemail = new MongoRegex('/'.$q.'/i');

        $res = $user->find(array('$or'=>array(array('email'=>$qemail),array('fullname'=>$qemail)) ),array('email','fullname'));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'value'=>$r['fullname'],'email'=>$r['email'],'label'=>$r['fullname'].' ( '.$r['email'].' )');
        }

        return Response::json($result);
    }

    public function getGroup()
    {
        $q = Request::input('term');

        $user = new Group();
        $qemail = new MongoRegex('/'.$q.'/i');

        $res = $user->find(array('$or'=>array(array('email'=>$qemail),array('firstname'=>$qemail),array('lastname'=>$qemail)) ));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'value'=>$r['groupname'],'email'=>$r['email'],'label'=>$r['groupname'].'<br />'.$r['firstname'].''.$r['lastname'].' ( '.$r['email'].' )<br />'.$r['company']);
        }

        return Response::json($result);
    }

    public function getUserdata()
    {
        $q = Request::input('term');

        $user = new User();
        $qemail = new MongoRegex('/'.$q.'/i');

        $res = $user->find(array('$or'=>array(array('email'=>$qemail),array('fullname'=>$qemail)) ));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'value'=>$r['fullname'],'email'=>$r['email'],'label'=>$r['fullname'].' ( '.$r['email'].' )','userdata'=>$r);
        }

        return Response::json($result);
    }

    public function getUserdatabyemail()
    {
        $q = Request::input('term');

        $user = LMongo::collection('users');

        $qemail = new MongoRegex('/'.$q.'/i');



        $res = $user->whereRegex('username',$qemail)->orWhereRegex('fullname',$qemail)->get();

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'value'=>$r['username'],'email'=>$r['username'],'label'=>$r['fullname'].' ( '.$r['username'].' )','userdata'=>$r);
        }

        return Response::json($result);
    }

    public function getUserdatabyname()
    {
        $q = Request::input('term');

        $user = LMongo::collection('users');

        $qemail = new MongoRegex('/'.$q.'/i');



        $res = $user->whereRegex('username',$qemail)->orWhereRegex('fullname',$qemail)->get();

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'value'=>$r['fullname'],'email'=>$r['username'],'label'=>$r['fullname'].' ( '.$r['username'].' )','userdata'=>$r);
        }

        return Response::json($result);
    }

    public function getUseridbyemail()
    {
        $q = Request::input('term');

        $user = new User();
        $qemail = new MongoRegex('/'.$q.'/i');

        $res = $user->find(array('$or'=>array(array('email'=>$qemail),array('fullname'=>$qemail)) ));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'value'=>$r['_id']->__toString(),'email'=>$r['email'],'label'=>$r['fullname'].' ( '.$r['email'].' )');
        }

        return Response::json($result);
    }

    public function getRev()
    {
        $q = Request::input('term');

        $doc = new Document();
        $qdoc = new MongoRegex('/'.$q.'/i');

        $res = $doc->find(array('title'=>$qdoc),array('title'));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'label'=>$r['title'],'value'=>$r['_id']->__toString());
        }

        return Response::json($result);
    }

    public function getProject()
    {
        $q = Request::input('term');

        $proj = new Project();
        $qproj = new MongoRegex('/'.$q.'/i');

        $res = $proj->find(array('$or'=>array(array('title'=>$qproj),array('projectNumber'=>$qproj)) ),array('title','projectNumber'));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'label'=>$r['projectNumber'].' - '.$r['title'],'title'=>$r['title'],'value'=>$r['projectNumber']);
        }

        return Response::json($result);
    }

    public function getProjectname()
    {
        $q = Request::input('term');

        $proj = new Project();
        $qproj = new MongoRegex('/'.$q.'/i');

        $res = $proj->find(array('$or'=>array(array('title'=>$qproj),array('projectNumber'=>$qproj)) ),array('title','projectNumber'));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'label'=>$r['projectNumber'].' - '.$r['title'],'number'=>$r['projectNumber'],'value'=>$r['title']);
        }

        return Response::json($result);
    }


    public function getTender()
    {
        $q = Request::input('term');

        $proj = new Tender();
        $qproj = new MongoRegex('/'.$q.'/i');

        $res = $proj->find(array('$or'=>array(array('title'=>$qproj),array('tenderNumber'=>$qproj)) ),array('title','tenderNumber'));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'label'=>$r['tenderNumber'].' - '.$r['title'],'title'=>$r['title'],'value'=>$r['tenderNumber']);
        }

        return Response::json($result);
    }

    public function getTendername()
    {
        $q = Request::input('term');

        $proj = new Tender();
        $qproj = new MongoRegex('/'.$q.'/i');

        $res = $proj->find(array('$or'=>array(array('title'=>$qproj),array('tenderNumber'=>$qproj)) ),array('title','tenderNumber'));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'label'=>$r['tenderNumber'].' - '.$r['title'],'number'=>$r['tenderNumber'],'value'=>$r['title']);
        }

        return Response::json($result);
    }

    public function getOpportunity()
    {
        $q = Request::input('term');

        $proj = new Opportunity();
        $qproj = new MongoRegex('/'.$q.'/i');

        $res = $proj->find(array('$or'=>array(array('title'=>$qproj),array('opportunityNumber'=>$qproj)) ),array('title','opportunityNumber'));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'label'=>$r['opportunityNumber'].' - '.$r['title'],'title'=>$r['title'],'value'=>$r['opportunityNumber']);
        }

        return Response::json($result);
    }

    public function getOpportunityname()
    {
        $q = Request::input('term');

        $proj = new Opportunity();
        $qproj = new MongoRegex('/'.$q.'/i');

        $res = $proj->find(array('$or'=>array(array('title'=>$qproj),array('opportunityNumber'=>$qproj)) ),array('title','opportunityNumber'));

        $result = array();

        foreach($res as $r){
            $result[] = array('id'=>$r['_id']->__toString(),'label'=>$r['opportunityNumber'].' - '.$r['title'],'number'=>$r['opportunityNumber'],'value'=>$r['title']);
        }

        return Response::json($result);
    }

    public function getMeta()
    {
        $q = Request::input('term');

        $doc = new Document();
        $id = new MongoId($q);

        $res = $doc->get(array('_id'=>$id));

        return Response::json($result);
    }

    public function postParam()
    {
        $in = Request::input();

        $key = $in['key'];
        $value = $in['value'];

        if(setparam($key,$value)){
            return Response::json(array('result'=>'OK'));
        }else{
            return Response::json(array('result'=>'ERR'));
        }

    }

    public function fromCamelCase($camelCaseString) {
            $re = '/(?<=[a-z])(?=[A-Z])/x';
            $a = preg_split($re, $camelCaseString);
            return join($a, " " );
    }

}

