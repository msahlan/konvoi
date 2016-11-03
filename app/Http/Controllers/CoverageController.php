<?php
namespace App\Http\Controllers;

use App\Http\Controllers\AdminController;

use App\Models\Coverage;
use App\Models\Uploaded;
use App\Models\Role;
use App\Models\Quota;

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

class CoverageController extends AdminController {

    public function __construct()
    {
        parent::__construct();


        //$cname = (new \ReflectionClass($this))->getShortName();

        $cname = substr(strrchr(get_class($this), '\\'), 1);

        $this->controller_name = str_replace('Controller', '', $cname);

        //$this->crumb = new Breadcrumb();
        //$this->crumb->append('Home','left',true);
        //$this->crumb->append(strtolower($this->controller_name));

        $this->model = new Coverage();
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
            array('Province',array('search'=>true,'sort'=>true)),
            //array('Quota',array('search'=>true,'sort'=>true)),
            array('City',array('search'=>true,'sort'=>true)),
            //array('Quota',array('search'=>true,'sort'=>true)),
            array('District',array('search'=>true,'sort'=>true)),
            //array('Quota',array('search'=>true,'sort'=>true)),
            array('ZIP',array('search'=>true,'sort'=>true ) ),
            //array('Created',array('search'=>true,'sort'=>true,'date'=>true)),
            //array('Last Update',array('search'=>true,'sort'=>true,'date'=>true)),
        );

        $this->title = 'Coverage';

        $this->can_add = true;

        $this->place_action = 'first';

        $this->additional_filter = View::make('quota.addfilter')->render();

        $this->js_table_event = view('quota.js_table_event')->render();

        //$this->crumb->addCrumb('System',url( strtolower($this->controller_name) ));

        return parent::getIndex();

    }

    public function postIndex()
    {

        $this->fields = array(
            array('province',array('kind'=>'text' ,'query'=>'like','pos'=>'both','show'=>true)),
            //array('provinceQuota',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('city',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            //array('cityQuota',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('district',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            //array('districtQuota',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('zip',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            //array('createdDate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            //array('lastUpdate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
        );

        return parent::postIndex();
    }

    public function postAdd($data = null)
    {

        $this->validator = array(
            'province' => 'required',
            'city'=> 'required',
            'district'=>'required'
        );

        return parent::postAdd($data);
    }

    public function beforeSave($data)
    {

        return $data;
    }

    public function afterSave($data)
    {
        return $data;
    }

    public function beforeUpdate($id,$data)
    {

        return $data;
    }

    public function postEdit($id,$data = null)
    {
        $this->validator = array(
            'province' => 'required',
            'city'=> 'required',
            'district'=>'required'
        );

        return parent::postEdit($id,$data);
    }

    public function makeActions($data)
    {

        $edit = '<a href="'.url('pickup/account/edit/'.$data['_id']).'"><i class="fa fa-edit"></i> Update</a>';

        $delete = '<span class="del" id="'.$data['_id'].'" ><i class="fa fa-trash"></i> Del</span>';

        $actions = $edit.'<br />'.$delete;

        return $actions;
    }

    public function SQL_additional_query($model)
    {

        $model = $model->orderBy('province','asc')
            ->orderBy('city','asc')
            ->orderBy('district','asc');

        return $model;

    }

    public function rows_post_process($rows, $aux = null){

        $province = '';
        $city = '';

        //print_r($rows);

        if(count($rows) > 0){

            for($i = 0; $i < count($rows); $i++){
                if($rows[$i][3] != $province){
                    $city = '';
                    $province = $rows[$i][3];
                    $rows[$i][3] = $rows[$i][3];
                    //$rows[$i][4] = $this->provinceButton($province);
                    //$rows[$i][4] = $rows[$i][4].'<span id="'.$province.'" data-type="text" data-province="'.$province.'" data-title="Update Quota" class="editProvinceQuota provinceQuota pointer label label-primary" data-original-title="" title="">Update</span>';
                }else{
                    $rows[$i][3] = '';
                    //$rows[$i][4] = $rows[$i][4].'<a href="#" id="provinceQuota" data-type="text" data-pk="'.$rows[$i][4].'" data-title="Update Quota" class="provinceQuota editable editable-click" data-original-title="" title="">Update</a>';
                    //$rows[$i][4] = '';
                }


                if($rows[$i][4] != $city){
                    $city = $rows[$i][4];
                    $rows[$i][4] = $rows[$i][4];
                    //$rows[$i][6] = '<a href="#" id="cityQuota" data-type="text" data-pk="'.$rows[$i][6].'" data-title="Update Quota" class="cityQuota editable editable-click" data-original-title="" title="">'.$rows[$i][6].'</a>';
                }else{
                    $rows[$i][4] = '';
                    //$rows[$i][6] = '';
                }

            }


        }



        return $rows;

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

    public function provinceButton($province)
    {
        $quota = Quota::where('province','=',$province)->where('scope','=','province')->first();
        $devcap = '';
        $devnum = '';
        $provinceQuota = 0;
        if($quota){
            $provinceQuota = $quota->quota;
            $devcap = $quota->devCap;
            $devnum = $quota->devNum;
        }

        return $provinceQuota.'<span id="'.$province.'" data-type="text" data-province="'.$province.'" data-devcap="'.$devcap.'" data-devnum="'.$devnum.'" data-title="Update Quota" class="editProvinceQuota provinceQuota pointer label label-primary pull-right" data-original-title="" title="">Update</span>';
    }

    public function postSavequota()
    {
        $num = Request::input('devnum');
        $cap = Request::input('devcap');
        $dq = Request::input('province');

        $quota = intval($num) * intval($cap);

        $q = Quota::where('province','=',trim($dq))
                ->where('scope','=','province')
                ->first();
        if($q){

        }else{
            $q = new Quota();
        }

        $q->province = trim($dq);
        $q->quota = $quota;
        $q->scope = 'province';
        $q->devCap = $cap;
        $q->devNum = $num;

        $r = $q->save();

        DB::collection('districts')->where('province', trim($dq) )
                       ->update(['provinceQuota'=>$quota], ['multi' => true]);

        if($r){
            return Response::json( array('result'=>'OK', 'quota'=>$r ) );
        }else{
            return Response::json( array('result'=>'ERR', 'message'=>'Failed to save Quota' ) );
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
