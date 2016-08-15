<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
})->middleware('auth');

Route::auth();

Route::get('/user', 'UserController@getIndex');
Route::post('/user', 'UserController@postIndex');

Route::get('/incoming', 'IncomingController@getIndex');
Route::post('/incoming', 'IncomingController@postIndex');

Route::get('/zoning', 'ZoningController@getIndex');
Route::post('/zoning', 'ZoningController@postIndex');

Route::get('/courierassign', 'CourierassignController@getIndex');
Route::post('/courierassign', 'CourierassignController@postIndex');

Route::get('/dispatched', 'DispatchedController@getIndex');
Route::post('/dispatched', 'DispatchedController@postIndex');

Route::get('/delivered', 'DeliveredController@getIndex');
Route::post('/delivered', 'DeliveredController@postIndex');

Route::get('/canceled', 'CanceledController@getIndex');
Route::post('/canceled', 'CanceledController@postIndex');

Route::get('/orderarchive', 'OrderarchiveController@getIndex');
Route::post('/orderarchive', 'OrderarchiveController@postIndex');

Route::get('/deliverylog', 'DeliverylogController@getIndex');
Route::post('/deliverylog', 'DeliverylogController@postIndex');

Route::get('/device', 'DeviceController@getIndex');
Route::post('/device', 'DeviceController@postIndex');

Route::get('/parsedevice', 'ParsedeviceController@getIndex');
Route::post('/parsedevice', 'ParsedeviceController@postIndex');
Route::post('/parsedevice/syncparse', 'ParsedeviceController@postSyncparse');

Route::get('/profile', 'ProfileController@getIndex');

function sa($item){
    if(URL::to($item) == URL::full() ){
        return  'active';
    }else{
        return '';
    }
}
