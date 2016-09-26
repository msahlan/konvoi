<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Fcm;

use App\Helpers\Prefs;


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
use \MongoId;
use \MongoInt32;
use DB;
use HTML;
use Excel;
use Validator;

class FcmController extends Controller {
    public $controller_name = '';

    public function  __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        //$this->model = "Member";
        $this->controller_name = strtolower( str_replace('Controller', '', get_class()) );

    }

    public function postRegister()
    {
        $token = Request::input('Token');
        $prevToken = Request::input('prevToken');

        if($prevToken == 'new'){
            $fcm = new Fcm();

            $fcm->token = $token;
            $fcm->prevToken = $prevToken;
            $fcm->save();
        }else{

            $efcm = Fcm::where('token','=', $prevToken)->first();

            if($efcm ){
                $efcm->token = $token;
                $efcm->prevToken = $prevToken;
                $efcm->save();

            }else{
                $fcm = new Fcm();
                $fcm->token = $token;
                $fcm->prevToken = $prevToken;
                $fcm->save();
            }

        }


    }

}
