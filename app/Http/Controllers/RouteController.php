<?php
namespace App\Http\Controllers;

use App\Http\Controllers\AdminController;

use App\Models\Pickup;
use App\Models\Orderlog;
use App\Models\Uploaded;
use App\Models\Geolog;
use App\Models\Role;

use App\Helpers\Prefs;

use Creitive\Breadcrumbs\Breadcrumbs;

use Auth;
use Event;
use View;
use Input;
use Request;
use Response;
use Mongomodel;
use \MongoRegex;
use DB;
use HTML;

class RouteController extends AdminController {

    public function __construct()
    {
        parent::__construct();

                $cname = substr(strrchr(get_class($this), '\\'), 1);
        $this->controller_name = str_replace('Controller', '', $cname);


        //$this->crumb = new Breadcrumb();
        //$this->crumb->append('Home','left',true);
        //$this->crumb->append(strtolower($this->controller_name));

        $this->model = new Geolog();
        //$this->model = DB::collection('documents');

    }

    public function getTest()
    {
        $raw = $this->model->where('docFormat','like','picture')->get();

        print $raw->toJSON();
    }


    public function getIndex()
    {
        $this->heads = array(
            array('Timestamp',array('search'=>true,'sort'=>true,'daterange'=>true)),
            array('Device Name',array('search'=>true,'sort'=>true)),
            array('Sensor',array('search'=>true,'sort'=>true)),
            array('Latitude',array('search'=>true,'sort'=>false)),
            array('Longitude',array('search'=>true,'sort'=>false)),
            array('Delivery Id',array('search'=>true,'sort'=>false)),
            array('Status',array('search'=>true,'sort'=>false)),
            array('App Name',array('search'=>true,'sort'=>true)),

        );

        //print $this->model->where('docFormat','picture')->get()->toJSON();

        $this->title = 'Route Planner';

        $this->show_select = false;

        $this->place_action = 'none';

        $this->can_add = false;


        //$this->additional_filter = View::make(strtolower($this->controller_name).'.addfilter')->with('submit_url','deliverytime')->render();

        $this->table_view = 'route.router';

        return parent::getIndex();

    }

    public function postIndex()
    {

        $this->fields = array(
            array('mtimestamp',array('kind'=>'daterange', 'callback'=>'showDatetime','query'=>'like','pos'=>'both','show'=>true)),
            array('deviceId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('sourceSensor',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('latitude',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('longitude',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('deliveryId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('status',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('appname',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true))
        );

        $this->def_order_by = 'mtimestamp';
        $this->def_order_dir = 'desc';
        $this->show_select = false;

        $this->place_action = 'none';

        return parent::postIndex();
    }

    public function SQL_additional_query($model)
    {

        //$model = $model->groupBy('timestamp')
        //            ->groupBy('appname')
        //            ->orderBy('mtimestamp','desc');

        return $model;

    }

    public function postSaveseq()
    {
        $in = Request::input();

        //print_r($in);

        $ids = array();
        $seqs = array();

        foreach($in['seq'] as $i){
            $ids[] = $i['id'];
            $seqs[$i['id']] = $i['seq'];
        }

        $shipments = Pickup::whereIn('transactionId',$ids)->get();

        foreach($shipments as $ship){
            $ship->assignmentSeq = intval($seqs[$ship->transactionId]);
            $ship->save();
        }

        return Response::json(array('result'=>'OK'));

    }



    public function postLocsave()
    {
        $in = Request::input();

        $order = Pickup::where('transactionId','=',$in['id'])
                    ->first();
        $order->latitude = $in['lat'];

        $order->longitude = $in['lon'];

        $order->save();

        return Response::json(['result'=>'OK']);

    }


    public function postLocsearch()
    {
        $in = Request::input();

        $term = $in['term'];

        $orders = Pickup::where('pickupAddress','like','%'.$term.'%')
                    ->where('latitude','!=','')
                    ->where('longitude','!=','')
                    ->where('latitude','!=',0)
                    ->where('longitude','!=',0)
                    ->get();

        //print_r($orders);

        $rtlist = array();

        if($orders){
            foreach($orders as $ord){
                $rtlist[] = View::make('locpicker.resitem')->with('order',$ord)->render();
            }
        }

        print implode('',$rtlist);

    }


    public function makeActions($data)
    {
        $delete = '<span class="del" id="'.$data['_id'].'" ><i class="icon-trash"></i>Delete</span>';
        $edit = '<a href="'.url('agent/edit/'.$data['_id']).'"><i class="icon-edit"></i>Update</a>';

        $actions = $edit.'<br />'.$delete;
        return $actions;
    }

    public function showDatetime($data)
    {
        return date('d-m-Y H:i:s',$data['mtimestamp']->sec );
    }

    public function splitTag($data){
        $tags = explode(',',$data['docTag']);
        if(is_array($tags) && count($tags) > 0 && $data['docTag'] != ''){
            $ts = array();
            foreach($tags as $t){
                $ts[] = '<span class="tag">'.$t.'</span>';
            }

            return implode('', $ts);
        }else{
            return $data['docTag'];
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

    public function namePic($data)
    {
        $name = HTML::link('products/view/'.$data['_id'],$data['productName']);
        if(isset($data['thumbnail_url']) && count($data['thumbnail_url'])){
            $display = HTML::image($data['thumbnail_url'][0].'?'.time(), $data['filename'][0], array('id' => $data['_id']));
            return $display.'<br />'.$name;
        }else{
            return $name;
        }
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

    public function getViewpics($id)
    {

    }


}
