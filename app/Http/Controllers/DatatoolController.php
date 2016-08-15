<?php

class DatatoolController extends AdminController {

    public function __construct()
    {
        parent::__construct();

                $cname = substr(strrchr(get_class($this), '\\'), 1);
        $this->controller_name = str_replace('Controller', '', $cname);


        //$this->crumb = new Breadcrumb();
        //$this->crumb->append('Home','left',true);
        //$this->crumb->append(strtolower($this->controller_name));

        $this->model = new Shipment();
        //$this->model = DB::collection('documents');
        $this->title = 'Data Tool';

    }

    public function getIndex()
    {
        set_time_limit(0);

        $this->title = 'Data Tool';

        $this->place_action = 'none';

        $this->show_select = false;

        $this->can_add = false;

        $this->is_report = true;

        $this->crumb->addCrumb('Data Tool',url( strtolower($this->controller_name) ));

        $this->additional_filter = View::make(strtolower($this->controller_name).'.addfilter')->with('submit_url','datatool')->render();


        $db = config('lundin.main_db');


        if( is_null($this->report_filter_input)){
            $in = Input::all();
        }else{
            $in = $this->report_filter_input;
        }

        //$company = $in['acc-company'];

//device=&courier=&logistic=&date-from=2015-10-24

        //print_r($in);

        $period_from = isset($in['date-from'])?$in['date-from']:'';
        $period_to = isset($in['date-to'])?$in['date-to']:'';

        $device = isset($in['device'])?$in['device']:'';
        $courier = isset($in['courier'])?$in['courier']:'';

        $merchant = isset($in['merchant'])?$in['merchant']:'';
        $logistic = isset($in['logistic'])?$in['logistic']:'';

        $city = isset($in['city'])?$in['city']:'';
        $zone = isset($in['zone'])?$in['zone']:'';

        $pendingcount = isset($in['pending-count'])?$in['pending-count']:'';

        $timebase = isset($in['time-base'])?$in['time-base']:'delivery_order_active.created';

        $status = isset($in['status'])?$in['status']:'';
        $courierstatus = isset($in['courier-status'])?$in['courier-status']:'';
        $pickupstatus = isset($in['pickup-status'])?$in['pickup-status']:'';

        if($period_to == '' || is_null($period_to) ){
            $period_to = date('Y-m-d',time());
        }

        if($period_from == '' || is_null($period_from) ){
            $period_from = date('Y-m-d',time());
        }

        $this->def_order_by = 'TRANS_DATETIME';
        $this->def_order_dir = 'DESC';
        $this->place_action = 'none';
        $this->show_select = false;

        /* Start custom queries */

        $mtab = config('jayon.assigned_delivery_table');

        $model = $this->model;

        $model = $model->select('assignment_date','ordertime','pickuptime','deliverytime','delivery_note','pending_count','recipient_name','delivery_id',$mtab.'.merchant_id as merchant_id','cod_bearer','delivery_bearer','buyer_name','buyerdeliveryzone','buyerdeliverycity','c.fullname as courier_name', $mtab.'.phone', $mtab.'.mobile1',$mtab.'.mobile2','merchant_trans_id','fulfillment_code','m.merchantname as merchant_name','m.fullname as fullname','a.application_name as app_name','a.domain as domain ','delivery_type','shipping_address','status','pickup_status','warehouse_status','cod_cost','delivery_cost','total_price','total_tax','total_discount')
            ->leftJoin('members as m',config('jayon.incoming_delivery_table').'.merchant_id','=','m.id')
            ->leftJoin('applications as a',config('jayon.assigned_delivery_table').'.application_id','=','a.id')
            ->leftJoin('devices as d',config('jayon.assigned_delivery_table').'.device_id','=','d.id')
            ->leftJoin('couriers as c',config('jayon.assigned_delivery_table').'.courier_id','=','c.id');
        /*
        $model = $model
            ->where(function($q){
                $q->where('status',config('jayon.trans_status_mobile_delivered'))
                    //->where('status',config('jayon.trans_status_admin_courierassigned'))
                    ->orWhere('status',config('jayon.trans_status_new'))
                    ->orWhere('status',config('jayon.trans_status_rescheduled'))
                    ->orWhere('status',config('jayon.trans_status_mobile_return'));

            });
        */

        if($pendingcount == '' || is_null($pendingcount) ){

        }else{
            if($pendingcount == 4){
                $model = $model->where('pending_count','>' ,$pendingcount);
            }else{
                $model = $model->where('pending_count','=',$pendingcount);
            }
        }

        if($status == '' || is_null($status) ){
            //$status = config('jex.data_tool_default_status');
        }else{
            $status = explode(',', $status);
            $model = $model->where(function($qs) use($status){
               $qs->whereIn('status', $status);
            });
        }

        if($courierstatus == '' || is_null($courierstatus) ){

        }else{
            $courierstatus = explode(',', $courierstatus);
            $model = $model->whereIn('courier_status', $courierstatus);
        }

        if($pickupstatus == '' || is_null($pickupstatus) ){

        }else{
            $pickupstatus = explode(',', $pickupstatus);
            $model = $model->whereIn('pickup_status', $pickupstatus);
        }

        /*
        if(empty($status)){
            $exstatus = config('jayon.devmanifest_default_excl_status');

            if(!empty($exstatus)){
                $model = $model->whereNotIn('status', $exstatus);
            }
        }else{
            $model = $model->whereIn('status', $status);
        }



        if(empty($courierstatus)){
            $excrstatus = config('jayon.devmanifest_default_excl_courier_status');

            if(!empty($excrstatus)){
                $model = $model->whereNotIn('courier_status', $excrstatus);
            }
        }else{
            $model = $model->whereIn('courier_status', $courierstatus);
        }
        */

        if($period_from == '' || is_null($period_from) ){
            $datefrom = date( 'Y-m-d 00:00:00', time() );
            $dateto = date( 'Y-m-d 23:59:59', time() );

        }else{

            $datefrom = date( 'Y-m-d 00:00:00', strtotime($period_from) );
            $dateto = date( 'Y-m-d 23:59:59', strtotime($period_to) );

            $model = $model->where(function($q) use($datefrom,$dateto, $timebase){
                $q->whereBetween($timebase,array($datefrom,$dateto));
            });

        }

        if($merchant == '' || is_null($merchant) ){

        }else{
            $model = $model->where(config('jayon.incoming_delivery_table').'.merchant_id','=', $merchant);
        }

        if($device == '' || is_null($device) ){

        }else{
            $model = $model->where('device_id','=', $device);
        }

        if($courier == '' || is_null($courier) ){

        }else{
            $model = $model->where('courier_id','=', $courier);
        }

        if($logistic == '' || is_null($logistic) ){

        }else{
            $model = $model->where('logistic','=', $logistic);
        }

        if($city == '' || is_null($city) ){

        }else{
            $model = $model->where('buyerdeliverycity','like', '%'.$city.'%');
        }

        if($zone == '' || is_null($zone) ){

        }else{
            $model = $model->where('buyerdeliveryzone','like', '%'.$zone.'%');
        }


        $actualresult = $model->orderBy($timebase, 'desc')->get();

        $tattrs = array('width'=>'100%','class'=>'table table-bordered table-striped');

        $thead = array();

        $thead[] = array(
                array('value'=>'No.','attr'=>''),
                array('value'=>'TOKO ONLINE','attr'=>''),
                array('value'=>'Type','attr'=>''),
                array('value'=>'Tgl Upload','attr'=>''),
                array('value'=>'Tgl Pick Up','attr'=>''),
                array('value'=>'Tgl Penugasan Terakhir <br />( Last Assignment Date )','attr'=>''),
                array('value'=>'Tgl Kirim','attr'=>''),
                array('value'=>'Pick Up -> Diterima','attr'=>''),
                array('value'=>'Kirim -> Diterima','attr'=>''),
                array('value'=>'Tgl Diterima','attr'=>''),
                array('value'=>'Status','attr'=>''),
                array('value'=>'Pending','attr'=>''),
                array('value'=>'Catatan','attr'=>''),
                array('value'=>'ALAMAT','attr'=>''),
                array('value'=>'Kecamatan','attr'=>''),
                array('value'=>'Kota','attr'=>''),
                array('value'=>'Delivery ID','attr'=>''),
                array('value'=>'No Kode Penjualan Toko','attr'=>''),
                array('value'=>'Fulfillment ID','attr'=>'')
            );

        /*

        $thead[] = array(
                array('value'=>'Mohon tunjukkan kartu identitas untuk di foto sebagai bagian bukti penerimaan','attr'=>'style="text-align:center;" colspan="13"'),
                array('value'=>'TANDA TANGAN','attr'=>''),
                array('value'=>'NAMA','attr'=>'class="bold center" style="width:50px" '),
            );
        */
        $seq = 1;
        $total_billing = 0;
        $total_delivery = 0;
        $total_cod = 0;

        $d = 0;
        $gt = 0;

        $lastdate = '';

        $courier_name = '';

        $order2assigndays = 0;
        $assign2deliverydays = 0;
        $order2deliverydays = 0;
        $pickup2deliverydays = 0;

        $csv_data = array();

        $dids = array();

        foreach($actualresult as $ar){
            $dids[] = $ar->delivery_id;
        }

        $details = Deliverylog::whereIn('delivery_id',$dids)
                        /*
                        ->where(function($q){
                            $q->where('status',config('jayon.trans_status_mobile_delivered'))
                                ->orWhere('status',config('jayon.trans_status_admin_courierassigned'))
                                ->orWhere('status',config('jayon.trans_status_new'))
                                ->orWhere('status',config('jayon.trans_status_rescheduled'))
                                ->orWhere('status',config('jayon.trans_status_mobile_return'));
                        })
                        */

                        ->where(function($q){
                            $q->where('notes','!=','')
                                ->orWhere('req_note','!=','');
                        })
                        ->orderBy('timestamp','desc')
                        ->get()->toArray();

        $dlist = array();
        foreach ($details as $dt) {
            $dlist[$dt['delivery_id']][] = $dt;
        }

        $denotes = Deliverynote::whereIn('deliveryId',$dids)
                    ->orderBy('mtimestamp','desc')
                    ->get()->toArray();

        foreach ($denotes as $dt) {
            $dlist[$dt['deliveryId']][] = $dt;
        }


        $tabdata = array();

        $cntcod = 0;
        $cntccod = 0;
        $cntdo = 0;
        $cntps = 0;
        $return = 0;

        $valid_pickups = 0;

        foreach ($actualresult as $r) {

            $details = (isset($dlist[$r->delivery_id]))?$dlist[$r->delivery_id]:array();

            $courier_name = $r->courier_name;
            $total = str_replace(array(',','.'), '', $r->total_price);
            $dsc = str_replace(array(',','.'), '', $r->total_discount);
            $tax = str_replace(array(',','.'), '',$r->total_tax);
            $dc = str_replace(array(',','.'), '',$r->delivery_cost);
            $cod = str_replace(array(',','.'), '',$r->cod_cost);

            $total = (int)$total;
            $dsc = (int)$dsc;
            $tax = (int)$tax;
            $dc = (int)$dc;
            $cod = (int)$cod;

            $payable = 0;

            $otime = date('Y-m-d',strtotime($r->ordertime));
            $ptime = date('Y-m-d',strtotime($r->pickuptime));
            $dtime = date('Y-m-d',strtotime($r->deliverytime));

            $ordertime = new DateTime($otime);

            //print_r($details);

            $notes = '';
            $event_seq = 0;
            $first_assignment = false;

            foreach($details as $d )
            {
                $n = '';

                if(isset($d['api_event'])){
                    if($d['api_event'] == 'admin_change_status'){
                        $n = $d['req_note'];
                    }else{
                        if($d['notes'] != ''){
                            $n = $d['notes'];
                        }
                    }
                    if($n != '' && $d['status'] != 'syncnote'){
                        $notes .= $d['timestamp'].'<br />';
                        $notes .= '<b>'.$d['status'].'</b><br />';
                        $notes .= $n.'<br /><br />';
                    }

                    if($d['status'] == config('jayon.trans_status_admin_courierassigned')){
                        if($event_seq > 0){
                            $first_assignment = $d;
                        }
                    }

                }else{

                    if($d['note'] != ''){
                        $n = $d['note'];
                    }

                    if($n != ''){
                        $notes .= $d['datetimestamp'].'<br />';
                        $notes .= '<b>'.$d['status'].'</b><br />';
                        $notes .= $n.'<br /><br />';
                    }

                }


                $event_seq++;
            }


            if($first_assignment){
                $assignment_date = new DateTime($first_assignment['timestamp']);
                $assignment_date->add(new DateInterval('P1D'));
            }else{
                $assignment_date = new DateTime($r->assignment_date);
            }

            $deliverytime = new DateTime($dtime);

            $pickuptime = new DateTime($ptime);

            $order2assign = $ordertime->diff($assignment_date);

            $assign2delivery = $assignment_date->diff($deliverytime);

            $pickup2delivery = $pickuptime->diff($deliverytime);

            $order2delivery = $ordertime->diff($deliverytime);

            if(is_null($r->deliverytime) || $r->deliverytime == '' || $r->deliverytime == '0000-00-00 00:00:00'){
                $assign2delivery->d = 0;
                $order2delivery->d = 0;
                $pickup2delivery->d = 0;
                $order2assigndays += (int)$order2assign->d ;
            }else{
                $order2assigndays += (int)$order2assign->d ;
                $assign2deliverydays += (int)$assign2delivery->d ;
                $order2deliverydays += (int)$order2delivery->d;
            }

            if(is_null($pickuptime) || $pickuptime == '' || $r->pickuptime == '' || $r->pickuptime == '0000-00-00 00:00:00' ){
                //$r->pickuptime = $assignment_date->add(new DateInterval('P1D'))->format('Y-m-d');

                //$pickuptime = new DateTime($r->pickuptime);

                //$pickup2delivery = $pickuptime->diff($deliverytime);

                $pickup2delivery->d = 0;
                $pickup2deliverydays += (int)$pickup2delivery->d;
            }else{
                //$valid_pickups++;
                $pickup2deliverydays += (int)$pickup2delivery->d;
                $valid_pickups++;
            }


            $d = 0;
            $gt = 0;



            $cntcod = ($r->delivery_type == 'COD')?($cntcod+1):$cntcod;
            $cntccod = ($r->delivery_type == 'CCOD')?($cntccod+1):$cntccod;
            $cntdo = ($r->delivery_type == 'Delivery Only')?($cntdo+1):$cntdo;
            $cntps = ($r->delivery_type == 'PS')?($cntps+1):$cntps;
            $cntreturn = ($r->status == 'returned')?($return+1):$return;

            //print_r($details);

            /*
            $notes = '';
            foreach($details as $d )
            {
                $n = '';
                $admin = true;
                if($d['api_event'] == 'admin_change_status'){
                    $n = $d['req_note'];
                }else{
                    if($d['notes'] != ''){
                        $n = $d['notes'];
                    }
                    $admin = false;
                }

                $isadmin = ($admin)?' [admin]':'';
                if($n != '' && $d['status'] != 'syncnote'){
                    $notes .= $d['timestamp'].$isadmin.'<br />';
                    $notes .= '<b>'.$d['status'].'</b><br />';
                    $notes .= $n.'<br />';
                }

            }
            */
            /*
            if($gt == 0 ){
                if($total > 0 && $payable)
                $gt = $total;
            }

            $payable = $gt;

            $total_delivery += (double)$dc;
            $total_cod += (double)$cod;
            $total_billing += (double)$payable;

            $db = '';
            if($r->delivery_bearer == 'merchant'){
                $dc = 0;
                $db = 'M';
            }else{
                $db = 'B';
            }

            //force all DO to zero

            $cb = '';
            if($r->cod_bearer == 'merchant'){
                $cod = 0;
                $cb = 'M';
            }else{
                $cb = 'B';
            }

            $codclass = '';

            if($r->delivery_type == 'COD' || $r->delivery_type == 'CCOD'){
                $chg = ($gt - $dsc) + $tax + $dc + $cod;

                //$chg = $gt + $dc + $cod;

                $codclass = 'cod';

            }else{
                $dc = 0;
                $cod = 0;
                $chg = $dc;
            }


            $fcode = ($r->fulfillment_code == '')?'':'<hr />'.$r->fulfillment_code;

            $phone_dupe = ($r->same_phone == 1)?'class="dupe"':'';
            $email_dupe = ($r->same_email == 1)?'class="dupe"':'';
            */



            $tabdata[] = array(

                    array('value'=>$seq,'attr'=>''),
                    array('value'=>$r->merchant_name,'attr'=>''),
                    array('value'=>$r->delivery_type,'attr'=>''),
                    array('value'=>$r->ordertime,'attr'=>''),
                    array('value'=>$r->pickuptime,'attr'=>''),
                    array('value'=>$r->assignment_date,'attr'=>''),
                    array('value'=>$assignment_date->format('Y-m-d'),'attr'=>''),
                    array('value'=>$pickup2delivery->d,'attr'=>''),
                    array('value'=>$assign2delivery->d,'attr'=>''),
                    array('value'=>$r->deliverytime,'attr'=>''),
                    array('value'=>$r->status,'attr'=>''),
                    array('value'=>$r->pending_count,'attr'=>''),
                    array('value'=>$notes,'attr'=>''),
                    array('value'=>$r->recipient_name.' | '.str_replace(array(",",'"',"\n","\r"), '', $r->shipping_address ).' '.$this->split_phone($r->phone).' '.$this->split_phone($r->mobile1).' '.$this->split_phone($r->mobile2),'attr'=>''),
                    array('value'=>$r->buyerdeliveryzone,'attr'=>''),
                    array('value'=>$r->buyerdeliverycity,'attr'=>''),
                    array('value'=>$r->delivery_id,'attr'=>''),
                    array('value'=>$this->hide_trx($r->merchant_trans_id),'attr'=>''),
                    array('value'=>$r->fulfillment_code,'attr'=>'')
                );

            $seq++;
        }

        $valid_pickups = ($valid_pickups > 0)?$valid_pickups:1;

            $avgdata = array(
                    array('value'=>'Rata-rata<br />( dlm satuan hari )','attr'=>'colspan="7"'),
                    array('value'=>number_format($pickup2deliverydays / $valid_pickups, 2, ',','.' ),'attr'=>'style="font-size:18px;font-weight:bold;"'),
                    array('value'=>number_format($assign2deliverydays / $seq, 2, ',','.' ),'attr'=>'style="font-size:18px;font-weight:bold;"'),
                    array('value'=>'','attr'=>'colspan="11"'),
                );

            array_unshift($tabdata, $avgdata);
            array_push($tabdata, $avgdata);

        $mtable = new HtmlTable($tabdata,$tattrs,$thead);

        $tables[] = $mtable->build();

        $this->table_raw = $tables;

        $report_header_data = array(
                'cod'=>$cntcod,
                'ccod'=>$cntccod,
                'do'=>$cntdo,
                'ps'=>$cntps,
                'return'=>$return,
                'avg'=>number_format($assign2deliverydays / $seq, 2, ',','.' )
        );

        if($this->print == true || $this->pdf == true){
            return array('tables'=>$tables,'report_header_data'=>$report_header_data);
        }else{
            return parent::reportPageGenerator();
        }


    }

    public function getStatic()
    {

    }

    public function getPrint()
    {

        $this->print = true;

        $tables = $this->getIndex();

        $this->table_raw = $tables['tables'];
        $this->report_header_data = $tables['report_header_data'];


        $this->report_entity = false;
        $sequencer = new Sequence();
        $doc_number = $sequencer->getNewId('devmanifest');

        $this->additional_filter = View::make(strtolower($this->controller_name).'.addhead')
                                            ->with('doc_number',$doc_number)
                                            ->with('report_header_data',$this->report_header_data)
                                            ->render();

        $this->report_file_name = 'MDL-'.str_pad($doc_number, 5, '0', STR_PAD_LEFT).'.html';
        $this->report_file_path = realpath('storage/docs').'/devmanifest/';

        $this->title = 'DELIVERY TIME';

        $this->report_type = 'deliverytime';

        return parent::printReport();
    }

    public function getGenpdf()
    {

        $this->pdf = true;

        $tables = $this->getIndex();

        $this->table_raw = $tables;

        $this->report_entity = false;
        $sequencer = new Sequence();
        $doc_number = $sequencer->getNewId('devmanifest');

        $this->additional_filter = View::make(strtolower($this->controller_name).'.addhead')
                                            ->with('doc_number',$doc_number)
                                            ->render();

        $this->report_file_name = 'MDL-'.str_pad($doc_number, 5, '0', STR_PAD_LEFT).'.html';
        $this->report_file_path = realpath('storage/docs').'/devmanifest/';

        $this->title = 'DELIVERY TIME';

        $this->report_type = 'deliverytime';

        return parent::printReport();
    }

    public function SQL_make_join($model)
    {
        //$model->with('coa');

        //PERIOD',TRANS_DATETIME,VCHR_NUM,ACC_DESCR,DESCRIPTN',TREFERENCE',CONV_CODE,AMOUNT',AMOUNT',DESCRIPTN'

        $model = $model->select('j10_a_salfldg.*','j10_acnt.DESCR as ACC_DESCR')
            ->leftJoin('j10_acnt', 'j10_a_salfldg.ACCNT_CODE', '=', 'j10_acnt.ACNT_CODE' );
        return $model;
    }

    public function SQL_additional_query($model)
    {
        $in = Request::input();

        $period_from = Request::input('acc-period-from');
        $period_to = Request::input('acc-period-to');

        $db = config('lundin.main_db');

        $company = Request::input('acc-company');

        $company = strtolower($company);

        $txtab = config('jayon.incoming_delivery_table');

        $model = $model->where(function($query){
                            $query->where('bucket','=',config('jayon.bucket_tracker'))
                                ->where(function($qs){
                                    $qs->where('logistic_type','=','external')
                                        ->orWhere(function($qx){
                                                $qx->where('logistic_type','=','internal')
                                                    ->where(function($qz){
                                                        $qz->where('status','=', config('jayon.trans_status_admin_courierassigned') )
                                                            ->orWhere('status','=', config('jayon.trans_status_mobile_pickedup') )
                                                            ->orWhere('status','=', config('jayon.trans_status_mobile_enroute') )
                                                            ->orWhere(function($qx){
                                                                $qx->where('status', config('jayon.trans_status_new'))
                                                                    ->where('pending_count', '>', 0);
                                                            });
                                                    });

                                        });
                                });


        })
        ->orderBy('assignment_date');

        return $model;

    }

    public function SQL_before_paging($model)
    {
        $m_original_amount = clone($model);
        $m_base_amount = clone($model);

        $aux['total_data_base'] = $m_base_amount->sum('OTHER_AMT');
        $aux['total_data_converted'] = $m_original_amount->sum('AMOUNT');

        //$this->aux_data = $aux;

        return $aux;
        //print_r($this->aux_data);

    }

    public function rows_post_process($rows, $aux = null){

        //print_r($this->aux_data);

        $total_base = 0;
        $total_converted = 0;
        $end = 0;

        $br = array_fill(0, $this->column_count(), '');


        $nrows = array();

        $subhead1 = '';
        $subhead2 = '';
        $subhead3 = '';

        $seq = 0;

        $subamount1 = 0;
        $subamount2 = 0;

        if(count($rows) > 0){

            for($i = 0; $i < count($rows);$i++){

                //print_r($rows[$i]['extra']);

                if($subhead1 == '' || $subhead1 != $rows[$i][1] || $subhead2 != $rows[$i][4] ){

                    $headline = $br;
                    if($subhead1 != $rows[$i][1]){
                        $headline[1] = '<b>'.$rows[$i]['extra']['PERIOD'].'</b>';
                    }else{
                        $headline[1] = '';
                    }

                    $headline[4] = '<b>'.$rows[$i]['extra']['ACCNT_CODE'].'</b>';
                    $headline['extra']['rowclass'] = 'row-underline';

                    if($subhead1 != ''){
                        $amtline = $br;
                        $amtline[8] = '<b>'.Ks::idr($subamount1).'</b>';
                        $amtline[10] = '<b>'.Ks::idr($subamount2).'</b>';
                        $amtline['extra']['rowclass'] = 'row-doubleunderline row-overline';

                        $nrows[] = $amtline;
                        $subamount1 = 0;
                        $subamount2 = 0;
                    }

                    $subamount1 += $rows[$i]['extra']['OTHER_AMT'];
                    $subamount2 += $rows[$i]['extra']['AMOUNT'];

                    $nrows[] = $headline;

                    $seq = 1;
                    $rows[$i][0] = $seq;

                    $rows[$i][8] = ($rows[$i]['extra']['CONV_CODE'] == 'IDR')?Ks::idr($rows[$i][8]):'';
                    $rows[$i][9] = ($rows[$i]['extra']['CONV_CODE'] == 'IDR')?Ks::dec2($rows[$i][9]):'';
                    $rows[$i][10] = Ks::usd($rows[$i][10]);

                    $nrows[] = $rows[$i];
                }else{
                    $seq++;
                    $rows[$i][0] = $seq;

                    $rows[$i][8] = ($rows[$i]['extra']['CONV_CODE'] == 'IDR')?Ks::idr($rows[$i][8]):'';
                    $rows[$i][9] = ($rows[$i]['extra']['CONV_CODE'] == 'IDR')?Ks::dec2($rows[$i][9]):'';
                    $rows[$i][10] = Ks::usd($rows[$i][10]);

                    $nrows[] = $rows[$i];


                }

                $total_base += doubleval( $rows[$i][8] );
                $total_converted += doubleval($rows[$i][10]);
                $end = $i;

                $subhead1 = $rows[$i][1];
                $subhead2 = $rows[$i][4];
            }

            // show total Page
            if($this->column_count() > 0){

                $tb = $br;
                $tb[1] = 'Total Page';
                $tb[8] = Ks::idr($total_base);
                $tb[10] = Ks::usd($total_converted);

                $nrows[] = $tb;

                if(!is_null($this->aux_data)){
                    $td = $br;
                    $td[1] = 'Total';
                    $td[8] = Ks::idr($aux['total_data_base']);
                    $td[10] = Ks::usd($aux['total_data_converted']);
                    $nrows[] = $td;
                }

            }

            return $nrows;

        }else{

            return $rows;

        }


        // show total queried


    }


    public function beforeSave($data)
    {

        if( isset($data['file_id']) && count($data['file_id'])){

            $mediaindex = 0;

            for($i = 0 ; $i < count($data['thumbnail_url']);$i++ ){

                $index = $mediaindex;

                $data['files'][ $data['file_id'][$i] ]['ns'] = $data['ns'][$i];
                $data['files'][ $data['file_id'][$i] ]['role'] = $data['role'][$i];
                $data['files'][ $data['file_id'][$i] ]['thumbnail_url'] = $data['thumbnail_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['large_url'] = $data['large_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['medium_url'] = $data['medium_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['full_url'] = $data['full_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_type'] = $data['delete_type'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_url'] = $data['delete_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['filename'] = $data['filename'][$i];
                $data['files'][ $data['file_id'][$i] ]['filesize'] = $data['filesize'][$i];
                $data['files'][ $data['file_id'][$i] ]['temp_dir'] = $data['temp_dir'][$i];
                $data['files'][ $data['file_id'][$i] ]['filetype'] = $data['filetype'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_image'] = $data['is_image'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_audio'] = $data['is_audio'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_video'] = $data['is_video'][$i];
                $data['files'][ $data['file_id'][$i] ]['fileurl'] = $data['fileurl'][$i];
                $data['files'][ $data['file_id'][$i] ]['file_id'] = $data['file_id'][$i];
                $data['files'][ $data['file_id'][$i] ]['sequence'] = $mediaindex;

                $mediaindex++;

                $data['defaultpic'] = $data['file_id'][0];
                $data['defaultpictures'] = $data['files'][$data['file_id'][0]];

            }

        }else{

            $data['defaultpic'] = '';
            $data['defaultpictures'] = '';
        }

        $cats = Prefs::getShopCategory()->shopcatToSelection('slug', 'name', false);
        $data['shopcategory'] = $cats[$data['shopcategoryLink']];

            $data['shortcode'] = str_random(5);

        return $data;
    }

    public function beforeUpdate($id,$data)
    {

        if( isset($data['file_id']) && count($data['file_id'])){

            $mediaindex = 0;

            for($i = 0 ; $i < count($data['thumbnail_url']);$i++ ){

                $index = $mediaindex;

                $data['files'][ $data['file_id'][$i] ]['ns'] = $data['ns'][$i];
                $data['files'][ $data['file_id'][$i] ]['role'] = $data['role'][$i];
                $data['files'][ $data['file_id'][$i] ]['thumbnail_url'] = $data['thumbnail_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['large_url'] = $data['large_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['medium_url'] = $data['medium_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['full_url'] = $data['full_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_type'] = $data['delete_type'][$i];
                $data['files'][ $data['file_id'][$i] ]['delete_url'] = $data['delete_url'][$i];
                $data['files'][ $data['file_id'][$i] ]['filename'] = $data['filename'][$i];
                $data['files'][ $data['file_id'][$i] ]['filesize'] = $data['filesize'][$i];
                $data['files'][ $data['file_id'][$i] ]['temp_dir'] = $data['temp_dir'][$i];
                $data['files'][ $data['file_id'][$i] ]['filetype'] = $data['filetype'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_image'] = $data['is_image'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_audio'] = $data['is_audio'][$i];
                $data['files'][ $data['file_id'][$i] ]['is_video'] = $data['is_video'][$i];
                $data['files'][ $data['file_id'][$i] ]['fileurl'] = $data['fileurl'][$i];
                $data['files'][ $data['file_id'][$i] ]['file_id'] = $data['file_id'][$i];
                $data['files'][ $data['file_id'][$i] ]['sequence'] = $mediaindex;

                $mediaindex++;

                $data['defaultpic'] = $data['file_id'][0];
                $data['defaultpictures'] = $data['files'][$data['file_id'][0]];

            }

        }else{

            $data['defaultpic'] = '';
            $data['defaultpictures'] = '';
        }

        if(!isset($data['shortcode']) || $data['shortcode'] == ''){
            $data['shortcode'] = str_random(5);
        }

        $cats = Prefs::getShopCategory()->shopcatToSelection('slug', 'name', false);
        $data['shopcategory'] = $cats[$data['shopcategoryLink']];


        return $data;
    }

    public function beforeUpdateForm($population)
    {
        //print_r($population);
        //exit();

        return $population;
    }

    public function afterSave($data)
    {

        $hdata = array();
        $hdata['historyTimestamp'] = new MongoDate();
        $hdata['historyAction'] = 'new';
        $hdata['historySequence'] = 0;
        $hdata['historyObjectType'] = 'asset';
        $hdata['historyObject'] = $data;
        History::insert($hdata);

        return $data;
    }

    public function afterUpdate($id,$data = null)
    {
        $data['_id'] = new MongoId($id);


        $hdata = array();
        $hdata['historyTimestamp'] = new MongoDate();
        $hdata['historyAction'] = 'update';
        $hdata['historySequence'] = 1;
        $hdata['historyObjectType'] = 'asset';
        $hdata['historyObject'] = $data;
        History::insert($hdata);


        return $id;
    }


    public function postAdd($data = null)
    {
        $this->validator = array(
            'shopDescription' => 'required'
        );

        return parent::postAdd($data);
    }

    public function postEdit($id,$data = null)
    {
        $this->validator = array(
            'shopDescription' => 'required'
        );

        //exit();

        return parent::postEdit($id,$data);
    }

    public function postDlxl()
    {
        set_time_limit(0);

        $this->report_filter_input = Input::all();

        //print_r($this->report_filter_input);

        $this->print = true;

        $table = $this->getIndex();

        //print_r($table);

        //$view = View::make('print.xls')->with('tables',$table['tables'])->render();

        //print $view;

        $this->export_output_fields = $table;

        return parent::postTabletoxls();

    }

    public function getImport(){

        $this->importkey = 'SKU';

        return parent::getImport();
    }

    public function postUploadimport()
    {
        $this->importkey = 'SKU';

        return parent::postUploadimport();
    }

    public function beforeImportCommit($data)
    {
        $defaults = array();

        $files = array();

        // set new sequential ID


        $data['priceRegular'] = new MongoInt32($data['priceRegular']);

        $data['thumbnail_url'] = array();
        $data['large_url'] = array();
        $data['medium_url'] = array();
        $data['full_url'] = array();
        $data['delete_type'] = array();
        $data['delete_url'] = array();
        $data['filename'] = array();
        $data['filesize'] = array();
        $data['temp_dir'] = array();
        $data['filetype'] = array();
        $data['fileurl'] = array();
        $data['file_id'] = array();
        $data['caption'] = array();

        $data['defaultpic'] = '';
        $data['brchead'] = '';
        $data['brc1'] = '';
        $data['brc2'] = '';
        $data['brc3'] = '';


        $data['defaultpictures'] = array();
        $data['files'] = array();

        return $data;
    }

    public function postRack()
    {
        $locationId = Request::input('loc');
        if($locationId == ''){
            $racks = Assets::getRack()->RackToSelection('_id','SKU',true);
        }else{
            $racks = Assets::getRack(array('locationId'=>$locationId))->RackToSelection('_id','SKU',true);
        }

        $options = Assets::getRack(array('locationId'=>$locationId));

        return Response::json(array('result'=>'OK','html'=>$racks, 'options'=>$options ));
    }

    public function makeActions($data)
    {
        /*
        if(!is_array($data)){
            $d = array();
            foreach( $data as $k->$v ){
                $d[$k]=>$v;
            }
            $data = $d;
        }

        $delete = '<span class="del" id="'.$data['_id'].'" ><i class="fa fa-times-circle"></i> Delete</span>';
        $edit = '<a href="'.url('advertiser/edit/'.$data['_id']).'"><i class="fa fa-edit"></i> Update</a>';
        $dl = '<a href="'.url('brochure/dl/'.$data['_id']).'" target="new"><i class="fa fa-download"></i> Download</a>';
        $print = '<a href="'.url('brochure/print/'.$data['_id']).'" target="new"><i class="fa fa-print"></i> Print</a>';
        $upload = '<span class="upload" id="'.$data['_id'].'" rel="'.$data['SKU'].'" ><i class="fa fa-upload"></i> Upload Picture</span>';
        $inv = '<span class="upinv" id="'.$data['_id'].'" rel="'.$data['SKU'].'" ><i class="fa fa-upload"></i> Update Inventory</span>';
        $stat = '<a href="'.url('stats/merchant/'.$data['id']).'"><i class="fa fa-line-chart"></i> Stats</a>';

        $history = '<a href="'.url('advertiser/history/'.$data['_id']).'"><i class="fa fa-clock-o"></i> History</a>';

        $actions = $stat.'<br />'.$edit.'<br />'.$delete;
        */
        $actions = '';
        return $actions;
    }

    public function accountDesc($data)
    {

        return $data['ACCNT_CODE'];
    }

    public function extractCategory()
    {
        $category = Product::distinct('category')->get()->toArray();
        $cats = array(''=>'All');

        //print_r($category);
        foreach($category as $cat){
            $cats[$cat[0]] = $cat[0];
        }

        return $cats;
    }

    public function splitTag($data){
        $tags = explode(',',$data['tags']);
        if(is_array($tags) && count($tags) > 0 && $data['tags'] != ''){
            $ts = array();
            foreach($tags as $t){
                $ts[] = '<span class="tag">'.$t.'</span>';
            }

            return implode('', $ts);
        }else{
            return $data['tags'];
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

    public function locationName($data){
        if(isset($data['locationId']) && $data['locationId'] != ''){
            $loc = Assets::getLocationDetail($data['locationId']);
            return $loc->name;
        }else{
            return '';
        }

    }

    public function catName($data)
    {
        return $data['shopcategory'];
    }

    public function rackName($data){
        if(isset($data['rackId']) && $data['rackId'] != ''){
            $loc = Assets::getRackDetail($data['rackId']);
            if($loc){
                return $loc->SKU;
            }else{
                return '';
            }
        }else{
            return '';
        }

    }

    public function postSynclegacy(){

        set_time_limit(0);

        $mymerchant = Merchant::where('group_id',4)->get();

        $count = 0;

        foreach($mymerchant->toArray() as $m){

            $member = Member::where('legacyId',$m['id'])->first();

            if($member){

            }else{
                $member = new Member();
            }

            foreach ($m as $k=>$v) {
                $member->{$k} = $v;
            }

            if(!isset($member->status)){
                $member->status = 'inactive';
            }

            if(!isset($member->url)){
                $member->url = '';
            }

            $member->legacyId = new MongoInt32($m['id']);

            $member->roleId = Prefs::getRoleId('Merchant');

            $member->unset('id');

            $member->save();

            $count++;
        }

        return Response::json( array('result'=>'OK', 'count'=>$count ) );

    }

    public function statNumbers($data){
        $datemonth = date('M Y',time());
        $firstday = Carbon::parse('first day of '.$datemonth);
        $lastday = Carbon::parse('last day of '.$datemonth)->addHours(23)->addMinutes(59)->addSeconds(59);

        $qval = array('$gte'=>new MongoDate(strtotime($firstday->toDateTimeString())),'$lte'=>new MongoDate( strtotime($lastday->toDateTimeString()) ));

        $qc = array();

        $qc['adId'] = $data['_id'];

        $qc['clickedAt'] = $qval;

        $qv = array();

        $qv['adId'] = $data['_id'];

        $qv['viewedAt'] = $qval;

        $clicks = Clicklog::whereRaw($qc)->count();

        $views = Viewlog::whereRaw($qv)->count();

        return $clicks.' clicks<br />'.$views.' views';
    }

    public function namePic($data)
    {
        $name = HTML::link('property/view/'.$data['_id'],$data['address']);

        $thumbnail_url = '';

        $ps = config('picture.sizes');


        if(isset($data['files']) && count($data['files'])){
            $glinks = '';

            $gdata = $data['files'][$data['defaultpic']];

            $thumbnail_url = $gdata['thumbnail_url'];
            foreach($data['files'] as $g){
                $g['caption'] = ( isset($g['caption']) && $g['caption'] != '')?$g['caption']:$data['SKU'];
                $g['full_url'] = isset($g['full_url'])?$g['full_url']:$g['fileurl'];
                foreach($ps as $k=>$s){
                    if(isset($g[$k.'_url'])){
                        $glinks .= '<input type="hidden" class="g_'.$data['_id'].'" data-caption="'.$k.'" value="'.$g[$k.'_url'].'" />';
                    }
                }
            }
            if(isset($data['useImage']) && $data['useImage'] == 'linked'){
                $thumbnail_url = $data['extImageURL'];
                $display = HTML::image($thumbnail_url.'?'.time(), $thumbnail_url, array('class'=>'thumbnail img-polaroid','style'=>'cursor:pointer;','id' => $data['_id'])).$glinks;
            }else{
                $display = HTML::image($thumbnail_url.'?'.time(), $thumbnail_url, array('class'=>'thumbnail img-polaroid','style'=>'cursor:pointer;','id' => $data['_id'])).$glinks;
            }
            return $display;
        }else{
            return $data['SKU'];
        }
    }

    public function dispBar($data)

    {
        $display = HTML::image(url('qr/'.urlencode(base64_encode($data['SKU']))), $data['SKU'], array('id' => $data['_id'], 'style'=>'width:100px;height:auto;' ));
        //$display = '<a href="'.url('barcode/dl/'.urlencode($data['SKU'])).'">'.$display.'</a>';
        return $display.'<br />'. '<a href="'.url('asset/detail/'.$data['_id']).'" >'.$data['SKU'].'</a>';
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

    public function getPrintlabel($sessionname, $printparam, $format = 'html' )
    {
        $pr = explode(':',$printparam);

        $columns = $pr[0];
        $resolution = $pr[1];
        $cell_width = $pr[2];
        $cell_height = $pr[3];
        $margin_right = $pr[4];
        $margin_bottom = $pr[5];
        $font_size = $pr[6];
        $code_type = $pr[7];
        $left_offset = $pr[8];
        $top_offset = $pr[9];

        $session = Printsession::find($sessionname)->toArray();
        $labels = Asset::whereIn('_id', $session)->get()->toArray();

        $skus = array();
        foreach($labels as $l){
            $skus[] = $l['SKU'];
        }

        $skus = array_unique($skus);

        $products = Asset::whereIn('SKU',$skus)->get()->toArray();

        $plist = array();
        foreach($products as $product){
            $plist[$product['SKU']] = $product;
        }

        return View::make('asset.printlabel')
            ->with('columns',$columns)
            ->with('resolution',$resolution)
            ->with('cell_width',$cell_width)
            ->with('cell_height',$cell_height)
            ->with('margin_right',$margin_right)
            ->with('margin_bottom',$margin_bottom)
            ->with('font_size',$font_size)
            ->with('code_type',$code_type)
            ->with('left_offset', $left_offset)
            ->with('top_offset', $top_offset)
            ->with('products',$plist)
            ->with('labels', $labels);
    }


    public function getViewpics($id)
    {

    }

    public function hide_trx($trx_id){
        if(preg_match('/^TRX_/', $trx_id)){
            return '';
        }else{
            return $trx_id;
        }
    }

    public function short_did($did){
        $did = explode('-',$did);
        return array_pop($did);
    }

    public function date_did($did){
        $did = explode('-',$did);
        if(count($did) == 3){
            $date_did = $did[1].'-'.$did[2];
        }else{
            $date_did = '';
        }
        return $date_did;
    }

    public function split_phone($phone){
        return str_replace(array('/','#','|'), '<br />', $phone);
    }

    public function updateStock($data){

        //print_r($data);

        $outlets = $data['outlets'];
        $outletNames = $data['outletNames'];
        $addQty = $data['addQty'];
        $adjustQty = $data['adjustQty'];

        unset($data['outlets']);
        unset($data['outletNames']);
        unset($data['addQty']);
        unset($data['adjustQty']);

        for( $i = 0; $i < count($outlets); $i++)
        {

            $su = array(
                    'outletId'=>$outlets[$i],
                    'outletName'=>$outletNames[$i],
                    'productId'=>$data['id'],
                    'SKU'=>$data['SKU'],
                    'productDetail'=>$data,
                    'status'=>'available',
                    'createdDate'=>new MongoDate(),
                    'lastUpdate'=>new MongoDate()
                );

            if($addQty[$i] > 0){
                for($a = 0; $a < $addQty[$i]; $a++){
                    $su['_id'] = str_random(40);
                    Stockunit::insert($su);
                }
            }

            if($adjustQty[$i] > 0){
                $td = Stockunit::where('outletId',$outlets[$i])
                    ->where('productId',$data['id'])
                    ->where('SKU', $data['SKU'])
                    ->where('status','available')
                    ->orderBy('createdDate', 'asc')
                    ->take($adjustQty[$i])
                    ->get();

                foreach($td as $d){
                    $d->status = 'deleted';
                    $d->lastUpdate = new MongoDate();
                    $d->save();
                }
            }
        }


    }

}
