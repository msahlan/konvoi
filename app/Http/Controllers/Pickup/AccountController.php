<?php
namespace App\Http\Controllers\Pickup;

use App\Http\Controllers\AdminController;

use App\Models\Creditaccount;
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
use Route;

class AccountController extends AdminController {

    public function __construct()
    {
        parent::__construct();


        //$cname = (new \ReflectionClass($this))->getShortName();

        $cname = substr(strrchr(get_class($this), '\\'), 1);

        $this->controller_name = str_replace('Controller', '', $cname);

        //$this->crumb = new Breadcrumb();
        //$this->crumb->append('Home','left',true);
        //$this->crumb->append(strtolower($this->controller_name));

        $this->model = new Creditaccount();
        //$this->model = DB::collection('documents');

    }

    public function getTest()
    {
        $raw = $this->model->where('docFormat','like','picture')->get();

        print $raw->toJSON();
    }


    public function getIndex()
    {

        print Route::currentRouteName();

        $this->heads = array(
            array('Active',array('search'=>true,'sort'=>true, 'select'=>[''=>'All',true=>'Yes',false=>'No'] )),
            array('Nomor Kontrak',array('search'=>true,'sort'=>true)),
            array('Atas Nama',array('search'=>true,'sort'=>true)),
            array('Perusahaan Kreditor',array('search'=>true,'sort'=>true)),
            array('Tipe',array('search'=>true,'sort'=>true,  'select'=> array_merge([''=>'All'], config('jc.credit_type' ) ) ) ),
            array('Jatuh Tempo',array('search'=>true,'sort'=>false  )),
            array('Jumlah Cicilan',array('search'=>true,'sort'=>true)),
            array('Tgl Bayar',array('search'=>true,'sort'=>true)),
            array('Alamat Pengambilan',array('search'=>true,'sort'=>true)),
            array('Kecamatan',array('search'=>true,'sort'=>true)),
            array('Kota',array('search'=>true,'sort'=>true)),
            //array('Created',array('search'=>true,'sort'=>true,'date'=>true)),
            //array('Last Update',array('search'=>true,'sort'=>true,'date'=>true)),
        );

        $this->title = 'Accounts';

        $this->can_add = true;

        $this->place_action = 'first';

        //$this->crumb->addCrumb('System',url( strtolower($this->controller_name) ));

        return parent::getIndex();

    }

    public function postIndex()
    {

        $this->fields = array(
            array('active',array('kind'=>'boolean' ,'callback'=>'statusToggle','query'=>'like','pos'=>'both','show'=>true)),
            array('contractNumber',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('contractName',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('creditorName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('Type',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('dueDate',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true,'attr'=>array('class'=>'expander'))),
            array('installmentAmt',array('kind'=>'currency','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDate',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupAddress',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDistrict',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupCity',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            //array('createdDate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            //array('lastUpdate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
        );

        return parent::postIndex();
    }

    public function postAdd($data = null)
    {

        $this->validator = array(
            'name' => 'required',
            'email'=> 'required|unique:users',
            'password'=>'required|same:repass'
        );

        return parent::postAdd($data);
    }

    public function beforeSave($data)
    {
        unset($data['repass']);
        $data['password'] = bcrypt($data['password']);

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
            $data['avatar'] = $avatar;

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

    public function beforeUpdate($id,$data)
    {

        if(isset($data['password']) && $data['password'] != ''){
            unset($data['repass']);
            $data['password'] = bcrypt($data['pass']);

        }else{
            unset($data['password']);
            unset($data['repass']);
        }

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

    public function makeActions($data)
    {

        $edit = '<a href="'.url('pickup/account/edit/'.$data['_id']).'"><i class="fa fa-edit"></i> Update</a>';

        $delete = '<span class="del" id="'.$data['_id'].'" ><i class="fa fa-trash"></i> Del</span>';

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

    public function statusToggle($data)
    {
        if($data['active']){
            $toggle = '<span class="toggle" id="'.$data['_id'].'" ><i class="fa fa-toggle-on"></i> Yes</span>';
        }else{
            $toggle = '<span class="toggle" id="'.$data['_id'].'" ><i class="fa fa-toggle-off"></i> No</span>';
        }

        return $toggle;
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

    public function dlActive($data){
        return ( isset($data['active']) && $data['active'])?'Yes':'No';
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

    public function postDlxl()
    {

        $this->heads = array(
            array('Active',array('search'=>false,'sort'=>false)),
            array('Nomor Kontrak',array('search'=>true,'sort'=>true)),
            array('Atas Nama',array('search'=>true,'sort'=>true)),
            array('Perusahaan Kreditor',array('search'=>true,'sort'=>true)),
            array('Tipe',array('search'=>true,'sort'=>true ) ),
            array('Jatuh Tempo',array('search'=>true,'sort'=>false  )),
            array('Jumlah Cicilan',array('search'=>true,'sort'=>true)),
            array('Tgl Bayar',array('search'=>true,'sort'=>true)),
            array('Alamat Pengambilan',array('search'=>true,'sort'=>true)),
            array('Kecamatan',array('search'=>true,'sort'=>true)),
            array('Kota',array('search'=>true,'sort'=>true)),
            array('Kode Pos',array('search'=>true,'sort'=>true)),
            array('Propinsi',array('search'=>true,'sort'=>true)),
            array('Created',array('search'=>true,'sort'=>true,'date'=>true)),
            array('Last Update',array('search'=>true,'sort'=>true,'date'=>true))
        );

        $this->fields = array(
            array('contractNumber',array('kind'=>'text' ,'query'=>'like','callback'=>'dlActive','pos'=>'both','show'=>true)),
            array('contractNumber',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            array('contractName',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('creditorName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('Type',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('dueDate',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true,'attr'=>array('class'=>'expander'))),
            array('installmentAmt',array('kind'=>'currency','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDate',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupAddress',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDistrict',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupCity',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupZIP',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupProvince',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('createdDate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('lastUpdate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true))
        );

        //$this->heads = config('jc.default_incoming_heads');

        //$this->fields = config('jc.default_incoming_fields');

        $this->def_order_by = 'createdDate';
        $this->def_order_dir = 'desc';
        $this->place_action = 'first';
        $this->show_select = true;

        return parent::postDlxl();
    }


}
