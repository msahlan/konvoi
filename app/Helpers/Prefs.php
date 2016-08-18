<?php
namespace App\Helpers;

use App\Models\Role;
use App\Models\Shipment;
use App\Models\Uploaded;
use App\Models\Deliveryfee;
use App\Models\Codsurcharge;
use App\Models\Box;
use App\Models\Boxstatus;

class Prefs {

    public static $category;
    public static $shopcategory;
    public static $section;
    public static $faqcategory;
    public static $productcategory;
    public static $role;
    public static $merchant;
    public static $logistic;
    public static $device;
    public static $courier;
    public static $position;
    public static $node;

    public function __construct()
    {

    }

    public static function checkUrl($url)
    {
       $headers = @get_headers( $url);
       $headers = (is_array($headers)) ? implode( "\n ", $headers) : $headers;

       return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
    }

    public static function getBilled($r)
    {
            if(isset($r['total_price'])){
                if($r['total_price'] == 0 || is_null($r['total_price']) || $r['total_price'] == ''){
                    if($r['chargeable_amount'] > 0){
                        $r['total_price'] = $r['chargeable_amount'];
                    }
                }
            }else{
                $r['total_price'] = 0;
            }

            //print $r['total_price']."\r\n";

            $app_id = $r['application_id'];

            $total =  $r['total_price'];
            $dsc =  $r['total_discount'];
            $tax = $r['total_tax'];
            $dc = $r['delivery_cost'];
            $cod = $r['cod_cost'];
            $charge = $r['chargeable_amount'];

            $total = (is_nan( (double)$total))?0:(double)$total;
            $dsc = (is_nan((double)$dsc))?0:(double)$dsc;
            $tax = (is_nan((double)$tax))?0:(double)$tax;
            $dc = (is_nan((double)$dc))?0:(double)$dc;
            $cod = (is_nan((double)$cod))?0:(double)$cod;
            $charge = (is_nan((double)$charge))?0:(double)$charge;

            if($total == 0 && $charge > 0){
                $total = $charge;
            }

            $payable = 0;

            $payable = ($total - $dsc) + $tax;

            //$codval = ($r->delivery_type == 'COD'|| $r->delivery_type == 'CCOD')?$payable:0;


            if($r['delivery_type'] == 'COD'|| $r['delivery_type'] == 'CCOD'){
                if($r['delivery_bearer'] == 'merchant'){
                    $dcx = 0;
                }else{
                    $dcx = $dc;
                }

                if($r['cod_bearer'] == 'merchant'){
                    $codx = 0;
                }else{
                    $codx = $cod;
                }

                $codval = ($total - $dsc) + $tax + $dcx + $codx;

            }else{
                $cod = 0;
                $codval = 0;
            }

            //$codval = $charge;
            //$total_cod_val += $codval;

            $orderdate = date('Y-m-d', strtotime($r['created']) );

            if($r['delivery_type'] == 'COD' || $r['delivery_type'] == 'CCOD'){
                if($r['cod_cost'] == 0 || is_null($r['cod_cost']) || $r['cod_cost'] == ''){
                    try{
                        if(isset($r['total_price'])){
                            $cod = self::get_cod_tariff($r['total_price'],$app_id, $orderdate);
                        }
                    }catch(Exception $e){

                    }
                }

            }else{
                $cod = 0;
            }


            if($r['delivery_cost'] == 0 || is_null($r['delivery_cost']) || $r['delivery_cost'] == ''){
                try{
                    $dc = self::get_weight_tariff($r['actual_weight'], $r['delivery_type'] ,$app_id, $orderdate);
                }catch(Exception $e){

                }

            }

            return array(
                    'payable'=>$payable,
                    'cod_surcharge'=>$codval,
                );

    }

    public static function getWeightNominalCache($app_id)
    {
            $tars = Deliveryfee::whereIn('app_id',$app_id)->get();

            $tararray = array();

            foreach($tars as $t){
                $tararray[$t['app_id']][$t['total']] = $t['calculated_kg'];
            }

            return $tararray;

    }

    public static function getAuxData($dids)
    {
        $sign = Uploaded::whereIn('parent_id',$dids)
                    ->where(function($qs){
                        $qs->where('is_signature','=',1)
                            ->orWhere('is_signature','=',strval(1));
                    })->count();
        $photos = Uploaded::whereIn('parent_id',$dids)
                    ->where(function($qp){
                        $qp->where('is_signature','=',0)
                            ->orWhere('is_signature','=',strval(0));
                    })->count();

        $loc = Geolog::whereIn('deliveryId',$dids)
                    ->where(function($ql){
                        $ql->where('status','=','delivered')
                            ->orWhere('status','=','returned')
                            ->orWhere('status','=','pending');
                    })->count();

        return array(
                'photo'=>$photos,
                'sign'=>$sign,
                'loc'=>$loc
            );
    }

    public static function getAuxDataDetail($dids)
    {
        $signs = Uploaded::whereIn('parent_id',$dids)
                    ->where(function($qs){
                        $qs->where('is_signature','=',1)
                            ->orWhere('is_signature','=',strval(1));
                    })->get();

        $photos = Uploaded::whereIn('parent_id',$dids)
                    ->where(function($qp){
                        $qp->where('is_signature','=',0)
                            ->orWhere('is_signature','=',strval(0));
                    })->get();

        $locs = Geolog::whereIn('deliveryId',$dids)
                    ->where(function($ql){
                        $ql->where('status','=','delivered')
                            ->orWhere('status','=','returned')
                            ->orWhere('status','=','pending');
                    })->get();

        $sign_groups = array();
        foreach($signs as $s){
            if( isset($sign_groups[$s->parent_id])){
                $sign_groups[$s->parent_id] += 1;
            }else{
                $sign_groups[$s->parent_id] = 1;
            }
        }

        $photo_groups = array();
        foreach($photos as $p){
            if( isset($photo_groups[$p->parent_id])){
                $photo_groups[$p->parent_id] += 1;
            }else{
                $photo_groups[$p->parent_id] = 1;
            }
        }

        $loc_groups = array();
        foreach($locs as $l){
            if( isset($loc_groups[$l->parent_id])){
                $loc_groups[$l->parent_id] += 1;
            }else{
                $loc_groups[$l->parent_id] = 1;
            }
        }

        return array(
                'photo'=>$photo_groups,
                'sign'=>$sign_groups,
                'loc'=>$loc_groups
            );
    }

    public static function getNotes($delivery_id, $as_array = true)
    {
        $notes = Deliverynote::where('deliveryId','=',$delivery_id)
                    ->orderBy('mtimestamp','desc')
                    ->get();

        if($as_array){
            return $notes->toArray();
        }else{
            $list = '<ul class="note_list">';
            foreach($notes as $note){
                $list .= '<li>';
                $list .= '<b>'.$note->status.'</b><br />';
                $list .= $note->datetimestamp.'<br />';
                $list .= $note->note;
                $list .= '</li>';
            }

            $list .= '</ul>';

            return $list;
        }

    }


    public static function getTrip($all = false)
    {
        $trip_count = Options::get('trip_per_day',1);
        if($all){
            $trips = array(''=>'All');
        }else{
            $trips = array();
        }
        for($t = 1; $t<= intval($trip_count);$t++ ){
            $trips[$t] = 'Trip '.$t;
        }

        return $trips;
    }


    public static function getPicStat($delivery_id)
    {
        $pic_count = 0;
        $sign_count = 0;

        $app = 'app v 1.0';

        $pics_db = Uploaded::where('parent_id','=',$delivery_id)
                    ->get();

        if($pics_db){

            if(count($pics_db->toArray()) > 0){
                $app = 'app v 2.0';
            }

            foreach($pics_db as $pic){
                if( intval($pic->is_signature) == 1){
                    $sign_count++;
                }else{
                    $pic_count++;
                }
            }
        }


        if($pic_count == 0 && $sign_count == 0){
            $existingpic = glob(config('jayon.picture_path').$delivery_id.'*.jpg');

            foreach($existingpic as $pic){
                if(preg_match('/_sign.jpg$/', $pic)){
                    $sign_count++;
                }else{
                    $pic_count++;
                }
            }
        }



        return array('pic'=>$pic_count, 'sign'=>$sign_count, 'app'=>$app );
    }

    public static function getThumbnailStat($delivery_id, $class = 'thumb'){

        $existingpic = glob(config('jayon.picture_path').$delivery_id.'*.jpg');

        //print_r($existingpic);

        $pidx = count($existingpic);

        foreach($existingpic as $epic){
            if(!file_exists(config('jayon.thumbnail_path').'th_'.$epic )){
                //generate_thumbnail( str_replace('.jpg', '', $epic ) );
            }
        }

        if($pidx > 1){
            $ths = '';
            foreach($existingpic as $epic){
                $epic2 = str_replace(config('jayon.picture_path'), '', $epic);


                //if(!file_exists(config('jayon.thumbnail_path').'th_'.$epic )){
                    $thumb = URL::to('/').'/public/receiver/'.$epic2;
                    $ths .= sprintf('<img style="width:45px;35px;float:left;" alt="'.$epic2.'" src="%s?'.time().'" />',$thumb);
                //}
            }

            $class = 'thumb_multi';

            $thumper = '<img class="'.$class.'" style="width:100%;height:100%;" alt="'.$delivery_id.'" src="'.URL::to('/').'/assets/images/10.png" >';

            $ths .= '<div style="width:100%;height:100%;display:block;position:absolute;top:0px;left:0px;">'.$thumper.'</div>';

            $thumbnail = '<div style="width:100px;height:75px;clear:both;display:block;cursor:pointer;position:relative;border:thin solid brown;overflow-y:hidden;">'.$ths.'</div>';
        }else{
            if(file_exists(config('jayon.picture_path').$delivery_id.'.jpg')){
                if(file_exists(config('jayon.thumbnail_path').'th_'.$delivery_id.'.jpg')){
                    $thumbnail = URL::to('/').'/public/receiver_thumb/th_'.$delivery_id.'.jpg';
                    $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
                }else{
                    if(generate_thumbnail($delivery_id)){
                        $thumbnail = URL::to('/').'/public/receiver_thumb/th_'.$delivery_id.'.jpg';
                        $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
                    }else{
                        $thumbnail = $CI->ag_asset->load_image('th_nopic.jpg');
                        $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
                    }
                }
            }else{
                if(file_exists(config('jayon.thumbnail_path').'th_'.$delivery_id.'.jpg')){
                    if($pidx > 0){
                        $class = 'thumb_multi';
                    }
                    $thumbnail = URL::to('/').'/public/receiver_thumb/th_'.$delivery_id.'.jpg';
                    $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
                }else{
                    $thumbnail = URL::to('/').'/assets/images/th_nopic.jpg';
                    $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
                }
            }
        }

        $has_sign = false;

        if(file_exists(config('jayon.picture_path').$delivery_id.'_sign.jpg')){
            //if(file_exists(config('jayon.thumbnail_path').'th_'.$delivery_id.'_sign.jpg')){
                $sthumbnail = URL::to('/').'/public/receiver/'.$delivery_id.'_sign.jpg';
                $thumbnail .= sprintf('<img style="cursor:pointer;width:100px;height:auto;" class="sign '.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" />',$sthumbnail);
            //}
            $has_sign = true;
        }

        if($has_sign){
            $gal = '<br />'.($pidx - 1).' pics & 1 signature';
        }else{
            $gal = '<br />'.$pidx.' pics, no signature';
        }

        if($pidx > 0){
            for($g = 0; $g < $pidx; $g++){
                $img = str_replace(config('jayon.picture_path'), '', $existingpic[$g]);
                $gal .= '<input type="hidden" class="gal_'.$delivery_id.'" value="'.$img.'" >';
            }
        }

        $thumbnail = $thumbnail.$gal;

        return $thumbnail;
    }



    public static function getWeightRange($tariff,$application_id)
    {

        if($tariff > 0){

            $model = Deliveryfee::where('total',$tariff);
            if(!is_null($application_id)){
                $model = $model->where('app_id',$application_id);
            }
            $row = $model->first();
            if($row){
                return $row->kg_from.' kg - '.$row->kg_to.' kg';
            }else{
                return 0;
            }
        }else{
            return 0;
        }

    }

    public static function getWeightNominal($tariff,$application_id)
    {

        if($tariff > 0){

            $model = Deliveryfee::where('total',$tariff);
            if(!is_null($application_id)){
                $model = $model->where('app_id',$application_id);
            }
            $row = $model->first();
            if($row){
                return $row->calculated_kg;
            }else{
                return 0;
            }
        }else{
            return 0;
        }

    }

    public static function getTypeselect()
    {
        return config('jex.logistic_type_select');
    }

    public static function getDeliveryId()
    {
        $d = date('d-mY',time()).'-'.strtoupper( str_random(5) ) ;
        return $d;
    }


    //old school function
    public static function random_string($type, $length)
    {

        return str_random($length);

    }

    public static function get_delivery_id($sequence,$merchant_id,$delivery_id = null){

        if(is_null($delivery_id) || $delivery_id == ''){
            $year_count = str_pad($sequence, config('jayon.year_sequence_pad'), '0', STR_PAD_LEFT);
            $merchant_id = str_pad($merchant_id, config('jayon.merchant_id_pad'), '0', STR_PAD_LEFT);
            $delivery_id = $merchant_id.'-'.date('d-mY',time()).'-'.$year_count;
        }else{
            $dr = Generatedawb::where('merchant_id','=',$merchant_id)
                    ->where('awb_string','=',$delivery_id)
                    ->where('is_used','=',0)
                    ->first();

            if($dr){

                $delivery_id = $delivery_id;
                $up = array('is_used'=>1, 'used_at'=>date('Y-m-d H:i:s', time() ) );

                $dr->is_used = 1;
                $dr->used_at = date('Y-m-d H:i:s', time() );
                $dr->save();

            }else{

                $year_count = str_pad($sequence, config('jayon.year_sequence_pad'), '0', STR_PAD_LEFT);
                $merchant_id = str_pad($merchant_id, config('jayon.merchant_id_pad'), '0', STR_PAD_LEFT);
                $delivery_id = $merchant_id.'-'.date('d-mY',time()).'-'.$year_count;
            }
        }


        return $delivery_id;
    }

    public static function generate_delivery_id($sequence,$merchant_id,$date = null){

        $year_count = str_pad($sequence, config('jayon.year_sequence_pad'), '0', STR_PAD_LEFT);
        $merchant_id = str_pad($merchant_id, config('jayon.merchant_id_pad'), '0', STR_PAD_LEFT);
        if(is_null($date)){
            $delivery_id = $merchant_id.'-'.date('d-mY',time()).'-'.$year_count;
        }else{
            $date = date('d-mY', strtotime($date) );
            $delivery_id = $merchant_id.'-'.$date.'-'.$year_count;
        }

        return $delivery_id;
    }

    public static function get_key_info($key){
        if(!is_null($key)){
            $row = Application::where('key','=',$key)->first();
            if($row){
                return $row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    public static function save_box($delivery_id, $merchant_trans_id, $fulfillment_code,$count){

        $affected = Box::where('delivery_id','=',$delivery_id)
            ->where('merchant_trans_id','=',$merchant_trans_id)
            ->where('fulfillment_code','=',$fulfillment_code)->delete();


        for($i = 0; $i < $count;$i++){

            $bd = new Box();
            $bd->delivery_id = $delivery_id;
            $bd->merchant_trans_id = $merchant_trans_id;
            $bd->fulfillment_code = $fulfillment_code;
            $bd->box_id = $i + 1;
            $bd->save();

            $bds = new Boxstatus();
            $bds->delivery_id = $delivery_id;
            $bds->merchant_trans_id = $merchant_trans_id;
            $bds->fulfillment_code = $fulfillment_code;
            $bds->box_id =  $i + 1;
            $bds->timestamp = date('Y-m-d H:i:s',time());
            $bds->save();
        }

    }

    public static function save_buyer($ds){

        $bd = array();

        if(isset($ds['buyer_id']) && $ds['buyer_id'] != '' && $ds['buyer_id'] > 1){
            if($pid = self::get_parent_buyer($ds['buyer_id'])){
                $bd['is_child_of'] = $pid;
                self::update_group_count($pid);
            }
        }

        $bd['buyer_name']  =  $ds['buyer_name'];
        $bd['buyerdeliveryzone']  =  $ds['buyerdeliveryzone'];
        $bd['buyerdeliverycity']  =  $ds['buyerdeliverycity'];
        $bd['shipping_address']  =  $ds['shipping_address'];
        $bd['phone']  =  $ds['phone'];
        $bd['mobile1']  =  $ds['mobile1'];
        $bd['mobile2']  =  $ds['mobile2'];
        $bd['recipient_name']  =  $ds['recipient_name'];
        $bd['shipping_zip']  =  $ds['shipping_zip'];
        $bd['email']  =  $ds['email'];
        $bd['delivery_id']  =  $ds['delivery_id'];
        $bd['delivery_cost']  =  $ds['delivery_cost'];
        $bd['cod_cost']  =  $ds['cod_cost'];
        $bd['delivery_type']  =  $ds['delivery_type'];
        $bd['currency']  =  $ds['currency'];
        $bd['total_price']  =  $ds['total_price'];
        $bd['chargeable_amount']  =  $ds['chargeable_amount'];
        $bd['delivery_bearer']  =  $ds['delivery_bearer'];
        $bd['cod_bearer']  =  $ds['cod_bearer'];
        $bd['cod_method']  =  $ds['cod_method'];
        $bd['ccod_method']  =  $ds['ccod_method'];
        $bd['application_id']  =  $ds['application_id'];
        //$bd['buyer_id']  =  $ds['buyer_id'];
        $bd['merchant_id']  =  $ds['merchant_id'];
        $bd['merchant_trans_id']  =  $ds['merchant_trans_id'];
        //$bd['courier_id']  =  $ds['courier_id'];
        //$bd['device_id']  =  $ds['device_id'];
        $bd['directions']  =  $ds['directions'];
        //$bd['dir_lat']  =  $ds['dir_lat'];
        //$bd['dir_lon']  =  $ds['dir_lon'];
        //$bd['delivery_note']  =  $ds['delivery_note'];
        //$bd['latitude']  =  $ds['latitude'];
        //$bd['longitude']  =  $ds['longitude'];
        $bd['created']  =  $ds['created'];

        $bd['cluster_id'] = substr(md5(uniqid(rand(), true)), 0, 20 );

        $buyer = new Buyer();

        foreach($bd as $b=>$v){
            $buyer->{$b} = $v;
        }

        $buyer->save();

        if($buyer){
            return $buyer->id;
        }else{
            return 0;
        }
    }


    private static function get_parent_buyer($id){
        $by = Buyer::where('id','=',$id)->first();

        if($by){

            $buyer = $by->toArray();
            if($buyer['is_parent'] == 1){
                $pid = $buyer['id'];
            }elseif($buyer['is_child_of'] > 0 && $buyer['is_parent'] == 0){
                $pid = $buyer['is_child_of'];
            }else{
                $pid = false;
            }

            return $pid;

        }else{
            return false;
        }

    }

    private static function update_group_count($id){

        $this->db->where('is_child_of',$id);
        $groupcount = Buyer::where('is_child_of','=',$id)->count();

        $dataup = array('group_count'=>($groupcount + 1) );

        $par = Buyer::where('id','=',$id)->first();

        if($par){
            $par->group_count = $groupcount + 1;
            $res = $par->save();
            return $res;
        }else{
            return false;
        }

    }

    public static function normalphone($number){
        $numbers = explode('/',$number);
        if(is_array($numbers)){
            $nums = array();
            foreach($numbers as $number){

                $number = str_replace(array('-',' ','(',')','[',']','{','}'), '', $number);

                if(preg_match('/^\+/', $number)){
                    if( preg_match('/^\+62/', $number)){
                        $number = preg_replace('/^\+62|^620/', '62', $number);
                    }else{
                        $number = preg_replace('/^\+/', '', $number);
                    }
                }else if(preg_match('/^62/', $number)){
                    $number = preg_replace('/^620/', '62', $number);
                }else if(preg_match('/^0/', $number)){
                    $number = preg_replace('/^0/', '62', $number);
                }

                $nums[] = $number;
            }
            $number = implode('/',$nums);
        }else{

            $number = str_replace(array('-',' ','(',')'), '', $number);

            if(preg_match('/^\+/', $number)){
                if( preg_match('/^\+62/', $number)){
                    $number = preg_replace('/^\+62|^620/', '62', $number);
                }else{
                    $number = preg_replace('/^\+/', '', $number);
                }
            }else if(preg_match('/^62/', $number)){
                $number = preg_replace('/^620/', '62', $number);
            }else if(preg_match('/^0/', $number)){
                $number = preg_replace('/^0/', '62', $number);
            }
        }

        return $number;
    }

    public static function check_email($email){

        $em = Buyer::where('email','=',$email)->first();

        //$em = $this->db->where('email',$email)->get($this->config->item('jayon_members_table'));
        if($em){
            return $em;
        }else{
            return false;
        }
    }

    public static function check_phone($phone, $mobile1, $mobile2){
        $em = Buyer::where('phone','like',$phone)
                ->orWhere('mobile1','like',$mobile1)
                ->orWhere('mobile2','like',$mobile2)->first();

        if($em){
            return $em;
        }else{
            return false;
        }
    }

    public static function get_cod_tariff($total_price,$app_id = null, $date = null){

        $total_price = doubleval($total_price);

        $row = 0;

        if(is_null($app_id)){
            $max = Codsurcharge::max('to_price');

            if($total_price > $max){
                $row = Codsurcharge::max('surcharge');
            }else{
                $selq = Codsurcharge::where('from_price','<=', doubleval($total_price) )
                        ->where('to_price', '>=', doubleval($total_price) );

                if(!is_null($date)){
                    $selq = $selq->where('period_from', '<=',$date)
                                ->where('period_to','>=',$date);
                }

                if(!is_null($app_id)){
                    $selq = $selq->where('app_id','=',$app_id);
                }

                $sel =  $selq->first();

                if($sel){
                    if(isset($sel->surcharge)){
                        $row = $sel->surcharge;
                    }else{
                        $row = 0;
                    }
                }

            }

        }else{
            $max = Codsurcharge::where('app_id','=',$app_id)->max('to_price');

            if($total_price > $max){
                $row = Codsurcharge::where('app_id','=',$app_id)->max('surcharge');
            }else{
                $selq = Codsurcharge::where('from_price','<=', doubleval($total_price) )
                        ->where('app_id','=',$app_id)
                        ->where('to_price', '>=', doubleval($total_price) );

                if(!is_null($date)){
                    $selq = $selq->where('period_from', '<=',$date)
                                ->where('period_to','>=',$date);
                }

                if(!is_null($app_id)){
                    $selq = $selq->where('app_id','=',$app_id);
                }

                $sel =  $selq->first();

                if($sel){
                    if(isset($sel->surcharge)){
                        $row = $sel->surcharge;
                    }else{
                        $row = 0;
                    }
                }

            }

        }

        return $row;
    }


    public static function get_weight_tariff($weight, $delivery_type ,$app_id = null){

        $weight = floatval($weight);

        if($weight > 0){

            if($delivery_type == 'PS'){
                if(is_null($app_id)){
                    return 0;
                }else{
                    $w = Psfee::where('app_id','=',$app_id)
                        ->where('kg_from','<=', $weight )
                        ->where('kg_to','>=', $weight )->first();
                    if($w){
                        return $w->total;
                    }else{
                        return 0;
                    }
                }
            }else{
                if(is_null($app_id)){
                    return 0;
                }else{
                    $w = Deliveryfee::where('app_id','=',$app_id)
                        ->where('kg_from','<=', $weight )
                        ->where('kg_to','>=', $weight )->first();
                    if($w){
                        return $w->total;
                    }else{
                        return 0;
                    }
                }
            }

        }else{
            return 0;
        }
    }


    public static function hashcheck($in , $pass){

        $hash = hash("haval256,5", config('kickstart.ci_key') . $in);

        if($hash == $pass){
            return true;
        }else{
            return false;
        }

    }

    public static function getRoleId($rolename){
        $role = Role::where('rolename',$rolename)->first();
        if($role){
            return $role->_id;
        }else{
            return false;
        }
    }

    //Courier
    public static function getCourier($key = null, $val=null){
        if(is_null($key)){
            $c = Courier::get();
            self::$courier = $c;
            return new self;
        }else{
            if($key == '_id'){
                $val = new MongoId($val);
            }
            $c = Courier::where($key,'=',$val)->first();
            self::$courier = $c;
            return $c;
        }
    }

    public function courierToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$courier as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function CourierToArray()
    {
        return self::$courier;
    }


    //Device
    public static function getDevice($key = null, $val=null){
        if(is_null($key)){
            $c = Device::get();
            self::$device = $c;
            return new self;
        }else{
            $c = Device::where($key,'=',$val)->first();
            self::$device = $c;
            return $c;
        }
    }

    public function DeviceToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$device as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function DeviceToArray()
    {
        return self::$device;
    }

    //Merchant
    public static function getMerchant($key = null, $val = null, $order = null, $dir = null ){
        if(is_null($key)){


            $c = Merchant::where('group_id','=',4)
                    ->orderBy('merchantname', 'asc')
                    ->get();
            self::$merchant = $c;
            return new self;
        }else{
            $c = Merchant::where($key,'=',$val)->first();
            self::$merchant = $c;
            return $c;
        }
    }

    public function merchantToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$merchant as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function merchantToArray()
    {
        return self::$merchant;
    }


    //Logistics
    public static function getLogistic($key = null, $val = null){
        if(is_null($key)){
            $c = Logistic::get();
            self::$logistic = $c;
            return new self;
        }else{
            $c = Logistic::where($key,'=',$val)->first();
            self::$logistic = $c;
            return $c;
        }
    }

    public function LogisticToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$logistic as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function LogisticToArray()
    {
        return self::$logistic;
    }

    //Disposition
    public static function getPosition(){
        $c = Position::get();

        self::$position = $c;
        return new self;
    }

    public function PositionToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'Select Position');
        }else{
            $ret = array();
        }

        foreach (self::$position as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function PositionToArray()
    {
        return self::$position;
    }



    public static function getNode(){
        $s = Position::get();

        self::$node = $s;
        return new self;
    }

    public function nodeToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$node as $s) {
            $ret[$s->{$value}] = $s->{$label};
        }

        return $ret;
    }

    public static function getShopCategory(){
        $c = Shopcategory::get();
        self::$shopcategory = $c;
        return new self;
    }

    public static function getCategory(){
        $c = Category::get();
        self::$category = $c;
        return new self;
    }

    public static function getSection(){
        $s = Section::get();

        self::$section = $s;
        return new self;
    }

    public function sectionToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$section as $s) {
            $ret[$s->{$value}] = $s->{$label};
        }

        return $ret;
    }


    public function catToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$category as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function ShopCatToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'All');
        }else{
            $ret = array();
        }

        foreach (self::$shopcategory as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function sectionToArray()
    {
        return self::$section;
    }

    public function catToArray()
    {
        return self::$category;
    }

    public function shopcatToArray()
    {
        return self::$shopcategory;
    }

    public static function getRole(){
        $c = Role::get();

        self::$role = $c;
        return new self;
    }

    public function RoleToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'Select Role');
        }else{
            $ret = array();
        }

        foreach (self::$role as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function RoleToArray()
    {
        return self::$role;
    }

//company
    public static function getCompany(){
        $c = Company::get();

        self::$role = $c;
        return new self;
    }

    public function CompanyToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'Select Company');
        }else{
            $ret = array();
        }

        foreach (self::$role as $c) {
            $ret[$c->{$value}] = $c->{$value}.' - '.$c->{$label};
        }


        return $ret;
    }

    public function CompanyToArray()
    {
        return self::$role;
    }

//company
    public static function getCoa(){
        $c = Coa::get();

        self::$role = $c;
        return new self;
    }

    public function CoaToSelection($value, $label, $all = true)
    {
        if($all){
            $ret = array(''=>'Select Coa');
        }else{
            $ret = array();
        }

        foreach (self::$role as $c) {
            $ret[$c->{$value}] = $c->{$label};
        }


        return $ret;
    }

    public function CoaToArray()
    {
        return self::$role;
    }


    public static function yearSelection(){
        $ya = array();
        for( $i = 1970; $i < 2050; $i++ ){
            $ya[$i] = $i;
        }
        return $ya;
    }

    public static function GetBatchId($SKU, $year, $month){

        $seq = DB::collection('batchnumbers')->raw();

        $new_id = $seq->findAndModify(
                array(
                    'SKU'=>$SKU,
                    'year'=>$year,
                    'month'=>$month
                    ),
                array('$inc'=>array('sequence'=>1)),
                null,
                array(
                    'new' => true,
                    'upsert'=>true
                )
            );


        $batchid = $year.$month.str_pad($new_id['sequence'], 4, '0', STR_PAD_LEFT);

        return $batchid;

    }

    public static function ExtractProductCategory($selection = true)
    {
        $category = Product::distinct('category')->get()->toArray();
        if($selection){
            $cats = array(''=>'All');
        }else{
            $cats = array();
        }

        //print_r($category);
        foreach($category as $cat){
            $cats[$cat[0]] = $cat[0];
        }

        return $cats;
    }

    public static function ExtractPages($selection = true)
    {
        $category = Viewlog::distinct('pageUri')->get()->toArray();
        if($selection){
            $cats = array(''=>'All');
        }else{
            $cats = array();
        }

        //print_r($category);
        foreach($category as $cat){
            $cats[$cat[0]] = $cat[0];
        }

        return $cats;
    }

    public static function ExtractHotspot($selection = true)
    {
        $category = Viewlog::distinct('spot')->get()->toArray();
        if($selection){
            $cats = array(''=>'All');
        }else{
            $cats = array();
        }

        //print_r($category);
        foreach($category as $cat){
            $cats[$cat[0]] = $cat[0];
        }

        return $cats;
    }

    public static function ExtractAdAsset($merchant_id,$selection = true)
    {
        $category = Asset::where('merchantId', $merchant_id )->get()->toArray();
        if($selection){
            $cats = array(''=>'All');
        }else{
            $cats = array();
        }

        if(count($category) > 0){
            foreach($category as $cat){
                $cats[$cat['_id']] = $cat['itemDescription'];
            }
        }

        return $cats;
    }

    public static function themeAssetsUrl()
    {
        return URL::to('/').'/'.Theme::getCurrentTheme();
    }

    public static function themeAssetsPath()
    {
        return 'themes/'.Theme::getCurrentTheme().'/assets/';
    }

    public static function getActiveTheme()
    {
        return config('kickstart.default_theme');
    }

    public static function getPrintDefault($type = 'asset'){
        $printdef = Printdefault::where('ownerId',Auth::user()->_id)
                        ->where('type',$type)
                        ->first();
        if($printdef){
            return $printdef;
        }else{
            $d = new stdClass();
            $d->col = 2;
            $d->res = 150;
            $d->cell_width = 250;
            $d->cell_height = 300;
            $d->margin_right = 8;
            $d->margin_bottom = 10;
            $d->font_size = 8;
            $d->code_type = 'qr';

            return $d;
        }
    }

    public static function colorizestatus($status, $prefix = '', $suffix = ''){

        $colors = config('jayon.status_colors');
        if($status == '' || !in_array($status, array_keys($colors))){
            $class = 'brown';
            $status = 'N/A';
        }else{
            $class = $colors[$status];
        }

        $atatus = str_replace('_', ' ', $status);
        $status = $prefix.ucwords($status).$suffix;

        return sprintf('<span class="%s">%s</span>',$class,$status);
    }

    public static function colorizetype($type, $prefix = '', $suffix = ''){

        if($type == 'COD'){
            $class = 'brown';
        }else if($type == 'CCOD'){
            $class = 'maroon';
        }else if($type == 'PS'){
            $class = 'green';
        }else{
            $class = 'red';
            $type = 'DO';
        }

        $type = $prefix.$type.$suffix;

        return sprintf('<span class="%s" style="text-align:center;">%s</span>',$class,$type);
    }


    public static function get_device_color($identifier){

        $col = Device::where('identifier','=',$identifier)->first();

        if($col){
            return $col->color;
        }else{
            return '#FF0000';
        }

    }


}
