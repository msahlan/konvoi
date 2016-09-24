<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;

class AssettypeapiController extends \BaseController {

    public $controller_name = '';

    public function  __construct()
    {
        //$this->model = "Member";
        $this->controller_name = strtolower( str_replace('Controller', '', get_class()) );

    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $key = Input::get('key');
		$user = \Apiauth::user($key);

        $type = \Assettype::get();
        for($i = 0; $i < count($type);$i++){

                $type[$i]->extId = $type[$i]->_id;

                unset($type[$i]->_id);
                unset($type[$i]->_token);

                $type[$i]->createdDate = date('Y-m-d H:i:s',$type[$i]->createdDate->sec);
                $type[$i]->lastUpdate = date('Y-m-d H:i:s',$type[$i]->lastUpdate->sec);

        }

        $actor = $user->fullname.' : '.$user->email;
        \Event::fire('log.api',array($this->controller_name, 'get' ,$actor,'rack list'));

        return $type;
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
