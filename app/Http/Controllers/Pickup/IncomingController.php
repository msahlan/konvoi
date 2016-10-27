<?php
namespace App\Http\Controllers\Pickup;

use App\Http\Controllers\AdminController;

use App\Models\Pickup;
use App\Models\Deliveryfee;
use App\Models\Codsurcharge;
use App\Models\Printsession;
use App\Models\Application;
use App\Models\Buyer;
use App\Models\Member;
use App\Models\History;
use App\Models\Shipmentlog;
use App\Models\Device;

use App\Helpers\Prefs;

use Creitive\Breadcrumbs\Breadcrumbs;

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
use DB;
use HTML;
use Route;

class IncomingController extends AdminController {

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



    public function __construct()
    {
        parent::__construct();

        $cname = substr(strrchr(get_class($this), '\\'), 1);
        $this->controller_name = str_replace('Controller', '', $cname);

        $this->model = new Pickup();
        //$this->model = DB::collection('documents');
        $this->title = 'Payment Pickup Order';

    }

    public function getIndex()
    {


        $this->heads = config('jc.default_incoming_heads');

        //print $this->model->where('docFormat','picture')->get()->toJSON();

        $route = Route::current();

        $this->title = 'Payment Pickup Order';

        $this->place_action = 'first';

        $this->show_select = true;

        $this->crumb->addCrumb('Pickup Order',url( strtolower($this->controller_name) ));

        /*
        $this->additional_filter = View::make(strtolower($this->controller_name).'.addfilter')
                        ->with('submit_url','gl')
                        ->with('ajaxawbdlxl','incoming/awbdlxl')
                        ->with('importawburl','incoming/importawb')
                        ->render();
        */

        $this->additional_filter .= View::make('shared.generate')->render();
        $this->additional_filter .= View::make('shared.deviceassign')
            ->with('ajaxdeviceurl',$route->getPrefix().'/'.strtolower($this->controller_name).'/shipmentlist')->render();

        //$this->additional_filter .= View::make('shared.cancelaction')->render();

        //$this->additional_filter .= View::make('shared.confirmaction')->render();

        //$this->additional_filter .= '<br />';
        //$this->additional_filter .= View::make('shared.markaction')->render();

        //$this->js_additional_param = "aoData.push( { 'name':'acc-period-to', 'value': $('#acc-period-to').val() }, { 'name':'acc-period-from', 'value': $('#acc-period-from').val() }, { 'name':'acc-code-from', 'value': $('#acc-code-from').val() }, { 'name':'acc-code-to', 'value': $('#acc-code-to').val() }, { 'name':'acc-company', 'value': $('#acc-company').val() } );";

        $this->can_import = false;
        $this->can_add = false;
        return parent::getIndex();

    }

    public function postIndex()
    {

        $this->fields = config('jc.default_incoming_fields');

        /*
        $categoryFilter = Request::input('categoryFilter');
        if($categoryFilter != ''){
            $this->additional_query = array('shopcategoryLink'=>$categoryFilter, 'group_id'=>4);
        }
        */

        $db = config('jayon.main_db');

        $this->def_order_by = 'createdDate';
        $this->def_order_dir = 'desc';
        $this->place_action = 'first';
        $this->show_select = true;

        $this->sql_key = 'delivery_id';
        $this->sql_table_name = config('jayon.incoming_delivery_table');
        $this->sql_connection = 'mysql';

        return parent::tableResponder();
    }

    public function postAssigndate(){

        $in = Request::input();
        $results = Shipment::whereIn('delivery_id', $in['ids'])->get();

        date_default_timezone_set('Asia/Jakarta');

        if(is_null($in['trip']) || $in['trip'] == ''){
            $trip = 1;
        }else{
            $trip =  $in['trip'];
        }

        $assignment_date = $in['date'];


        $ts = new MongoDate();
        //print_r($results->toArray());
            $reason = (isset($in['reason']))?$in['reason']:'initial';
        //if($results){
            $res = false;
        //}else{
            foreach($results as $r){

                $pre = clone $r;


                /*
                if(is_null($in['date']) || $in['date'] == ''){

                }else{
                    //$newdate = strtotime($in['date']);
                    //$r->pick_up_date = new MongoDate($newdate) ;
                }
                */

                $r->buyerdeliveryslot = $trip;

                $r->status = config('jayon.trans_status_admin_dated');
                $r->assigntime = date('Y-m-d H:i:s',time());
                $r->assignment_date = $assignment_date;


                /*
                if($r->logistic_type == 'internal'){
                    if($r->cod == 0 || $r->cod == ''){
                        $r->awb = $r->delivery_id;
                        $r->bucket = config('jayon.bucket_dispatcher');
                        $r->status = config('jayon.trans_status_admin_dated');
                    }
                }else{
                    if($r->awb != ''){
                        $r->bucket = config('jayon.bucket_tracker');
                        $r->status = config('jayon.trans_status_admin_dated');
                    }
                }
                */

                $r->save();

                $hdata = array();
                $hdata['historyTimestamp'] = $ts;
                $hdata['historyAction'] = 'assign_date';
                $hdata['historySequence'] = 1;
                $hdata['historyObjectType'] = 'shipment';
                $hdata['historyObject'] = $r->toArray();
                $hdata['actor'] = Auth::user()->name;
                $hdata['actor_id'] = (isset(Auth::user()->_id))?Auth::user()->_id:Auth::user()->id;

                History::insert($hdata);

                $sdata = array();
                $sdata['timestamp'] = $ts;
                $sdata['action'] = 'assign_date';
                $sdata['reason'] = $reason;
                $sdata['objectType'] = 'shipment';
                $sdata['object'] = $r->toArray();
                $sdata['preObject'] = $pre->toArray();
                $sdata['actor'] = Auth::user()->name;
                $sdata['actor_id'] = (isset(Auth::user()->_id))?Auth::user()->_id:Auth::user()->id;
                Shipmentlog::insert($sdata);


            }
            $res = true;
        //}

        if($res){
            return Response::json(array('result'=>'OK' ));
        }else{
            return Response::json(array('result'=>'ERR:MOVEFAILED' ));
        }

    }


    public function getStatic()
    {

        $this->heads = config('jex.default_heads');

        //print $this->model->where('docFormat','picture')->get()->toJSON();

        $this->title = 'Incoming Order';


        $this->crumb->addCrumb('Cost Report',url( strtolower($this->controller_name) ));

        //$this->additional_filter = View::make(strtolower($this->controller_name).'.addfilter')->with('submit_url','gl/static')->render();

        //$this->js_additional_param = "aoData.push( { 'name':'acc-period-to', 'value': $('#acc-period-to').val() }, { 'name':'acc-period-from', 'value': $('#acc-period-from').val() }, { 'name':'acc-code-from', 'value': $('#acc-code-from').val() }, { 'name':'acc-code-to', 'value': $('#acc-code-to').val() }, { 'name':'acc-company', 'value': $('#acc-company').val() } );";

        $this->product_info_url = strtolower($this->controller_name).'/info';

        $this->printlink = strtolower($this->controller_name).'/print';

        //table generator part

        $this->fields = config('jex.default_fields');

        $db = config('jayon.main_db');

        $this->def_order_by = 'ordertime';
        $this->def_order_dir = 'desc';
        $this->place_action = 'none';
        $this->show_select = false;

        $this->sql_key = 'delivery_id';
        $this->sql_table_name = config('jayon.incoming_delivery_table');
        $this->sql_connection = 'mysql';

        $this->responder_type = 's';

        return parent::printGenerator();
    }

    public function getPrint()
    {

        $this->fields = config('jex.default_heads');

        //print $this->model->where('docFormat','picture')->get()->toJSON();

        $this->title = 'Incoming Order';

        $this->crumb->addCrumb('Cost Report',url( strtolower($this->controller_name) ));

        //$this->additional_filter = View::make(strtolower($this->controller_name).'.addfilter')->with('submit_url','gl/static')->render();

        //$this->js_additional_param = "aoData.push( { 'name':'acc-period-to', 'value': $('#acc-period-to').val() }, { 'name':'acc-period-from', 'value': $('#acc-period-from').val() }, { 'name':'acc-code-from', 'value': $('#acc-code-from').val() }, { 'name':'acc-code-to', 'value': $('#acc-code-to').val() }, { 'name':'acc-company', 'value': $('#acc-company').val() } );";

        $this->product_info_url = strtolower($this->controller_name).'/info';

        $this->printlink = strtolower($this->controller_name).'/print';

        //table generator part

        $this->fields = config('jex.default_fields');

        $db = config('jayon.main_db');

        $this->def_order_by = 'ordertime';
        $this->def_order_dir = 'desc';
        $this->place_action = 'none';
        $this->show_select = false;

        $this->sql_key = 'delivery_id';
        $this->sql_table_name = config('jayon.incoming_delivery_table');
        $this->sql_connection = 'mysql';

        $this->responder_type = 's';

        return parent::printPage();
    }

    public function SQL_make_join($model)
    {
        //$model->with('coa');

        //PERIOD',TRANS_DATETIME,VCHR_NUM,ACC_DESCR,DESCRIPTN',TREFERENCE',CONV_CODE,AMOUNT',AMOUNT',DESCRIPTN'
        /*
        $model = $model->select('j10_a_salfldg.*','j10_acnt.DESCR as ACC_DESCR')
            ->leftJoin('j10_acnt', 'j10_a_salfldg.ACCNT_CODE', '=', 'j10_acnt.ACNT_CODE' );
            */
        return $model;
    }

    public function SQL_additional_query($model)
    {
        $model = $model->where('status','=',config('jayon.trans_status_new'))
                    ->orderBy('assignmentDate','desc')
                    ->orderBy('pickupCity','desc')
                    ->orderBy('pickupDistrict','desc');

        return $model;

    }

    public function SQL_before_paging($model)
    {
        /*
        $m_original_amount = clone($model);
        $m_base_amount = clone($model);

        $aux['total_data_base'] = $m_base_amount->sum('OTHER_AMT');
        $aux['total_data_converted'] = $m_original_amount->sum('AMOUNT');
        */
        //$this->aux_data = $aux;

        $aux = array();
        return $aux;
        //print_r($this->aux_data);

    }

    public function postShipmentlist()
    {
        $in = Request::input();

        $city = $in['city'];

        $district = $in['district'];

        $date = $in['date'];

        $this->fields = config('jc.default_incoming_fields');

        $fields = $this->fields; // fields set must align with search column index

        $search_fields = (is_null($this->search_fields))?$this->fields:$this->search_fields;

        $infilters = Request::input('filter');
        $insorting = Request::input('sort');

        $defsort = 1;
        $defdir = -1;

        $idx = 0;
        $q = array();

        $hilite = array();
        $hilite_replace = array();

        $colheads = array();
        $coltitles = array();

        //exit();
        $model = new Pickup();

        array_shift($infilters);
        if($this->place_action == 'both' || $this->place_action == 'first'){
            array_shift($infilters);
        }

        $comres = $this->DLcompileSearch($search_fields, $model,$infilters);

        $model = $comres['model'];
        $q = $comres['q'];
        $searchpar = $comres['in'];


        //$pick_up_date = new MongoDate(strtotime($date));

        $pick_up_date = date('Y-m-d 00:00:00', strtotime($date));

        //print $pick_up_date;

        $shipments = $model->where('assignmentDate','=', $pick_up_date )
                        ->where('status','=', config('jayon.trans_status_new'))
                        ->where('pickupCity','=',$city)
                        ->where('pickupDistrict','=',$district)
                        ->get();

        $shipments = $shipments->toArray();

        //print_r($shipments);

        for($i = 0; $i < count($shipments); $i++){
            $shipments[$i]['assignmentDate'] = date('Y-m-d', strtotime($shipments[$i]['assignmentDate']) );
            //$shipments[$i]['assignmentdate'] = date('Y-m-d', $shipments[$i]['assignmentdate']->sec );
        }

        $city = trim($city);
        /*
        $devices = Device::where('city','regex', new MongoRegex('/'.$city.'/i'))
                                ->where(function($on){
                                        $on->where('is_on','=',1)
                                            ->orWhere('is_on','=',strval(1));
                                })
                                ->get();
        */
        $devices = Device::where('city','like', '%'.$city.'%')
                                ->where(function($on){
                                        $on->where('is_on','=',1)
                                            ->orWhere('is_on','=',strval(1));
                                })
                                ->get();

        $caps = array();

        $dids = array();
        foreach($devices as $d){
            $dids[] = $d->id;
        }

        $qloads = Pickup::select('deviceName')
                    ->where('assignmentDate',$pick_up_date)
                    ->where('deviceKey','!=','')
                    //->groupBy('deviceName')
                    ->get();

        $loads = array();

        foreach($qloads as $ld){
            if(isset($loads[$ld->deviceKey])){
                $loads[$ld->deviceKey] += 1;
            }else{
                $loads[$ld->deviceKey] = 1;
            }
        }



        foreach($devices as $d){
            $caps[$d->key]['identifier'] = $d->identifier;
            $caps[$d->key]['id'] = $d->id;
            $caps[$d->key]['key'] = $d->key;
            $caps[$d->key]['city'] = $d->city;
            $caps[$d->key]['count'] = (isset( $loads[$d->id] ))?$loads[$d->id]:0;

        }

        return Response::json( array('result'=>'OK', 'shipment'=>$shipments, 'device'=>$caps ) );
        //print_r($caps);

    }

    public function postAssigndevice()
    {
        $in = Request::input();

        //better use device key to alleviate mysql dependency
        $device = Device::where('key','=', $in['device'] )->first();

        $shipments = Pickup::whereIn('transactionId', $in['ship_ids'] )->get();

        //print_r($device);
        //print_r($shipments->toArray());

        $ts = new MongoDate();

        foreach($shipments as $sh){

            $pre = clone $sh;


            //$sh->status = Config::get('jayon.trans_status_admin_zoned');
            $sh->status = config('jayon.trans_status_admin_devassigned');
            $sh->deviceKey = $device->key;
            $sh->deviceName = $device->identifier;
            $sh->deviceId = $device->id;
            $sh->save();


            $hdata = array();
            $hdata['historyTimestamp'] = $ts;
            $hdata['historyAction'] = 'assign_device';
            $hdata['historySequence'] = 1;
            $hdata['historyObjectType'] = 'shipment';
            $hdata['actor'] = Auth::user()->name;
            $hdata['actor_id'] = Auth::user()->_id;

            $hdata = array_merge($sh->toArray(), $hdata );

            //History::insert($hdata);

            $sdata = array();
            $sdata['timestamp'] = $ts;
            $sdata['action'] = 'assign_device';
            $sdata['reason'] = 'initial';
            $sdata['objectType'] = 'shipment';
            $sdata['object'] = $sh->toArray();
            $sdata['preObject'] = $pre->toArray();
            $sdata['actor'] = Auth::user()->name;
            $sdata['actor_id'] = Auth::user()->_id;
            //Shipmentlog::insert($sdata);

        }

        return Response::json( array('result'=>'OK', 'shipment'=>$shipments ) );

    }


    public function rows_post_process($rows, $aux = null){

        $date = '';
        $city = '';
        $zone = '';

        //print_r($rows);

        if(count($rows) > 0){

            for($i = 0; $i < count($rows); $i++){
                if($rows[$i][3] != $date){
                    $city = '';
                    $date = $rows[$i][3];
                    $rows[$i][3] = '<input type="radio" name="date_select" value="'.$rows[$i][3].'" class="date_select form-control" /> '.$rows[$i][3];
                }else{
                    $rows[$i][3] = '';
                }


                if($rows[$i][4] != $city){
                    $city = $rows[$i][4];
                    $rows[$i][4] = '<input type="radio" name="city_select" value="'.$rows[$i][4].'" class="city_select form-control" /> '.$rows[$i][4];
                }else{
                    $rows[$i][4] = '';
                }

                if($rows[$i][5] != $zone){
                    $zone = $rows[$i][5];
                    $rows[$i][5] = '<input type="radio" name="zone_select" value="'.$rows[$i][5].'" class="zone_select form-control" /> '.$rows[$i][5];
                }else{
                    $rows[$i][5] = '';
                }

            }


        }

        return $rows;

    }


    public function beforeSave($data)
    {

        if( isset($data['file_id']) && count($data['file_id'])){

            $mediaindex = 0;

            for($i = 0 ; $i < count($data['thumbnail_url']);$i++ ){

                $index = $mediaindex;

                $data['files'][ $data['file_id'][$i] ]['ns'] = $data['ns'][$i];
                $data['files'][ $data['file_id'][$i] ]['role'] = $data['role'][$i];
                $data['files'][ $data['file_id'][$i] ]['thumbnail_url'] = $data['thumbnail_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['large_url'] = $data['large_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['medium_url'] = $data['medium_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['full_url'] = $data['full_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_type'] = $data['delete_type'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_url'] = $data['delete_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['filename'] = $data['filename'][$i];
                $data['files'][ $data['file_id'][$i] ]['filesize'] = $data['filesize'][$i];
                $data['files'][ $data['file_id'][$i] ]['temp_dir'] = $data['temp_dir'][$i];
                $data['files'][ $data['file_id'][$i] ]['filetype'] = $data['filetype'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_image'] = $data['is_image'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_audio'] = $data['is_audio'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_video'] = $data['is_video'][$i];
                $data['files'][ $data['file_id'][$i] ]['fileurl'] = $data['fileurl'][$i];
                $data['files'][ $data['file_id'][$i] ]['file_id'] = $data['file_id'][$i];
                $data['files'][ $data['file_id'][$i] ]['sequence'] = $mediaindex;

                $mediaindex++;

                $data['defaultpic'] = $data['file_id'][0];
                $data['defaultpictures'] = $data['files'][$data['file_id'][0]];

            }

        }else{

            $data['defaultpic'] = '';
            $data['defaultpictures'] = '';
        }

        $cats = Prefs::getShopCategory()->shopcatToSelection('slug', 'name', false);
        $data['shopcategory'] = $cats[$data['shopcategoryLink']];

            $data['shortcode'] = str_random(5);

        return $data;
    }

    public function beforeUpdate($id,$data)
    {

        if( isset($data['file_id']) && count($data['file_id'])){

            $mediaindex = 0;

            for($i = 0 ; $i < count($data['thumbnail_url']);$i++ ){

                $index = $mediaindex;

                $data['files'][ $data['file_id'][$i] ]['ns'] = $data['ns'][$i];
                $data['files'][ $data['file_id'][$i] ]['role'] = $data['role'][$i];
                $data['files'][ $data['file_id'][$i] ]['thumbnail_url'] = $data['thumbnail_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['large_url'] = $data['large_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['medium_url'] = $data['medium_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['full_url'] = $data['full_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_type'] = $data['delete_type'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_url'] = $data['delete_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['filename'] = $data['filename'][$i];
                $data['files'][ $data['file_id'][$i] ]['filesize'] = $data['filesize'][$i];
                $data['files'][ $data['file_id'][$i] ]['temp_dir'] = $data['temp_dir'][$i];
                $data['files'][ $data['file_id'][$i] ]['filetype'] = $data['filetype'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_image'] = $data['is_image'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_audio'] = $data['is_audio'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_video'] = $data['is_video'][$i];
                $data['files'][ $data['file_id'][$i] ]['fileurl'] = $data['fileurl'][$i];
                $data['files'][ $data['file_id'][$i] ]['file_id'] = $data['file_id'][$i];
                $data['files'][ $data['file_id'][$i] ]['sequence'] = $mediaindex;

                $mediaindex++;

                $data['defaultpic'] = $data['file_id'][0];
                $data['defaultpictures'] = $data['files'][$data['file_id'][0]];

            }

        }else{

            $data['defaultpic'] = '';
            $data['defaultpictures'] = '';
        }

        if(!isset($data['shortcode']) || $data['shortcode'] == ''){
            $data['shortcode'] = str_random(5);
        }

        $cats = Prefs::getShopCategory()->shopcatToSelection('slug', 'name', false);
        $data['shopcategory'] = $cats[$data['shopcategoryLink']];


        return $data;
    }

    public function beforeUpdateForm($population)
    {
        //print_r($population);
        //exit();

        return $population;
    }

    public function afterSave($data)
    {

        $hdata = array();
        $hdata['historyTimestamp'] = new MongoDate();
        $hdata['historyAction'] = 'new';
        $hdata['historySequence'] = 0;
        $hdata['historyObjectType'] = 'asset';
        $hdata['historyObject'] = $data;
        History::insert($hdata);

        return $data;
    }

    public function afterUpdate($id,$data = null)
    {
        $data['_id'] = new MongoId($id);


        $hdata = array();
        $hdata['historyTimestamp'] = new MongoDate();
        $hdata['historyAction'] = 'update';
        $hdata['historySequence'] = 1;
        $hdata['historyObjectType'] = 'asset';
        $hdata['historyObject'] = $data;
        History::insert($hdata);


        return $id;
    }

    public function makeActions($data){

        if(isset($data['_id']) && $data['_id'] instanceOf MongoId){
            $id = $data['_id'];
        }else{
            $id = (isset($data['id']))?$data['id']:'0';
        }

        $printslip = '<span class="printslip action" type"button" data-rel="tooltip" data-toggle="tooltip" data-placement="left" title="" data-original-title="Print Slip" id="'.$id.'" ><i class="fa fa-print"></i> Print Slip</span>';

        $detailview = '<span class="detailview action" type"button" data-rel="tooltip" data-toggle="tooltip" data-placement="left" title="" data-original-title="Order Detail" id="'.$id.'" ><i class="fa fa-eye"></i> View Order</span>';

        $delete = '<span class="del" type"button" data-rel="tooltip" data-toggle="tooltip" data-placement="left" title="" data-original-title="Delete item" id="'.$id.'" ><i class="fa fa-trash"></i> Del</span>';

        $edit = '<a href="'.url( strtolower($this->controller_name).'/edit/'.$id).'" type"button" data-rel="tooltip" data-toggle="tooltip" data-placement="left" title="" data-original-title="Edit item" ><i class="fa fa-edit"></i> Edit</a>';
        //$actions = $edit.'<br />'.$delete;

        $actions = $printslip.'<br />'.$detailview;

        return $actions;
    }



    public function postAdd($data = null)
    {
        $this->validator = array(
            'shopDescription' => 'required'
        );

        return parent::postAdd($data);
    }

    public function postEdit($id,$data = null)
    {
        $this->validator = array(
            'shopDescription' => 'required'
        );

        //exit();

        return parent::postEdit($id,$data);
    }

    public function postDlxl()
    {

        $this->heads = null;

        $this->fields = config('jex.default_incoming_fields');

        $db = config('jayon.main_db');

        $this->def_order_by = 'ordertime';
        $this->def_order_dir = 'desc';
        $this->place_action = 'first';
        $this->show_select = true;

        $this->sql_key = 'delivery_id';
        $this->sql_table_name = config('jayon.incoming_delivery_table');
        $this->sql_connection = 'mysql';

        return parent::postSQLDlxl();
    }

    public function getImport(){

        $this->importkey = 'delivery_id';

        $this->import_aux_form = View::make(strtolower($this->controller_name).'.importauxform')->render();

        return parent::getImport();
    }

    public function postUploadimport()
    {
        $this->importkey = 'delivery_id';

        return parent::postUploadimport();
    }

    public function processImportAuxForm()
    {

        return array(
            'merchant_id'=>Request::input('merchant_id'),
            'merchant_name'=>Request::input('merchant_name'),
            'app_id'=>Request::input('merchant_app'),
         );
    }

    public function beforeImportCommit($data)
    {

        unset($data['createdDate']);
        unset($data['lastUpdate']);

        $data['created'] = $data['created_at'];

        unset($data['created_at']);
        unset($data['updated_at']);

        unset($data['volume']);
        unset($data['sessId']);
        unset($data['isHead']);


            $merchant_id = $data['merchant_id'];
            $merchant_name = $data['merchant_name'];
            $app_id = $data['app_id'];

            $app = Application::find($app_id);

            $app_key = $app->key;

            $data['delivery_type'] = ($data['delivery_type'] == 'DO')?'Delivery Only':$data['delivery_type'];
            $data['actual_weight'] = $data['weight'];
            $data['weight'] = Prefs::get_weight_tariff($data['weight'], $data['delivery_type'] ,$app_id, date('Y-m-d',time()));

            $trx_detail = array();
            $trx_detail[0]['unit_description'] = $data['package_description'];
            $trx_detail[0]['unit_price'] = $data['total_price'];
            $trx_detail[0]['unit_quantity'] = 1;
            $trx_detail[0]['unit_total'] = $data['total_price'] ;
            $trx_detail[0]['unit_discount'] = 0;

            unset($data['package_description']);
            unset($data['no']);

            if(isset($data['direction']) && $data['direction'] != '')
            {
                if(isset($data['directions']) && $data['directions'] != ''){

                }else{
                    $data['directions'] = $data['direction'];
                }
            }

            unset($data['direction']);

            $order = $this->ordermap;

            foreach($data as $k=>$v){
                if(in_array($k, array_keys($order))){
                    $order[$k] = $v;
                }
            }

            $order['zip'] = '-';

            $order['merchant_id'] = $merchant_id;
            $order['application_id'] = $app_id;
            $order['application_key'] = $app_key;
            $order['trx_detail'] = $trx_detail;

            unset($order['merchant_name']);

            //print_r($order);

            $trx_id = 'TRX_'.$merchant_id.'_'. substr( str_replace(array(' ','.'), '', microtime()) , 0, 5) ;

            $trx = json_encode($order);
            $result = $this->order_save($trx,$app_key,$trx_id);

        return false;
    }


    public function accountDesc($data)
    {

        return $data['ACCNT_CODE'];
    }

    public function extractCategory()
    {
        $category = Product::distinct('category')->get()->toArray();
        $cats = array(''=>'All');

        //print_r($category);
        foreach($category as $cat){
            $cats[$cat[0]] = $cat[0];
        }

        return $cats;
    }

    public function splitTag($data){
        $tags = explode(',',$data['tags']);
        if(is_array($tags) && count($tags) > 0 && $data['tags'] != ''){
            $ts = array();
            foreach($tags as $t){
                $ts[] = '<span class="tag">'.$t.'</span>';
            }

            return implode('', $ts);
        }else{
            return $data['tags'];
        }
    }

    public function splitShare($data){
        $tags = explode(',',$data['docShare']);
        if(is_array($tags) && count($tags) > 0 && $data['docShare'] != ''){
            $ts = array();
            foreach($tags as $t){
                $ts[] = '<span class="tag">'.$t.'</span>';
            }

            return implode('', $ts);
        }else{
            return $data['docShare'];
        }
    }

    public function locationName($data){
        if(isset($data['locationId']) && $data['locationId'] != ''){
            $loc = Assets::getLocationDetail($data['locationId']);
            return $loc->name;
        }else{
            return '';
        }

    }

    public function merchantInfo($data)
    {
        return $data['merchant_name'].'<hr />'.$data['app_name'];
    }

    public function catName($data)
    {
        return $data['shopcategory'];
    }

    public function rackName($data){
        if(isset($data['rackId']) && $data['rackId'] != ''){
            $loc = Assets::getRackDetail($data['rackId']);
            if($loc){
                return $loc->SKU;
            }else{
                return '';
            }
        }else{
            return '';
        }

    }

    public function postSynclegacy(){

        set_time_limit(0);

        $mymerchant = Merchant::where('group_id',4)->get();

        $count = 0;

        foreach($mymerchant->toArray() as $m){

            $member = Member::where('legacyId',$m['id'])->first();

            if($member){

            }else{
                $member = new Member();
            }

            foreach ($m as $k=>$v) {
                $member->{$k} = $v;
            }

            if(!isset($member->status)){
                $member->status = 'inactive';
            }

            if(!isset($member->url)){
                $member->url = '';
            }

            $member->legacyId = new MongoInt32($m['id']);

            $member->roleId = Prefs::getRoleId('Merchant');

            $member->unset('id');

            $member->save();

            $count++;
        }

        return Response::json( array('result'=>'OK', 'count'=>$count ) );

    }

    public function statNumbers($data){
        $datemonth = date('M Y',time());
        $firstday = Carbon::parse('first day of '.$datemonth);
        $lastday = Carbon::parse('last day of '.$datemonth)->addHours(23)->addMinutes(59)->addSeconds(59);

        $qval = array('$gte'=>new MongoDate(strtotime($firstday->toDateTimeString())),'$lte'=>new MongoDate( strtotime($lastday->toDateTimeString()) ));

        $qc = array();

        $qc['adId'] = $data['_id'];

        $qc['clickedAt'] = $qval;

        $qv = array();

        $qv['adId'] = $data['_id'];

        $qv['viewedAt'] = $qval;

        $clicks = Clicklog::whereRaw($qc)->count();

        $views = Viewlog::whereRaw($qv)->count();

        return $clicks.' clicks<br />'.$views.' views';
    }

    public function namePic($data)
    {
        $name = url('property/view/'.$data['_id'],$data['address']);

        $thumbnail_url = '';

        $ps = config('picture.sizes');


        if(isset($data['files']) && count($data['files'])){
            $glinks = '';

            $gdata = $data['files'][$data['defaultpic']];

            $thumbnail_url = $gdata['thumbnail_url'];
            foreach($data['files'] as $g){
                $g['caption'] = ( isset($g['caption']) && $g['caption'] != '')?$g['caption']:$data['SKU'];
                $g['full_url'] = isset($g['full_url'])?$g['full_url']:$g['fileurl'];
                foreach($ps as $k=>$s){
                    if(isset($g[$k.'_url'])){
                        $glinks .= '<input type="hidden" class="g_'.$data['_id'].'" data-caption="'.$k.'" value="'.$g[$k.'_url'].'" />';
                    }
                }
            }
            if(isset($data['useImage']) && $data['useImage'] == 'linked'){
                $thumbnail_url = $data['extImageURL'];
                $display = link_to_asset($thumbnail_url.'?'.time(), $thumbnail_url, array('class'=>'thumbnail img-polaroid','style'=>'cursor:pointer;','id' => $data['_id'])).$glinks;
            }else{
                $display = link_to_asset($thumbnail_url.'?'.time(), $thumbnail_url, array('class'=>'thumbnail img-polaroid','style'=>'cursor:pointer;','id' => $data['_id'])).$glinks;
            }
            return $display;
        }else{
            return $data['SKU'];
        }
    }

    public function puDisp($data){
        return $data['pickup_person'].'<br />'.$data['pickup_dev_id'];
    }

    public function weightRange($data)
    {
        return Prefs::getWeightRange($data['weight'],$data['application_id']);
    }

    public function showWHL($data)
    {
        return $data['width'].'x'.$data['height'].'x'.$data['length'];
    }

    public function dispFBar($data)

    {
        $display = HTML::image(url('qr/'.urlencode(base64_encode($data['delivery_id'].'|'.$data['merchant_trans_id'].'|'.$data['fulfillment_code'].'|box:1' ))), $data['merchant_trans_id'], array('id' => $data['delivery_id'], 'style'=>'width:100px;height:auto;' ));
        //$display = '<a href="'.url('barcode/dl/'.urlencode($data['SKU'])).'">'.$display.'</a>';
        return $display.'<br />'. '<a href="'.url('incoming/detail/'.$data['delivery_id']).'" >'.$data['fulfillment_code'].' ('.$data['box_count'].' box)</a>';
    }

    public function sameEmail($data)
    {
        if($data['same_email'] == 1){
            return '<span class="dupe">'.$data['email'].'</span>';
        }else{
            return $data['email'];
        }
    }

    public function phoneList($data)
    {
        $phones = array($data['phone'],$data['mobile1'],$data['mobile2']);
        $phones = array_filter($phones);
        $phones = implode('<br />', $phones);

        if($data['same_phone'] == 1){
            return '<span class="dupe">'.$phones.'</span>';
        }else{
            return $phones;
        }
    }

    public function dispBar($data)

    {
        $display = HTML::image(url('qr/'.urlencode(base64_encode($data['delivery_id'].'|'.$data['merchant_trans_id'].'|'.$data['fulfillment_code'].'|box:1' ))), $data['merchant_trans_id'], array('id' => $data['delivery_id'], 'style'=>'width:100px;height:auto;' ));
        //$display = '<a href="'.url('barcode/dl/'.urlencode($data['SKU'])).'">'.$display.'</a>';
        return $display.'<br />'. '<a href="'.url('asset/detail/'.$data['delivery_id']).'" >'.$data['merchant_trans_id'].'</a>';
    }

    public function statusList($data)
    {

        return '<span class="orange white-text">'.$data['status'].'</span><br /><span class="brown">'.$data['pickup_status'].'</span><br /><span class="green">'.$data['courier_status'].'</span><br /><span class="maroon">'.$data['warehouse_status'].'</span>';
    }


    public function colorizetype($data)
    {
        return Prefs::colorizetype($data['delivery_type']);
    }


    public function pics($data)
    {
        $name = HTML::link('products/view/'.$data['_id'],$data['productName']);
        if(isset($data['thumbnail_url']) && count($data['thumbnail_url'])){
            $display = HTML::image($data['thumbnail_url'][0].'?'.time(), $data['filename'][0], array('style'=>'min-width:100px;','id' => $data['_id']));
            return $display.'<br /><span class="img-more" id="'.$data['_id'].'">more images</span>';
        }else{
            return $name;
        }
    }

    public function getPrintlabel($sessionname, $printparam, $format = 'html' )
    {
        $pr = explode(':',$printparam);

        $columns = $pr[0];
        $resolution = $pr[1];
        $cell_width = $pr[2];
        $cell_height = $pr[3];
        $margin_right = $pr[4];
        $margin_bottom = $pr[5];
        $font_size = $pr[6];
        $code_type = $pr[7];
        $left_offset = $pr[8];
        $top_offset = $pr[9];

        $session = Printsession::find($sessionname)->toArray();
        $labels = Shipment::whereIn('delivery_id', $session)->get()->toArray();

        $skus = array();
        foreach($labels as $l){
            $skus[] = $l['delivery_id'];
        }

        $skus = array_unique($skus);

        $products = Shipment::whereIn('delivery_id',$skus)->get()->toArray();

        $plist = array();
        foreach($products as $product){
            $plist[$product['delivery_id']] = $product;
        }

        return view('asset.printlabel')
            ->with('columns',$columns)
            ->with('resolution',$resolution)
            ->with('cell_width',$cell_width)
            ->with('cell_height',$cell_height)
            ->with('margin_right',$margin_right)
            ->with('margin_bottom',$margin_bottom)
            ->with('font_size',$font_size)
            ->with('code_type',$code_type)
            ->with('left_offset', $left_offset)
            ->with('top_offset', $top_offset)
            ->with('products',$plist)
            ->with('labels', $labels);
    }


    public function getViewpics($id)
    {

    }

    public function updateStock($data){

        //print_r($data);

        $outlets = $data['outlets'];
        $outletNames = $data['outletNames'];
        $addQty = $data['addQty'];
        $adjustQty = $data['adjustQty'];

        unset($data['outlets']);
        unset($data['outletNames']);
        unset($data['addQty']);
        unset($data['adjustQty']);

        for( $i = 0; $i < count($outlets); $i++)
        {

            $su = array(
                    'outletId'=>$outlets[$i],
                    'outletName'=>$outletNames[$i],
                    'productId'=>$data['id'],
                    'SKU'=>$data['SKU'],
                    'productDetail'=>$data,
                    'status'=>'available',
                    'createdDate'=>new MongoDate(),
                    'lastUpdate'=>new MongoDate()
                );

            if($addQty[$i] > 0){
                for($a = 0; $a < $addQty[$i]; $a++){
                    $su['_id'] = str_random(40);
                    Stockunit::insert($su);
                }
            }

            if($adjustQty[$i] > 0){
                $td = Stockunit::where('outletId',$outlets[$i])
                    ->where('productId',$data['id'])
                    ->where('SKU', $data['SKU'])
                    ->where('status','available')
                    ->orderBy('createdDate', 'asc')
                    ->take($adjustQty[$i])
                    ->get();

                foreach($td as $d){
                    $d->status = 'deleted';
                    $d->lastUpdate = new MongoDate();
                    $d->save();
                }
            }
        }


    }

    // worker functions

    public function order_save($indata,$api_key,$transaction_id)
    {
        $args = '';

        //$api_key = $this->get('key');
        //$transaction_id = $this->get('trx');

        if(is_null($api_key)){
            $result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
            return $result;
        }else{
            $app = $this->get_key_info(trim($api_key));

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

                $in->phone = ( isset( $in->phone ) && $in->phone != '')?Prefs::normalphone( $in->phone ):'';
                $in->mobile1 = ( isset( $in->mobile1 ) && $in->mobile1 != '' )?Prefs::normalphone( $in->mobile1 ):'';
                $in->mobile2 = ( isset( $in->mobile2 ) && $in->mobile2 != '' )?Prefs::normalphone( $in->mobile2 ):'';


                if(isset($in->buyer_id) && $in->buyer_id != '' && $in->buyer_id > 1){

                    $buyer_id = $in->buyer_id;
                    $is_new = false;

                }else{

                    if($in->email == '' || $in->email == '-' || !isset($in->email) || $in->email == 'noemail'){

                        $in->email = 'noemail';
                        $is_new = true;
                        if( trim($in->phone.$in->mobile1.$in->mobile2) != ''){
                            if($buyer = $this->check_phone($in->phone,$in->mobile1,$in->mobile2)){
                                $buyer_id = $buyer['id'];
                                $is_new = false;
                            }
                        }

                    }else if($buyer = $this->check_email($in->email)){

                        $buyer_id = $buyer['id'];
                        $is_new = false;

                    }else if($buyer = $this->check_phone($in->phone,$in->mobile1,$in->mobile2)){

                        $buyer_id = $buyer['id'];
                        $is_new = false;

                    }

                }

                if(isset($in->merchant_trans_id) && $in->merchant_trans_id != ""){
                    $transaction_id = $in->merchant_trans_id;
                }


                if($is_new){
                    $buyer_username = substr(strtolower(str_replace(' ','',$in->buyer_name)),0,6).random_string('numeric', 4);
                    $dataset['username'] = $buyer_username;
                    $dataset['email'] = $in->email;
                    $dataset['phone'] = $in->phone;
                    $dataset['mobile1'] = $in->mobile1;
                    $dataset['mobile2'] = $in->mobile2;
                    $dataset['fullname'] = $in->buyer_name;
                    $password = random_string('alnum', 8);
                    $dataset['password'] = $this->ag_auth->salt($password);
                    $dataset['created'] = date('Y-m-d H:i:s',time());

                    /*
                    $dataset['province'] =
                    $dataset['mobile']
                    */

                    $dataset['street'] = $in->shipping_address;
                    $dataset['district'] = $in->buyerdeliveryzone;
                    $dataset['city'] = $in->buyerdeliverycity;
                    $dataset['country'] = 'Indonesia';
                    $dataset['zip'] = $in->zip;

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

                $order['directions'] = (isset($in->directions))?$in->directions:'';
                //$order['dir_lat'] = $in->dir_lat;
                //$order['dir_lon'] = $in->dir_lon;
                $order['buyerdeliverytime'] = $in->buyerdeliverytime;
                $order['buyerdeliveryslot'] = (isset($in->buyerdeliveryslot))?$in->buyerdeliveryslot:1;
                $order['buyerdeliveryzone'] = (isset($in->buyerdeliveryzone))?$in->buyerdeliveryzone:'';
                $order['buyerdeliverycity'] = (is_null($in->buyerdeliverycity) || $in->buyerdeliverycity == '')?'Jakarta':$in->buyerdeliverycity;

                $order['currency'] = (isset($in->currency))?$in->currency:'IDR';
                $order['total_price'] = (isset($in->total_price))?$in->total_price:0;
                $order['total_discount'] = (isset($in->total_discount))?$in->total_discount:0;
                $order['total_tax'] = (isset($in->total_tax))?$in->total_tax:0;

                if($in->delivery_type == 'DO' || $in->delivery_type == 'Delivery Only'){
                    $order['cod_cost'] = 0;
                }else{
                    $order['cod_cost'] = Prefs::get_cod_tariff($order['total_price'],$app->id, date('Y-m-d', time() ) );
                }

                $order['box_count'] = (isset($in->box_count))?$in->box_count:1;

                $order['shipping_address'] = $in->shipping_address;
                $order['shipping_zip'] = $in->zip;
                $order['phone'] = $in->phone;
                $order['mobile1'] = $in->mobile1;
                $order['mobile2'] = $in->mobile2;
                $order['status'] = $in->status;

                $order['width'] = $in->width;
                $order['height'] = $in->height;
                $order['length'] = $in->length;
                $order['weight'] = (isset($in->weight))?$in->weight:0;
                $order['delivery_type'] = $in->delivery_type;

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

                $order['is_import'] = 1;


                //sprint_r($order);

                //die();

                //$inres = $this->db->insert($this->config->item('incoming_delivery_table'),$order);
                //$sequence = $this->db->insert_id();

                $inres = Shipment::create($order);

                //print_r($inres);

                //die();
                $sequence = $inres->id;

                //$delivery_id = get_delivery_id($sequence,$app->merchant_id);

                if(isset($in->delivery_id) ){
                    if(is_null($in->delivery_id) || $in->delivery_id == ''){
                        $delivery_id = Prefs::get_delivery_id($sequence,$app->merchant_id);
                    }else{
                        $delivery_id = Prefs::get_delivery_id($sequence,$app->merchant_id, $in->delivery_id);
                    }
                }else{
                    $delivery_id = Prefs::get_delivery_id($sequence,$app->merchant_id);
                }

                if(isset($in->box_count)){
                    $box_count = $in->box_count;
                }else{
                    $box_count = 1;
                }

                Prefs::save_box($delivery_id, trim($transaction_id), $order['fulfillment_code'],$box_count);

                $nedata['fullname'] = $in->buyer_name;
                $nedata['merchant_trx_id'] = trim($transaction_id);
                $nedata['delivery_id'] = $delivery_id;
                $nedata['merchantname'] = $app->application_name;
                $nedata['app'] = $app;

                $order['delivery_id'] = $delivery_id;

                $this->save_buyer($order);

                $norder = Shipment::find($sequence);

                $norder->delivery_id = $delivery_id;

                //print_r($norder);

                $norder->save();

                //$this->notify_by_email($in,$app);

            }
        }

        //$this->log_access($api_key, __METHOD__ ,$result,$args);
    }

    //private supporting functions

    private function notify_by_email($in,$app)
    {
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

        $d = 0;
        $gt = 0;


        if($in->trx_detail){
            $seq = 0;

            foreach($in->trx_detail as $it){
                $item['ordertime'] = $order['ordertime'];
                $item['delivery_id'] = $delivery_id;
                $item['unit_sequence'] = $seq++;
                $item['unit_description'] = $it->unit_description;
                $item['unit_price'] = $it->unit_price;
                $item['unit_quantity'] = $it->unit_quantity;
                $item['unit_total'] = $it->unit_total;
                $item['unit_discount'] = $it->unit_discount;

                $rs = $this->db->insert($this->config->item('delivery_details_table'),$item);

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

            return $result;
        }else{
            $nedata['detail'] = false;

            $result = json_encode(array('status'=>'OK:ORDERPOSTEDNODETAIL','timestamp'=>now(),'delivery_id'=>$delivery_id));

            return $result;
        }

        //print_r($app);

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

        }


    }

    private function get_key_info($key){
        if(!is_null($key)){

            $result = Application::where('key','=',$key)->first();

            if($result){
                //$row = $result->row();
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function get_dev_info($key){
        if(!is_null($key)){
            $result = Device::where('key','=',$key)->first();
            if($result){
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function get_dev_info_by_id($identifier){
        if(!is_null($identifier)){
            $result = Device::where('identifier','=',$identifier)->first();
            if($result){
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    private function check_email($email){
        $em = Member::where('email','=',$email)->first();
        if($em){
            return $em->toArray();
        }else{
            return false;
        }
    }

    private function check_phone($phone, $mobile1, $mobile2){
        $em = Shipment::where('phone','like',$phone)
                ->orWhere('mobile1','like',$mobile1)
                ->orWhere('mobile2','like',$mobile2)
                ->first();
        if($em){
            return $em->toArray();
        }else{
            return false;
        }
    }


    private function register_buyer($dataset){
        $dataset['group_id'] = 5;

        if($m = Buyer::create($dataset)){
            return $m->id;
        }else{
            return 0;
        }
    }

    private function save_buyer($ds){

        if(isset($ds['buyer_id']) && $ds['buyer_id'] != '' && $ds['buyer_id'] > 1){
            if($pid = $this->get_parent_buyer($ds['buyer_id'])){
                $bd['is_child_of'] = $pid;
                $this->update_group_count($pid);
            }
        }

        $bd['buyer_name']  =  $ds['buyer_name'];
        $bd['buyerdeliveryzone']  =  $ds['buyerdeliveryzone'];
        $bd['buyerdeliverycity']  =  $ds['buyerdeliverycity'];
        $bd['shipping_address']  =  $ds['shipping_address'];
        $bd['phone']  =  $ds['phone'];
        $bd['mobile1']  =  $ds['mobile1'];
        $bd['mobile2']  =  $ds['mobile2'];
        $bd['recipient_name']  =  $ds['recipient_name'];
        $bd['shipping_zip']  =  $ds['shipping_zip'];
        $bd['email']  =  $ds['email'];
        $bd['delivery_id']  =  $ds['delivery_id'];
        $bd['delivery_cost']  =  $ds['delivery_cost'];
        $bd['cod_cost']  =  $ds['cod_cost'];
        $bd['delivery_type']  =  $ds['delivery_type'];
        $bd['currency']  =  $ds['currency'];
        $bd['total_price']  =  $ds['total_price'];
        $bd['chargeable_amount']  =  $ds['chargeable_amount'];
        $bd['delivery_bearer']  =  $ds['delivery_bearer'];
        $bd['cod_bearer']  =  $ds['cod_bearer'];
        $bd['cod_method']  =  $ds['cod_method'];
        $bd['ccod_method']  =  $ds['ccod_method'];
        $bd['application_id']  =  $ds['application_id'];
        //$bd['buyer_id']  =  $ds['buyer_id'];
        $bd['merchant_id']  =  $ds['merchant_id'];
        $bd['merchant_trans_id']  =  $ds['merchant_trans_id'];
        //$bd['courier_id']  =  $ds['courier_id'];
        //$bd['device_id']  =  $ds['device_id'];
        $bd['directions']  =  $ds['directions'];
        //$bd['dir_lat']  =  $ds['dir_lat'];
        //$bd['dir_lon']  =  $ds['dir_lon'];
        //$bd['delivery_note']  =  $ds['delivery_note'];
        //$bd['latitude']  =  $ds['latitude'];
        //$bd['longitude']  =  $ds['longitude'];
        $bd['created']  =  $ds['created'];

        $bd['cluster_id'] = substr(md5(uniqid(rand(), true)), 0, 20 );

        if($m = Buyer::create($bd)){
            return $m->id;
        }else{
            return 0;
        }
    }

    private function get_parent_buyer($id){

        $by = Buyer::find($id);

        if($by){

            $buyer = $by->toArray();
            if($buyer['is_parent'] == 1){
                $pid = $buyer['id'];
            }elseif($buyer['is_child_of'] > 0 && $buyer['is_parent'] == 0){
                $pid = $buyer['is_child_of'];
            }else{
                $pid = false;
            }

            return $pid;

        }else{
            return false;
        }

    }

    private function update_group_count($id){

        $children = Buyer::where('is_child_of',$id);
        $groupcount = $children->count();

        $parent = Buyer::find($id);

        $parent->group_count = $groupcount + 1;

        if($res = $parent->save() ){
            return $res;
        }else{
            return false;
        }

    }

    private function get_device($key){
        $dev = Device::where('key','=',key)->first();

        if($dev){
            return $dev->toArray();
        }else{
            return false;
        }
    }

    private function get_group(){
        $this->db->select('id,description');
        $result = $this->db->get($this->ag_auth->config['auth_group_table']);
        foreach($result->result_array() as $row){
            $res[$row['id']] = $row['description'];
        }
        return $res;
    }

    private function log_access($api_key,$query,$result,$args = null){
        $data['timestamp'] = date('Y-m-d H:i:s',time());
        $data['accessor_ip'] = $this->accessor_ip;
        $data['api_key'] = (is_null($api_key))?'':$api_key;
        $data['query'] = $query;
        $data['result'] = $result;
        $data['args'] = (is_null($args))?'':$args;

        access_log($data);
    }

    private function admin_auth($username = null,$password = null){
        if(is_null($username) || is_null($password)){
            return false;
        }

        $password = $this->ag_auth->salt($password);
        $result = $this->db->where('username',$username)->where('password',$password)->get($this->ag_auth->config['auth_user_table']);

        if($result->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }


}
