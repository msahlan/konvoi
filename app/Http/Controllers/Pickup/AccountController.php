<?php
namespace App\Http\Controllers\Pickup;

use App\Http\Controllers\AdminController;

use App\Models\Creditaccount;
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
use Validator;

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
            array('Jenis Kredit / Program',array('search'=>true,'sort'=>true)),
            array('Jenis Barang',array('search'=>true,'sort'=>true,  'select'=> array_merge([''=>'All'], config('jc.credit_type' ) ) ) ),
            array('Jatuh Tempo',array('search'=>true,'sort'=>false  )),
            array('Jumlah Cicilan',array('search'=>true,'sort'=>true)),
            array('Tgl Bayar',array('search'=>true,'sort'=>true)),
            array('Deskripsi Barang',array('search'=>true,'sort'=>true)),
            array('Alamat Pengambilan',array('search'=>true,'sort'=>true)),
            array('Phone',array('search'=>true,'sort'=>true)),
            array('Kecamatan',array('search'=>true,'sort'=>true)),
            array('Kota',array('search'=>true,'sort'=>true)),
            array('Propinsi',array('search'=>true,'sort'=>true)),
            array('Kode Pos',array('search'=>true,'sort'=>true)),
            array('Kartu Pembayaran',array('search'=>true,'sort'=>true)),
            array('Nama Pembayar',array('search'=>true,'sort'=>true)),
            array('Email Pembayar',array('search'=>true,'sort'=>true)),
            //array('Created',array('search'=>true,'sort'=>true,'date'=>true)),
            //array('Last Update',array('search'=>true,'sort'=>true,'date'=>true)),
        );

        $this->title = 'Accounts';

        $this->can_add = true;

        $this->place_action = 'first';

        $day_load = $this->getQuotaByDate();

        //$this->additional_filter = View::make('shared.generate');

        $this->additional_filter = View::make(strtolower($this->controller_name).'.addfilter')
                ->with('day_load', $day_load )
                ->with('submit_url','gl')
                ->with('ajaxawbdlxl','incoming/awbdlxl')
                ->with('importawburl','incoming/importawb')
                ->render();


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
            array('programName',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('Type',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
            array('dueDate',array('kind'=>'numeric','query'=>'like','pos'=>'both','show'=>true,'attr'=>array('class'=>'expander'))),
            array('installmentAmt',array('kind'=>'currency','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDate',array('kind'=>'numeric','query'=>'like','pos'=>'both','show'=>true)),
            array('productDescription',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupAddress',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('phone',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true ,'callback'=>'phoneList', 'multi'=>['phone','mobile'],'multirel'=>'OR' )),
            array('pickupDistrict',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupCity',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupProvince',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupZIP',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('bankCard',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('payerName',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('payerEmail',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
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
        $creditor = Creditor::find($data['creditor']);
        $data['creditorName'] = $creditor->coName;

        if(isset($data['payerEmail']) && $data['payerEmail'] != ''){
            $payer = User::where('email','=', trim($data['payerEmail']))->first();

            if($payer){
                $data['payerId'] = $payer->id;
                $data['payerName'] = $payer->name;
                $data['phone'] = isset($payer->phone)?$payer->phone:'';
                $data['mobile'] = isset($payer->mobile)?$payer->mobile:'';
                $data['payerEmail'] = trim($data['payerEmail']);

                $data['bankCard'] = (isset($payer->bankCard))?$payer->bankCard:'';

            }else{
                $data['payerId'] = '';
                $data['payerName'] = $data['contractName'];
                $data['payerEmail'] = trim($data['payerEmail']);

                $data['bankCard'] = '';

            }

        }else{
            $data['payerId'] = '';
            $data['payerName'] = $data['contractName'];
            $data['payerEmail'] = 'Non Member';

            $data['bankCard'] = '';

        }

        $data['dueDate'] = intval($data['dueDate']);
        $data['pickupDate'] = intval($data['pickupDate']);

        return $data;
    }

    public function afterSave($data)
    {

    }

    public function beforeUpdate($id,$data)
    {

        $creditor = Creditor::find($data['creditor']);
        $data['creditorName'] = $creditor->coName;

        if(isset($data['payerEmail']) && $data['payerEmail'] != ''){
            $payer = User::where('email','=', trim($data['payerEmail']))->first();

            if($payer){
                $data['payerId'] = $payer->id;
                $data['payerName'] = $payer->name;
                $data['phone'] = isset($payer->phone)?$payer->phone:'';
                $data['mobile'] = isset($payer->mobile)?$payer->mobile:'';
                $data['payerEmail'] = trim($data['payerEmail']);

                $data['bankCard'] = (isset($payer->bankCard))?$payer->bankCard:'';

            }else{
                $data['payerId'] = '';
                $data['payerName'] = $data['contractName'];
                $data['payerEmail'] = trim($data['payerEmail']);

                $data['bankCard'] = '';

            }

        }else{
            $data['payerId'] = '';
            $data['payerName'] = $data['contractName'];
            $data['payerEmail'] = 'Non Member';

            $data['bankCard'] = '';

        }

        $data['dueDate'] = intval($data['dueDate']);
        $data['pickupDate'] = intval($data['pickupDate']);

        return $data;
    }

    public function postEdit($id,$data = null)
    {
        $this->validator = array(
            'contractNumber' => 'required',
            'creditor'=> 'required',
            'Type'=>'required',
            'dueDate'=>'required',
            'installmentAmt'=>'required',
            'pickupDate'=>'required',
            'pickupAddress'=>'required',
            'pickupDistrict'=>'required',
            'pickupCity'=>'required',
            'pickupZIP'=>'required',
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

    public function phoneList($data)
    {
        return $data['phone'].'<hr>'.$data['mobile'];
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

    public function getImport(){

        $this->importkey = '_id';

        $this->import_aux_form = View::make(strtolower($this->controller_name).'.crimportauxform')->render();

        return parent::getImport();
    }

    public function postUploadimport()
    {
        $this->importkey = '_id';

        $this->validator = [
            'creditor'=>'required',
            'inputfile'=>'required|file'
        ];

        $messages = [
            'creditor.required' => ':attribute harus dipilih salah satu.',
            'inputfile.required' => 'File untuk diupload tidak boleh kosong.',
        ];

        $validation = Validator::make(Request::all(), $this->validator, $messages);

        if($validation->fails()){
            return redirect('pickup/account/import')
                ->withErrors($validation)
                ->withInput();
        }else{
            return parent::postUploadimport();
        }
    }

    public function processImportAuxForm()
    {

        return array(
            'creditor'=>Request::input('creditor'),
            'creditorName'=>''
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

        $data['active'] = (strtolower($data['active']) == 'yes')?true:false;

        if(isset($data['payerEmail']) && $data['payerEmail'] != ''){
            $payer = User::where('email','=', trim($data['payerEmail']))->first();

            if($payer){
                $data['payerId'] = $payer->id;
                $data['payerName'] = $payer->name;
                if(isset($data['phone']) && trim($data['phone']) != ''){

                }else{
                    $data['phone'] = isset($payer->phone)?$payer->phone:'';
                }

                if(isset($data['mobile']) && trim($data['mobile']) != ''){

                }else{
                    $data['mobile'] = isset($payer->mobile)?$payer->mobile:'';
                }
                $data['payerEmail'] = trim($data['payerEmail']);

                $data['bankCard'] = (isset($payer->bankCard))?$payer->bankCard:'';

            }else{
                $data['payerId'] = '';
                $data['payerName'] = $data['contractName'];
                $data['payerEmail'] = trim($data['payerEmail']);

                $data['bankCard'] = '';

            }

        }else{
            $data['payerId'] = '';
            $data['payerName'] = $data['contractName'];
            $data['payerEmail'] = 'Non Member';

            $data['bankCard'] = '';

        }

        $creditor = Creditor::find($data['creditor']);
        $data['creditorName'] = $creditor->coName;

        $data['dueDate'] = intval($data['dueDate']);
        $data['pickupDate'] = intval($data['pickupDate']);

        if(isset($data['programName']) && $data['programName'] != ''){
            Prefs::checkProgram($data['creditor'], $data['programName'], $data['Type'], $data['creditorName']);
        }

        return $data;
    }


    public function postDlxl()
    {

        $this->heads = array(
            array('Active',array('search'=>false,'sort'=>false)),
            array('Nomor Kontrak',array('search'=>true,'sort'=>true)),
            array('Atas Nama',array('search'=>true,'sort'=>true)),
            array('Perusahaan Kreditor',array('search'=>true,'sort'=>true)),
            array('Jenis Cicilan',array('search'=>true,'sort'=>true ) ),
            array('Jatuh Tempo',array('search'=>true,'sort'=>false  )),
            array('Jumlah Cicilan',array('search'=>true,'sort'=>true)),
            array('Tgl Bayar',array('search'=>true,'sort'=>true)),
            array('Alamat Pengambilan',array('search'=>true,'sort'=>true)),
            array('Kecamatan',array('search'=>true,'sort'=>true)),
            array('Kota',array('search'=>true,'sort'=>true)),
            array('Kode Pos',array('search'=>true,'sort'=>true)),
            array('Propinsi',array('search'=>true,'sort'=>true)),
            array('Nama Pembayar',array('search'=>true,'sort'=>true)),
            array('Email Pembayar',array('search'=>true,'sort'=>true)),
            array('Created',array('search'=>true,'sort'=>true,'date'=>true)),
            array('Last Update',array('search'=>true,'sort'=>true,'date'=>true))
        );

        $this->fields = array(
            array('active',array('kind'=>'text' ,'query'=>'like','callback'=>'dlActive','pos'=>'both','show'=>true)),
            array('contractNumber',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
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
            array('payerName',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('payerEmail',array('kind'=>'text', 'query'=>'like','pos'=>'both','show'=>true)),
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


    public function getQuotaByDate()
    {
        $quotas = array();

        for($i=1;$i < 32 ; $i++){
            $quotas[$i] = $this->model->where('pickupDate','=',$i)->count();
        }

        return $quotas;
    }



}
