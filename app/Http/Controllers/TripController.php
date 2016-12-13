<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AdminController;

use App\Models\Trips;


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
class TripController extends AdminController
{
    public function __construct()
    {
        parent::__construct();


        //$cname = (new \ReflectionClass($this))->getShortName();

        $cname = substr(strrchr(get_class($this), '\\'), 1);

        $this->controller_name = str_replace('Controller', '', $cname);

        //$this->crumb = new Breadcrumb();
        //$this->crumb->append('Home','left',true);
        //$this->crumb->append(strtolower($this->controller_name));

        $this->model = new Trips();
        //$this->model = DB::collection('documents');

    }

      public function postAdd($data = null)

    {

        $this->validator = array(
            'name' => 'required',
            'email'=> 'required|unique:users',
            'date_konvoi'=>'required'
        );

        return parent::postAdd($data);
    }

      public function beforeSave($data)
    {         
        return $data;
    }

    public function getIndex(){
    	$this->heads = array(
    		array('Full Name',array('search'=>true,'sort'=>true)),
    		array('Mobile', array('search'=>true,'sort'=>true)),
    		array('Email',array('search'=>true,'sort'=>true)),
    		array('Address',array('search'=>true,'sort'=>true)),
    		array('Tanggal Touring',array('search'=>true,'sort'=>true)),
    		array('Tujuan',array('search'=>true,'sort'=>true)),
    		array('Deskripsi Touring',array('search'=>true,'sort'=>true)),
            array('Created At',array('search'=>true,'sort'=>true)),
            array('Las Update',array('search'=>true,'sort'=>true)),

    		);
    	$this->title = 'Touring';

        $this->can_add = true;

        $this->place_action = 'first';

        $this->crumb->addCrumb('System',url( strtolower($this->controller_name) ));

        return parent::getIndex();

    }

    public function postIndex()
    {

        $this->fields = array(
           
            array('name',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('mobile',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('email',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('address',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('date_konvoi',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('destination',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('description',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('createdDate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('lastUpdate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
        );

        return parent::postIndex();
    }

    public function postEdit($id,$data = null)
    {
        $this->validator = array(
            'name' => 'required',
            'email'=> 'required'
        );

        if($data['password'] == ''){
            unset($data['password']);
            unset($data['repass']);
        }else{
            $this->validator['password'] = 'required|same:repass';
        }

        return parent::postEdit($id,$data);
    }

}
