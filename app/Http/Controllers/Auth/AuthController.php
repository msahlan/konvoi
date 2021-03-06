<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Creditor;
use App\Models\Creditaccount;

use App\Helpers\Prefs;

use Validator;
use Auth;
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
            'phone' => 'required|max:255',
            'mobile' => 'max:255',
            'agreeToTerms' => 'required|present',
            'bankCard'=>'required'
        ];

        if(isset($data['coName'])){
            $co_validator = [
                'coName' => 'required|max:255',
                'address_1' => 'required|max:255',
                'phone' => 'required|max:255',
                'mobile' => 'max:255',
                'city' => 'required|max:255',
                'province' => 'required|max:255',
            ];

            unset($validator['bankCard']);

            $validator = array_merge($validator, $co_validator);

        }

        if(isset($data['contractNumber'])){
            $cx_validator = [
                'contractNumber' => 'required|max:255',
                'contractName' => 'required|max:255',
                'creditor' => 'required',
                'Type' => 'required',
                'programName' => 'required',
                'bankCard' => 'required',
                'productDescription' => 'required',
                'dueDate' => 'required',
                'installmentAmt' => 'required',
                'pickupDate' => 'required',
                'pickupAddress' => 'required',
                'province' => 'required',
                'pickupCity' => 'required',
                'pickupDistrict' => 'required',
                'pickupZIP' => 'required',

            ];

            $validator = array_merge($validator, $cx_validator);
        }

        //print_r($validator);

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


        if(isset($data['coName'])){

            $nu = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'mobile' => $data['mobile'],
                'roleId' => $data['roleId'],
                'password' => bcrypt($data['password'])
            ];

        }else{
            $nu = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'mobile' => $data['mobile'],
                'roleId' => $data['roleId'],
                'password' => bcrypt($data['password']),
                'bankCard' => $data['bankCard']
            ];

        }

        //print_r($nu);
        //die();

        $new_user = User::create($nu);

        if(isset($data['coName']) && $new_user){
            Creditor::create([
                'coName'=>$data['coName'],
                'address_1'=>$data['address_1'],
                'address_2'=>$data['address_2'],
                'coPhone'=>$data['coPhone'],
                'coFax'=>$data['coFax'],
                'city'=>$data['city'],
                'province'=>$data['province'],
                'countryOfOrigin'=>$data['countryOfOrigin'],
                'pic'=>$new_user->name,
                'picId'=>$new_user->_id,
                'picName'=>$new_user->name
            ]);
        }

        if(isset($data['contractNumber']) && $new_user){

            $creditor = Creditor::find($data['creditor']);
            if($creditor && isset($creditor->coName)){
                $cname = $creditor->coName;
            }else{
                $cname = $creditor->coName;
            }

            Creditaccount::create([
                'contractNumber' =>$data['contractName'],
                'contractName'=>$data['contractName'],
                'creditor' => $data['creditor'],
                'Type' => $data['Type'],
                'programName' => $data['programName'],
                'bankCard' => $data['bankCard'],
                'productDescription' => $data['productDescription'],
                'dueDate' => $data['dueDate'],
                'installmentAmt' => $data['installmentAmt'],
                'pickupDate' => $data['pickupDate'],
                'pickupAddress' => $data['pickupAddress'],
                'pickupProvince' => $data['province'],
                'pickupCity' => $data['pickupCity'],
                'pickupDistrict' => $data['pickupDistrict'],
                'pickupZIP' => $data['pickupZIP'],
                'active' => true,
                'payerEmail' => $data['email'],
                'phone' => $data['phone'],
                'mobile' => $data['mobile'],
                'creditorName' => $cname,
                'payerId' => $new_user->_id,
                'payerName' => $new_user->name,
            ]);
        }

        return $new_user;

    }

    protected function authenticated($user)
    {
        //print_r($user->toArray());
        //die();
        $user = Auth::user();

        //print_r($user->toArray());



        if($user->roleId == Prefs::getRoleId('Member')) {
            return redirect('member');
        }

        if($user->roleId == Prefs::getRoleId('Creditor')) {
            return redirect('creditor');
        }

        return redirect('/');
    }
}
