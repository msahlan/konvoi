<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Creditor;

use App\Helpers\Prefs;

use Validator;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class FrontController extends Controller
{
    public function __construct()
    {
        //$this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    public function getIndex()
    {
    	return view('front.index');
    }

}