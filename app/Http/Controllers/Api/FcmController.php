<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;

class FcmController extends \Controller {
    public $controller_name = '';

    public function  __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        //$this->model = "Member";
        $this->controller_name = strtolower( str_replace('Controller', '', get_class()) );

    }

    public function postRegister()
    {
        $token = \Input::get('Token');
        $prevToken = \Input::get('prevToken');

        if($prevToken == 'new'){
            $fcm = new \Fcm();

            $fcm->token = $token;
            $fcm->prevToken = $prevToken;
            $fcm->save();
        }else{

            $efcm = \Fcm::where('token','=', $prevToken)->first();

            if($efcm ){
                $efcm->token = $token;
                $efcm->prevToken = $prevToken;
                $efcm->save();

            }else{
                $fcm = new \Fcm();
                $fcm->token = $token;
                $fcm->prevToken = $prevToken;
                $fcm->save();
            }

        }


    }

}
