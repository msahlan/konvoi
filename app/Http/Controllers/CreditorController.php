<?php
namespace App\Http\Controllers;

use App\Http\Controllers\AdminController;

use App\Models\Creditor;
use App\Models\Uploaded;
use App\Models\Role;
use App\Models\User;

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
use Route;

class CreditorController extends AdminController {

    public function __construct()
    {
        parent::__construct();


        //$cname = (new \ReflectionClass($this))->getShortName();

        $cname = substr(strrchr(get_class($this), '\\'), 1);

        $this->controller_name = str_replace('Controller', '', $cname);

        //$this->crumb = new Breadcrumb();
        //$this->crumb->append('Home','left',true);
        //$this->crumb->append(strtolower($this->controller_name));

        $this->model = new Creditor();
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
            array('Photo',array('search'=>false,'sort'=>false)),
            array('Company Name',array('search'=>true,'sort'=>true)),
            array('PIC Name',array('search'=>true,'sort'=>true)),
            array('Address',array('search'=>true,'sort'=>false, 'select'=>Prefs::getRole()->RoleToSelection('_id','rolename' )  )),
            array('Address 2',array('search'=>true,'sort'=>true)),
            array('Phone',array('search'=>true,'sort'=>true)),
            array('Fax',array('search'=>true,'sort'=>true)),
            array('City',array('search'=>true,'sort'=>true)),
            array('Created',array('search'=>true,'sort'=>true,'date'=>true)),
            array('Last Update',array('search'=>true,'sort'=>true,'date'=>true)),
        );

        $this->title = 'Creditors';

        $this->can_add = true;

        $this->place_action = 'first';

        $this->crumb->addCrumb('System',url( strtolower($this->controller_name) ));

        return parent::getIndex();

    }

    public function postIndex()
    {

        $this->fields = array(
            array('coName',array('kind'=>'text', 'callback'=>'namePic' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('coName',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('picName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('address_1',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true,'attr'=>array('class'=>'expander'))),
            array('address_2',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('phone',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('fax',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('city',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('createdDate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('lastUpdate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
        );

        return parent::postIndex();
    }

    public function postAdd($data = null)
    {

        $this->validator = array(
            'coName' => 'required',
            'address_1'=> 'required',
            'coPhone'=>'required',
            'coEmail'=>'required',
            'pickupFee'=>'required',
            'password'=>'required|same:password_confirmation'
        );

        if($data['userId'] == ''){
            $this->validator['email']='required|unique:users';
        }

        return parent::postAdd($data);
    }

    public function beforeSave($data)
    {
        $photo = array();
        $avatar = '';

        if( isset($data['fileid'])){

            $avfile = Uploaded::find($data['fileid']);
            if($avfile){
                $avatar = $avfile->square_url;
                $photo[] = $avfile->toArray();
            }

        }

        $data['photo']= $photo;
        $data['logo'] = $avatar;

        return $data;
    }

    public function afterSave($data)
    {
        foreach($data['photo'] as $p) {
            $up = Uploaded::find($p['_id']);
            if($up){
                $up->parent_id = $data['_id'];
                $up->save();
            }
        }
    }


    public function beforeUpdateForm($population)
    {
        $pic = User::find($population['picId']);

        $population['name'] = $pic->name;
        $population['email'] = $pic->email;
        $population['phone'] = $pic->phone;
        $population['mobile'] = $pic->mobile;

        return $population;
    }

    public function beforeUpdate($id,$data)
    {

        $photo = array();
        $avatar = '';

        if( isset($data['fileid'])){

            $avfile = Uploaded::find($data['fileid']);
            if($avfile){
                $avatar = $avfile->square_url;
            }

        }

        $data['photo']= $photo;
        $data['logo'] = $avatar;


        return $data;
    }

    public function postEdit($id,$data = null)
    {
        $this->validator = array(
            'coName' => 'required',
            'address_1'=> 'required',
            'coPhone'=>'required',
            'coEmail'=>'required'
        );

        if($data['password'] != ''){
            $this->validator['password'] = 'required|same:password_confirmation';
        }

        return parent::postEdit($id,$data);
    }

    public function makeActions($data)
    {
        $route = Route::current();

        $delete = '<span class="del" id="'.$data['_id'].'" ><i class="fa fa-trash"></i>Delete</span>';
        $edit = '<a href="'.url( $route->getPrefix().'/creditor/edit/'.$data['_id']).'"><i class="fa fa-edit"></i>Update</a>';

        $actions = $edit.'<br />'.$delete;
        return $actions;
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
        $display = '<span style="display:block;text-align:center;color:green;"><i style="font-size:48px;" class="icon-user"></i></span>';
        if(isset($data['logo']) && $data['logo'] != ''){
            if(Prefs::checkUrl($data['logo'])){
                $display = '<span style="display:block;text-align:center;color:green;">'.HTML::image($data['logo'].'?'.time(), $data['fullname'], array('id' => $data['_id'],'class'=>'img-circle avatar')).'</span>';
            }
        }else{
            //$display = HTML::image(url('images/no-photo.png').'?'.time(), $data['fullname'], array('id' => $data['_id']));
        }

        return $display;

    }

    public function idRole($data)
    {
        $role = Role::find($data['roleId']);
        if($role){
            return $role->rolename;
        }else{
            return '';
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
