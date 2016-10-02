<?php
namespace App\Http\Controllers;

use App\Http\Controllers\AdminController;

use App\Models\Shipment;
use App\Models\Deliveryfee;
use App\Models\Codsurcharge;
use App\Models\Printsession;
use App\Models\Application;
use App\Models\Buyer;
use App\Models\Member;
use App\Models\History;
use App\Models\Shipmentlog;

use App\Helpers\Prefs;

use Creitive\Breadcrumbs\Breadcrumbs;

use Config;

use Auth;
use Event;
use View;
use Input;
use Request;
use Response;
use Mongomodel;
use \MongoRegex;
use \MongoDate;
use DB;
use HTML;

class DashboardController extends AdminController {

    public function __construct()
    {
        parent::__construct();

                $cname = substr(strrchr(get_class($this), '\\'), 1);
        $this->controller_name = str_replace('Controller', '', $cname);

    }


    public function getIndex()
    {


        $this->title = 'Dashboard';

        if(Auth::check()){
            if( Auth::user()->roleId == Prefs::getRoleId( 'Member')){
                return view('dashboard.member')
                    ->with('crumb',$this->crumb)
                    ->with('title',$this->title);
            }else if( Auth::user()->roleId == Prefs::getRoleId( 'Creditor')){
                return view('dashboard.creditor')
                    ->with('crumb',$this->crumb)
                    ->with('title',$this->title);
            }else{
                return view('dashboard.admin')
                    ->with('crumb',$this->crumb)
                    ->with('title',$this->title);
            }
        }else{
            return redirect('login');
        }
    }


}
