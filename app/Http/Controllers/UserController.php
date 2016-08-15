<?php
namespace App\Http\Controllers;

use App\Http\Controllers\AdminController;

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

class UserController extends AdminController {

    public function __construct()
    {
        parent::__construct();


        //$cname = (new \ReflectionClass($this))->getShortName();

        $cname = substr(strrchr(get_class($this), '\\'), 1);

        $this->controller_name = str_replace('Controller', '', $cname);

        //$this->crumb = new Breadcrumb();
        //$this->crumb->append('Home','left',true);
        //$this->crumb->append(strtolower($this->controller_name));

        $this->model = new User();
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
            array('Full Name',array('search'=>true,'sort'=>true)),
            array('Role',array('search'=>true,'sort'=>false, 'select'=>Prefs::getRole()->RoleToSelection('_id','rolename' )  )),
            array('Email',array('search'=>true,'sort'=>true)),
            array('Mobile',array('search'=>true,'sort'=>true)),
            array('Address',array('search'=>true,'sort'=>true)),
            array('Created',array('search'=>true,'sort'=>true,'date'=>true)),
            array('Last Update',array('search'=>true,'sort'=>true,'date'=>true)),
        );

        $this->title = 'Users';

        $this->can_add = true;

        $this->place_action = 'first';

        $this->crumb->addCrumb('System',url( strtolower($this->controller_name) ));

        return parent::getIndex();

    }

    public function postIndex()
    {

        $this->fields = array(
            array('fullname',array('kind'=>'text', 'callback'=>'namePic' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('fullname',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('roleId',array('kind'=>'text', 'callback'=>'idRole' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('email',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true,'attr'=>array('class'=>'expander'))),
            array('mobile',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('address_1',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('createdDate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('lastUpdate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
        );

        return parent::postIndex();
    }

    public function postAdd($data = null)
    {

        $this->validator = array(
            'firstname' => 'required',
            'lastname' => 'required',
            'email'=> 'required|unique:agents',
            'pass'=>'required|same:repass'
        );

        return parent::postAdd($data);
    }

    public function beforeSave($data)
    {
        unset($data['repass']);
        $data['pass'] = Hash::make($data['pass']);

        $data['fullname'] = $data['firstname'].' '.$data['lastname'];

            $photo = array();
            $avatar = '';

            if( isset($data['file_id']) && count($data['file_id'])){

                for($i = 0 ; $i < count($data['thumbnail_url']);$i++ ){

                    $photo['role'] = $data['role'][$i];
                    $photo['thumbnail_url'] = $data['thumbnail_url'][$i];
                    $photo['large_url'] = $data['large_url'][$i];
                    $photo['medium_url'] = $data['medium_url'][$i];
                    $photo['full_url'] = $data['full_url'][$i];
                    $photo['delete_type'] = $data['delete_type'][$i];
                    $photo['delete_url'] = $data['delete_url'][$i];
                    $photo['filename'] = $data['filename'][$i];
                    $photo['filesize'] = $data['filesize'][$i];
                    $photo['temp_dir'] = $data['temp_dir'][$i];
                    $photo['filetype'] = $data['filetype'][$i];
                    $photo['is_image'] = $data['is_image'][$i];
                    $photo['is_audio'] = $data['is_audio'][$i];
                    $photo['is_video'] = $data['is_video'][$i];
                    $photo['fileurl'] = $data['fileurl'][$i];
                    $photo['file_id'] = $data['file_id'][$i];

                    $avatar = $photo['medium_url'];
                }
            }

            $data['photo']= $photo;
            $data['avatar'] = $avatar;

        return $data;
    }

    public function beforeUpdate($id,$data)
    {

        if(isset($data['pass']) && $data['pass'] != ''){
            unset($data['repass']);
            $data['pass'] = Hash::make($data['pass']);

        }else{
            unset($data['pass']);
            unset($data['repass']);
        }

        $data['fullname'] = $data['firstname'].' '.$data['lastname'];

        $photo = array();
        $avatar = '';

        if( isset($data['fileid'])){

            $avfile = Uploaded::find($data['fileid']);
            if($avfile){
                $avatar = $avfile->square_url;
            }

        }

        $data['photo']= $photo;
        $data['avatar'] = $avatar;


        return $data;
    }

    public function postEdit($id,$data = null)
    {
        $this->validator = array(
            'firstname' => 'required',
            'lastname' => 'required',
            'email'=> 'required'
        );

        if($data['pass'] == ''){
            unset($data['pass']);
            unset($data['repass']);
        }else{
            $this->validator['pass'] = 'required|same:repass';
        }

        return parent::postEdit($id,$data);
    }

    public function makeActions($data)
    {
        $delete = '<span class="del" id="'.$data['_id'].'" ><i class="fa fa-trash"></i>Delete</span>';
        $edit = '<a href="'.url('user/edit/'.$data['_id']).'"><i class="fa fa-edit"></i>Update</a>';

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
        if(isset($data['avatar']) && $data['avatar'] != ''){
            if(Prefs::checkUrl($data['avatar'])){
                $display = '<span style="display:block;text-align:center;color:green;">'.HTML::image($data['avatar'].'?'.time(), $data['fullname'], array('id' => $data['_id'],'class'=>'img-circle avatar')).'</span>';
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
