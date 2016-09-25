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

Route::get('/', 'DocsController@getIndex')->middleware('auth');

Route::auth();

Route::post('/upload', 'UploadController@postIndex');
Route::post('/upload/avatar', 'UploadController@postAvatar');
Route::post('/upload/logo', 'UploadController@postLogo');
Route::post('/upload/docs', 'UploadController@postDocs');


Route::get('/user', 'UserController@getIndex');
Route::post('/user', 'UserController@postIndex');
Route::get('/user/add', 'UserController@getAdd');
Route::post('/user/add', 'UserController@postAdd');
Route::get('/user/edit/{id}', 'UserController@getEdit');
Route::post('/user/edit/{id}', 'UserController@postEdit');
Route::post('/user/del', 'UserController@postDel');

Route::get('/usergroup', 'UsergroupController@getIndex');
Route::post('/usergroup', 'UsergroupController@postIndex');
Route::get('/usergroup/add', 'UsergroupController@getAdd');
Route::post('/usergroup/add', 'UsergroupController@postAdd');
Route::get('/usergroup/edit/{id}', 'UsergroupController@getEdit');
Route::post('/usergroup/edit/{id}', 'UsergroupController@postEdit');
Route::post('/usergroup/del', 'UsergroupController@postDel');

Route::get('/organization', 'OrganizationController@getIndex');
Route::post('/organization', 'OrganizationController@postIndex');
Route::get('/organization/add', 'OrganizationController@getAdd');
Route::post('/organization/add', 'OrganizationController@postAdd');
Route::get('/organization/edit/{id}', 'OrganizationController@getEdit');
Route::post('/organization/edit/{id}', 'OrganizationController@postEdit');
Route::post('/organization/del', 'OrganizationController@postDel');


Route::get('/incoming', 'IncomingController@getIndex');
Route::post('/incoming', 'IncomingController@postIndex');
Route::get('/incoming/printlabel/{sessionname}/{printparam}/{format?}', 'IncomingController@getPrintlabel');
Route::get('/incoming/import', 'IncomingController@getImport');
Route::post('/incoming/uploadimport', 'IncomingController@postUploadimport');
Route::get('/incoming/commit/{sessid}', 'IncomingController@getCommit');
Route::post('/incoming/commit/{sessid}', 'IncomingController@postCommit');
Route::post('/incoming/dlxl', 'IncomingController@postDlxl');
Route::get('/incoming/dl/{filename}', 'IncomingController@getDl');
Route::get('/incoming/csv/{filename}', 'IncomingController@getCsv');
Route::get('/incoming/add', 'IncomingController@getAdd');
Route::post('/incoming/add', 'IncomingController@postAdd');


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

Route::get('/fcmdevice', 'FcmdeviceController@getIndex');
Route::post('/fcmdevice', 'FcmdeviceController@postIndex');
Route::post('/fcmdevice/syncparse', 'FcmdeviceController@postSyncparse');
Route::post('/fcmpush', 'FcmdeviceController@postFcmpush');


Route::post('/ajax/sessionsave', 'AjaxController@postSessionsave');
Route::get('/ajax/merchant', 'AjaxController@getMerchant');
Route::post('/ajax/merchantapp', 'AjaxController@postMerchantapp');
Route::post('/ajax/locationlog', 'AjaxController@postLocationlog');
Route::post('/ajax/delfile', 'AjaxController@postDelfile');
Route::get('/ajax/org', 'AjaxController@getOrg');
Route::get('/ajax/device', 'AjaxController@getDevice');


Route::get('/profile', 'ProfileController@getIndex');

Route::get('/orderlog', 'OrderlogController@getIndex');
Route::post('/orderlog', 'OrderlogController@postIndex');

Route::get('/notelog', 'NotelogController@getIndex');
Route::post('/notelog', 'NotelogController@postIndex');

Route::get('/photolog', 'PhotologController@getIndex');
Route::post('/photolog', 'PhotologController@postIndex');

Route::get('/locationlog', 'LocationlogController@getIndex');
Route::post('/locationlog', 'LocationlogController@postIndex');

Route::get('/route', 'RouteController@getIndex');
Route::post('/route', 'RouteController@postIndex');

Route::get('/docs', 'DocsController@getIndex');
Route::post('/docs', 'DocsController@postIndex');
Route::get('/docs/printlabel/{sessionname}/{printparam}/{format?}', 'DocsController@getPrintlabel');
Route::get('/docs/import', 'DocsController@getImport');
Route::post('/docs/uploadimport', 'DocsController@postUploadimport');
Route::get('/docs/commit/{sessid}', 'DocsController@getCommit');
Route::post('/docs/commit/{sessid}', 'DocsController@postCommit');
Route::post('/docs/dlxl', 'DocsController@postDlxl');
Route::get('/docs/dl/{filename}', 'DocsController@getDl');
Route::get('/docs/csv/{filename}', 'DocsController@getCsv');
Route::get('/docs/add', 'DocsController@getAdd');
Route::post('/docs/add', 'DocsController@postAdd');

Route::get('/docs/edit/{id}', 'DocsController@getEdit');
Route::post('/docs/edit/{id}', 'DocsController@postEdit');

Route::post('/docs/dirscan', 'DocsController@postDirscan');

Route::get('/invoice', 'InvoiceController@getIndex');
Route::post('/invoice', 'InvoiceController@postIndex');
Route::get('/invoice/printlabel/{sessionname}/{printparam}/{format?}', 'InvoiceController@getPrintlabel');
Route::get('/invoice/import', 'InvoiceController@getImport');
Route::post('/invoice/uploadimport', 'InvoiceController@postUploadimport');
Route::get('/invoice/commit/{sessid}', 'InvoiceController@getCommit');
Route::post('/invoice/commit/{sessid}', 'InvoiceController@postCommit');
Route::post('/invoice/dlxl', 'InvoiceController@postDlxl');
Route::get('/invoice/dl/{filename}', 'InvoiceController@getDl');
Route::get('/invoice/csv/{filename}', 'InvoiceController@getCsv');
Route::get('/invoice/add', 'InvoiceController@getAdd');
Route::post('/invoice/add', 'InvoiceController@postAdd');

Route::get('/invoice/edit/{id}', 'InvoiceController@getEdit');
Route::post('/invoice/edit/{id}', 'InvoiceController@postEdit');

Route::post('/invoice/dirscan', 'InvoiceController@postDirscan');


Route::get('/asset', 'AssetController@getIndex');
Route::post('/asset', 'AssetController@postIndex');
Route::get('/asset/printlabel/{sessionname}/{printparam}/{format?}', 'AssetController@getPrintlabel');
Route::get('/asset/import', 'AssetController@getImport');
Route::post('/asset/uploadimport', 'AssetController@postUploadimport');
Route::get('/asset/commit/{sessid}', 'AssetController@getCommit');
Route::post('/asset/commit/{sessid}', 'AssetController@postCommit');
Route::post('/asset/dlxl', 'AssetController@postDlxl');
Route::get('/asset/dl/{filename}', 'AssetController@getDl');
Route::get('/asset/csv/{filename}', 'AssetController@getCsv');
Route::get('/asset/add', 'AssetController@getAdd');
Route::post('/asset/add', 'AssetController@postAdd');
Route::get('/asset/edit/{id}', 'AssetController@getEdit');
Route::post('/asset/edit/{id}', 'AssetController@postEdit');
Route::post('/asset/dirscan', 'AssetController@postDirscan');

Route::get('/assetlocation', 'AssetlocationController@getIndex');
Route::post('/assetlocation', 'AssetlocationController@postIndex');
Route::get('/assetlocation/printlabel/{sessionname}/{printparam}/{format?}', 'AssetlocationController@getPrintlabel');
Route::get('/assetlocation/import', 'AssetlocationController@getImport');
Route::post('/assetlocation/uploadimport', 'AssetlocationController@postUploadimport');
Route::get('/assetlocation/commit/{sessid}', 'AssetlocationController@getCommit');
Route::post('/assetlocation/commit/{sessid}', 'AssetlocationController@postCommit');
Route::post('/assetlocation/dlxl', 'AssetlocationController@postDlxl');
Route::get('/assetlocation/dl/{filename}', 'AssetlocationController@getDl');
Route::get('/assetlocation/csv/{filename}', 'AssetlocationController@getCsv');
Route::get('/assetlocation/add', 'AssetlocationController@getAdd');
Route::post('/assetlocation/add', 'AssetlocationController@postAdd');
Route::get('/assetlocation/edit/{id}', 'AssetlocationController@getEdit');
Route::post('/assetlocation/edit/{id}', 'AssetlocationController@postEdit');

Route::group(array('prefix' => 'api/v1/mobile','middleware'=>array('api') ), function (){
    Route::get('/auth', 'Api\AuthController@index');
    Route::post('/auth/login', 'Api\AuthController@login');
    Route::put('/auth/login', 'Api\AuthController@login');
    Route::post('/auth/logout', 'Api\AuthController@logout');
    Route::put('/auth/logout', 'Api\AuthController@logout');
    Route::post('/upload', 'Api\UploadapiController@postFile');
    Route::put('/sync/assets', 'Api\SyncapiController@putAssets');
    Route::post('/sync/meta', 'Api\SyncapiController@postMeta');
    Route::post('/sync/scanlog', 'Api\SyncapiController@postScanlog');
    Route::post('/sync/note', 'Api\SyncapiController@postNote');
    Route::post('/sync/geolog', 'Api\SyncapiController@postGeolog');
    Route::post('/sync/order', 'Api\SyncapiController@postOrder');
    Route::post('/sync/orderstatus', 'Api\SyncapiController@postOrderstatus');

    Route::post('/sync/hub', 'Api\SyncapiController@postHuborder');
    Route::post('/sync/hubstatus', 'Api\SyncapiController@postHubstatus');

    Route::post('/sync/pickup', 'Api\SyncapiController@postPickuporder');
    Route::post('/sync/pickupstatus', 'Api\SyncapiController@postPickupstatus');

    Route::post('/fcm/register', 'Api\FcmController@postRegister');

    Route::post('/sync/box', 'Api\SyncapiController@postBox');
    Route::post('/sync/boxstatus', 'Api\SyncapiController@postBoxstatus');
    Route::resource('img', 'Api\ImgapiController');
    Route::resource('location', 'Api\LocationapiController');
    Route::resource('rack', 'Api\RackapiController');
    Route::resource('asset', 'Api\AssetapiController');
    Route::resource('delivery', 'Api\DeliveryapiController');
    Route::resource('pickup', 'Api\PickupapiController');
    Route::resource('warehouse', 'Api\HubapiController');
    Route::resource('merchant', 'Api\MerchantapiController');
});

Route::group(array('prefix' => 'api/v1/service'), function (){
    Route::resource('awb', 'Api\AwbController');
    Route::resource('confirm', 'Api\ConfirmController');
    Route::resource('status', 'Api\StatusController');
    Route::post('wv', 'Api\AwbController@postWv');
});


/* Fast Routes */

Route::get('dir', function(){
    $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
    print $storagePath;

    $files = Storage::disk('repo')->files();
    print_r($files);
});


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
