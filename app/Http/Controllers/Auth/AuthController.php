<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Creditor;

use App\Helpers\Prefs;

use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'agreeToTerms' => 'required|present'
        ];

        if(isset($data['coName'])){
            $co_validator = [
                'coName' => 'required|max:255',
                'address_1' => 'required|max:255',
                'phone' => 'required|max:255',
                'city' => 'required|max:255',
                'province' => 'required|max:255',
            ];

            $validator = array_merge($validator, $co_validator);

        }

        print_r($validator);

        return Validator::make($data, $validator);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        //print_r($data);
        //die();
        $new_user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'roleId' => $data['roleId'],
            'password' => bcrypt($data['password']),
        ]);

        if(isset($data['coName']) && $new_user){
            Creditor::create([
                'coName'=>$data['coName'],
                'address_1'=>$data['address_1'],
                'address_2'=>$data['address_2'],
                'phone'=>$data['phone'],
                'fax'=>$data['fax'],
                'city'=>$data['city'],
                'province'=>$data['province'],
                'countryOfOrigin'=>$data['countryOfOrigin'],
                'pic'=>$new_user->name,
                'picId'=>$new_user->_id,
                'picName'=>$new_user->name
            ]);
        }

        return $new_user;

    }

    protected function authenticated($user)
    {
        print_r($user->toArray());
        //die();
        if($user->roleId == Prefs::getRoleId('Member')) {
            return redirect('/member/profile');
        }

        if($user->roleId == Prefs::getRoleId('Creditor')) {
            return redirect('/creditor/profile');
        }

        return redirect('/');
    }
}
