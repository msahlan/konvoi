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

Route::get('/', 'IncomingController@getIndex');

Route::auth();

Route::get('/user', 'UserController@getIndex');
Route::post('/user', 'UserController@postIndex');

Route::get('/incoming', 'IncomingController@getIndex');
Route::post('/incoming', 'IncomingController@postIndex');
Route::get('/incoming/printlabel/{sessionname}/{printparam}/{format?}', 'IncomingController@getPrintlabel');
Route::get('/incoming/import', 'IncomingController@getImport');


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

Route::post('/ajax/sessionsave', 'AjaxController@postSessionsave');

Route::get('/profile', 'ProfileController@getIndex');


/* Fast Routes */

Route::get('qr/{txt}',function($txt){
    $txt = base64_decode($txt);
    return QRCode::format('png')->size(399)->color(40,40,40)->generate($txt);
});

Route::get('barcode/dl/{txt}',function($txt){
    $barcode = new Barcode();
    $barcode->make($txt,'code128',60, 'horizontal' ,true);
    return $barcode->render('jpg',$txt,true);
});

Route::get('barcode/{txt}',function($txt){
	print DNS1D::getBarcodePNG($txt, 'C128');
});

Route::get('pdf417/{txt}',function($txt){
    $txt = base64_decode($txt);
    header('Content-Type: image/svg+xml');
    print DNS2D::getBarcodeSVG($txt, "PDF417");
});


function sa($item){
    if(URL::to($item) == URL::full() ){
        return  'active';
    }else{
        return '';
    }
}
