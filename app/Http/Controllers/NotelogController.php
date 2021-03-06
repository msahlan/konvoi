<?php
namespace App\Http\Controllers;

use App\Http\Controllers\AdminController;

use App\Models\Deliverynote;
use App\Models\Uploaded;
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

class NotelogController extends AdminController {

    public $heads = array(
            array('Timestamp',array('search'=>true,'sort'=>true,'datetimerange'=>true)),
            array('Delivery Id',array('search'=>true,'sort'=>true)),
            array('Status',array('search'=>true,'sort'=>false)),
            array('Latitude',array('search'=>true,'sort'=>false)),
            array('Longitude',array('search'=>true,'sort'=>false)),
            array('Delivery Note',array('search'=>true,'sort'=>true)),
            array('Device Name',array('search'=>true,'sort'=>true)),
            array('App Name',array('search'=>true,'sort'=>true))
        );

    public $fields = array(
            array('mtimestamp',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('deliveryId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('status',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('latitude',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('longitude',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('note',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('deviceId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('appname',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true))
        );



    public function __construct()
    {
        parent::__construct();

                $cname = substr(strrchr(get_class($this), '\\'), 1);
        $this->controller_name = str_replace('Controller', '', $cname);


        //$this->crumb = new Breadcrumb();
        //$this->crumb->append('Home','left',true);
        //$this->crumb->append(strtolower($this->controller_name));

        $this->model = new Deliverynote();
        //$this->model = DB::collection('documents');

    }

    public function getIndex()
    {
        //$this->heads = $this->def_heads;

        //print $this->model->where('docFormat','picture')->get()->toJSON();

        $this->title = 'Delivery Note Log';

        $this->show_select = false;

        $this->place_action = 'none';

        $this->can_add = false;

        return parent::getIndex();

    }

    public function postIndex()
    {

        //$this->fields = $this->def_fields;

        $this->def_order_by = 'mtimestamp';
        $this->def_order_dir = 'desc';
        $this->show_select = false;

        $this->place_action = 'none';

        return parent::postIndex();
    }

    public function postDlxl()
    {

        return parent::postDlxl();
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
