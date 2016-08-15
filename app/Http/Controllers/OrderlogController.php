<?php

class OrderlogController extends AdminController {

    public $heads = array(
            array('Timestamp',array('search'=>true,'sort'=>true,'datetimerange'=>true)),
            array('Merchant Id',array('search'=>true,'sort'=>true)),
            array('Delivery Id',array('search'=>true,'sort'=>true)),
            array('No Kode Toko',array('search'=>true,'sort'=>false)),
            array('Fulfillment Code',array('search'=>true,'sort'=>false)),
            array('Status',array('search'=>true,'sort'=>false)),
            array('Delivery Time',array('search'=>true,'sort'=>true)),
            array('Delivery Note',array('search'=>true,'sort'=>true)),
            array('Delivery Actor',array('search'=>true,'sort'=>true)),
            array('Assigned Delivery Device',array('search'=>true,'sort'=>true)),
            array('Pickup Status',array('search'=>true,'sort'=>true)),
            array('Pickup Time',array('search'=>true,'sort'=>true)),
            array('Pick Up Actor',array('search'=>true,'sort'=>true)),
            array('Warehouse Status',array('search'=>true,'sort'=>true)),
            array('Warehouse In',array('search'=>true,'sort'=>true ,'datetimerange'=>true)),
            array('Warehouse Out',array('search'=>true,'sort'=>true ,'datetimerange'=>true)),
            array('Warehouse Actor',array('search'=>true,'sort'=>true)),
            array('Latitude',array('search'=>true,'sort'=>false)),
            array('Longitude',array('search'=>true,'sort'=>false)),
            array('App Name',array('search'=>true,'sort'=>true)),

        );

    public $fields = array(
            array('created_at',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('merchantId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('deliveryId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('merchantTransId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('fulfillmentCode',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('status',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('deliverytime',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('deliveryNote',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('deliveryDevId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('deviceId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupStatus',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickuptime',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('pickupDevId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('warehouseStatus',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('warehouseIn',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('warehouseOut',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('warehouseDevId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('latitude',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('longitude',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
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

        $this->model = new Orderlog();
        //$this->model = DB::collection('documents');

    }

    public function getIndex()
    {
        //$this->heads = $this->def_heads;

        //print $this->model->where('docFormat','picture')->get()->toJSON();

        $this->title = 'Order Status Log';

        $this->show_select = false;

        $this->place_action = 'none';

        return parent::getIndex();

    }

    public function postIndex()
    {

        //$this->fields = $this->def_fields;

        $this->def_order_by = 'created_at';
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
