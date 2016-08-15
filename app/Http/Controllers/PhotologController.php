<?php

class PhotologController extends AdminController {

    public $heads = array(
            array('Timestamp',array('search'=>true,'sort'=>true,'datetimerange'=>true)),
            array('Thumbnail',array('search'=>true,'sort'=>true)),
            array('Delivery ID',array('search'=>true,'sort'=>false)),
            array('Device ID',array('search'=>true,'sort'=>false)),
            array('File ID',array('search'=>true,'sort'=>true)),
            array('Is Signature',array('search'=>true,'sort'=>true)),
            array('Latitude',array('search'=>true,'sort'=>true)),
            array('Longitude',array('search'=>true,'sort'=>false)),
            array('Name',array('search'=>true,'sort'=>true)),
            array('Namespace',array('search'=>true,'sort'=>true)),
            array('Parent Class',array('search'=>true,'sort'=>true)),
            array('Size',array('search'=>true,'sort'=>true)),
            array('Type',array('search'=>true,'sort'=>false)),
            array('Original URL',array('search'=>true,'sort'=>true)),
            array('App Name',array('search'=>true,'sort'=>true))

        );

    public $fields = array(
            array('createdDate',array('kind'=>'datetime','query'=>'like','pos'=>'both','show'=>true)),
            array('thumbnail_url',array('kind'=>'text', 'callback'=>'showThumb','query'=>'like','pos'=>'both','show'=>true)),
            array('parent_id',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('deviceId',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('file_id',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('is_signature',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('latitude',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('longitude',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('name',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('ns',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('parent_class',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('size',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('type',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
            array('url',array('kind'=>'text','query'=>'like','pos'=>'both','show'=>true)),
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

        $this->model = new Uploaded();
        //$this->model = DB::collection('documents');

    }

    public function getIndex()
    {
        //$this->heads = $this->def_heads;

        //print $this->model->where('docFormat','picture')->get()->toJSON();

        $this->title = 'Photo Log';

        $this->show_select = false;

        $this->place_action = 'none';

        return parent::getIndex();

    }

    public function postIndex()
    {

        //$this->fields = $this->def_fields;

        $this->def_order_by = 'createdDate';
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

    public function showThumb($data)
    {
        $display = HTML::image($data['thumbnail_url'].'?'.time(), $data['filename'], array('id' => $data['_id']));

        return $display;
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
