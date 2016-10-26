<?php
use App\Helpers\Prefs;
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

Route::get('/', function(){
    if(Auth::check()){
        $role = strtolower( Prefs::getRoleById(Auth::user()->roleId ));
        if($role == 'member' || $role == 'creditor'){
            return redirect( $role );
        }else{
            return redirect( 'dashboard');
        }
    }else{
        return view('front.index');
    }
});


Route::auth();

Route::get('dashboard', 'DashboardController@getIndex');

Route::get('member/register', 'Auth\AuthController@showRegistrationForm');
Route::get('creditor/register', 'Auth\AuthController@showRegistrationForm');

Route::get('remkota',function(){
    $cov = App\Models\Coverage::where('city','regexp', '/Kota /i')->get();

    foreach($cov as $c){

        print $c->city."\n\r";

        $c->city = trim(str_replace('Kota ','',$c->city)) ;

        print $c->city."\n\r";

        $c->save();

    }

});

Route::post('/upload', 'UploadController@postIndex');
Route::post('/upload/avatar', 'UploadController@postAvatar');
Route::post('/upload/logo', 'UploadController@postLogo');
Route::post('/upload/docs', 'UploadController@postDocs');

Route::get('/option', 'OptionController@getIndex');
Route::post('/option', 'OptionController@postIndex');
Route::get('/option/add', 'OptionController@getAdd');
Route::post('/option/add', 'OptionController@postAdd');
Route::get('/option/edit/{id}', 'OptionController@getEdit');
Route::post('/option/edit/{id}', 'OptionController@postEdit');
Route::post('/option/del', 'OptionController@postDel');


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
Route::post('/incoming/assigndate', 'IncomingController@postAssigndate');


Route::get('/zoning', 'ZoningController@getIndex');
Route::post('/zoning', 'ZoningController@postIndex');
Route::post('/zoning/shipmentlist', 'ZoningController@postShipmentlist');
Route::post('/zoning/assigndevice', 'ZoningController@postAssigndevice');
Route::post('/zoning/deviceavail', 'ZoningController@postDeviceavail');


Route::get('/courierassign', 'CourierassignController@getIndex');
Route::post('/courierassign', 'CourierassignController@postIndex');
Route::post('/courierassign/assigncourier', 'CourierassignController@postAssigncourier');

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

Route::get('/coverage', 'CoverageController@getIndex');
Route::post('/coverage', 'CoverageController@postIndex');

Route::get('/device', 'DeviceController@getIndex');
Route::post('/device', 'DeviceController@postIndex');

Route::get('/parsedevice', 'ParsedeviceController@getIndex');
Route::post('/parsedevice', 'ParsedeviceController@postIndex');
Route::post('/parsedevice/syncparse', 'ParsedeviceController@postSyncparse');

Route::get('/fcmdevice', 'FcmdeviceController@getIndex');
Route::post('/fcmdevice', 'FcmdeviceController@postIndex');
Route::post('/fcmdevice/syncparse', 'FcmdeviceController@postSyncparse');
Route::post('/fcmdevice/fcmpush', 'FcmdeviceController@postFcmpush');

/* common ajax routes */
Route::post('/ajax/sessionsave', 'AjaxController@postSessionsave');
Route::get('/ajax/merchant', 'AjaxController@getMerchant');
Route::post('/ajax/merchantapp', 'AjaxController@postMerchantapp');
Route::post('/ajax/locationlog', 'AjaxController@postLocationlog');
Route::post('/ajax/delfile', 'AjaxController@postDelfile');
Route::get('/ajax/org', 'AjaxController@getOrg');
Route::get('/ajax/user', 'AjaxController@getUser');
Route::get('/ajax/device', 'AjaxController@getDevice');
Route::get('/ajax/courier', 'AjaxController@getCourier');
Route::post('/ajax/confirmdata', 'AjaxController@postConfirmdata');
Route::post('/ajax/canceldata', 'AjaxController@postCanceldata');
Route::post('/ajax/generatedata', 'AjaxController@postGeneratedata');

Route::post('/ajax/routelist', 'AjaxController@postRoutelist');
Route::post('/ajax/saveroutelist', 'AjaxController@postSaveroutelist');

Route::get('/ajax/creditprogram', 'AjaxController@getCreditprogram');
Route::post('/ajax/creditprogram', 'AjaxController@postCreditprogram');

Route::post('/ajax/city', 'AjaxController@postCity');
Route::post('/ajax/district', 'AjaxController@postDistrict');

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
Route::post('/route/locsearch', 'RouteController@postLocsearch');
Route::post('/route/locsave', 'RouteController@postLocsave');
Route::post('/route/saveseq', 'RouteController@postSaveseq');


Route::group( [ 'prefix'=>'document', 'middlewareGroup'=>['web'] ] , function(){

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

});


Route::group( [ 'prefix'=>'finance', 'middlewareGroup'=>['web'] ] , function(){

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


});


Route::group( [ 'prefix'=>'asset', 'middlewareGroup'=>['web'] ] , function(){

    Route::get('/asset', 'Asset\AssetController@getIndex');
    Route::post('/asset', 'Asset\AssetController@postIndex');
    Route::get('/asset/printlabel/{sessionname}/{printparam}/{format?}', 'Asset\AssetController@getPrintlabel');
    Route::get('/asset/import', 'Asset\AssetController@getImport');
    Route::post('/asset/uploadimport', 'Asset\AssetController@postUploadimport');
    Route::get('/asset/commit/{sessid}', 'Asset\AssetController@getCommit');
    Route::post('/asset/commit/{sessid}', 'Asset\AssetController@postCommit');
    Route::post('/asset/dlxl', 'Asset\AssetController@postDlxl');
    Route::get('/asset/dl/{filename}', 'Asset\AssetController@getDl');
    Route::get('/asset/csv/{filename}', 'Asset\AssetController@getCsv');
    Route::get('/asset/add', 'Asset\AssetController@getAdd');
    Route::post('/asset/add', 'Asset\AssetController@postAdd');
    Route::get('/asset/edit/{id}', 'Asset\AssetController@getEdit');
    Route::post('/asset/edit/{id}', 'Asset\AssetController@postEdit');
    Route::post('/asset/dirscan', 'Asset\AssetController@postDirscan');

    Route::get('/assetlocation', 'Asset\AssetlocationController@getIndex');
    Route::post('/assetlocation', 'Asset\AssetlocationController@postIndex');
    Route::get('/assetlocation/printlabel/{sessionname}/{printparam}/{format?}', 'Asset\AssetlocationController@getPrintlabel');
    Route::get('/assetlocation/import', 'Asset\AssetlocationController@getImport');
    Route::post('/assetlocation/uploadimport', 'Asset\AssetlocationController@postUploadimport');
    Route::get('/assetlocation/commit/{sessid}', 'Asset\AssetlocationController@getCommit');
    Route::post('/assetlocation/commit/{sessid}', 'Asset\AssetlocationController@postCommit');
    Route::post('/assetlocation/dlxl', 'Asset\AssetlocationController@postDlxl');
    Route::get('/assetlocation/dl/{filename}', 'Asset\AssetlocationController@getDl');
    Route::get('/assetlocation/csv/{filename}', 'Asset\AssetlocationController@getCsv');
    Route::get('/assetlocation/add', 'Asset\AssetlocationController@getAdd');
    Route::post('/assetlocation/add', 'Asset\AssetlocationController@postAdd');
    Route::get('/assetlocation/edit/{id}', 'Asset\AssetlocationController@getEdit');
    Route::post('/assetlocation/edit/{id}', 'Asset\AssetlocationController@postEdit');


});


Route::group( [ 'prefix'=>'member', 'middlewareGroup'=>['web'] ] , function(){
    Route::get('/', 'DashboardController@getIndex');

    Route::get('/profile', 'Member\ProfileController@getIndex');

    Route::get('/account', 'Member\AccountController@getIndex');
    Route::post('/account', 'Member\AccountController@postIndex');
    Route::get('/account/add', 'Member\AccountController@getAdd');
    Route::post('/account/add', 'Member\AccountController@postAdd');
    Route::get('/account/edit/{id}', 'Member\AccountController@getEdit');
    Route::post('/account/edit/{id}', 'Member\AccountController@postEdit');
    Route::post('/account/toggle', 'Member\AccountController@postToggle');
    Route::post('/account/del', 'Member\AccountController@postDel');

    Route::get('/transaction', 'Member\TransactionController@getIndex');
    Route::post('/transaction', 'Member\TransactionController@postIndex');

});

Route::group( [ 'prefix'=>'creditor', 'middlewareGroup'=>['web'] ] , function(){
    Route::get('/', 'DashboardController@getIndex');

    Route::get('/profile', 'Creditor\ProfileController@getIndex');

    Route::get('/account', 'Creditor\AccountController@getIndex');
    Route::post('/account', 'Creditor\AccountController@postIndex');
    Route::get('/account/add', 'Creditor\AccountController@getAdd');
    Route::post('/account/add', 'Creditor\AccountController@postAdd');
    Route::get('/account/edit/{id}', 'Creditor\AccountController@getEdit');
    Route::post('/account/edit/{id}', 'Creditor\AccountController@postEdit');
    Route::get('/account/printlabel/{sessionname}/{printparam}/{format?}', 'Creditor\AccountController@getPrintlabel');
    Route::get('/account/import', 'Creditor\AccountController@getImport');
    Route::post('/account/uploadimport', 'Creditor\AccountController@postUploadimport');
    Route::get('/account/commit/{sessid}', 'Creditor\AccountController@getCommit');
    Route::post('/account/commit/{sessid}', 'Creditor\AccountController@postCommit');
    Route::post('/account/dlxl', 'Creditor\AccountController@postDlxl');
    Route::get('/account/dl/{filename}', 'Creditor\AccountController@getDl');
    Route::get('/account/csv/{filename}', 'Creditor\AccountController@getCsv');

    Route::get('/type', 'Creditor\TypeController@getIndex');
    Route::post('/type', 'Creditor\TypeController@postIndex');
    Route::get('/type/add', 'Creditor\TypeController@getAdd');
    Route::post('/type/add', 'Creditor\TypeController@postAdd');
    Route::get('/type/edit/{id}', 'Creditor\TypeController@getEdit');
    Route::post('/type/edit/{id}', 'Creditor\TypeController@postEdit');
    Route::get('/type/printlabel/{sessionname}/{printparam}/{format?}', 'Creditor\TypeController@getPrintlabel');
    Route::get('/type/import', 'Creditor\TypeController@getImport');
    Route::post('/type/uploadimport', 'Creditor\TypeController@postUploadimport');
    Route::get('/type/commit/{sessid}', 'Creditor\TypeController@getCommit');
    Route::post('/type/commit/{sessid}', 'Creditor\TypeController@postCommit');
    Route::post('/type/dlxl', 'Creditor\TypeController@postDlxl');
    Route::get('/type/dl/{filename}', 'Creditor\TypeController@getDl');
    Route::get('/type/csv/{filename}', 'Creditor\TypeController@getCsv');

    Route::get('/transaction', 'Creditor\TransactionController@getIndex');
    Route::post('/transaction', 'Creditor\TransactionController@postIndex');

});

Route::group( [ 'prefix'=>'pickup', 'middlewareGroup'=>['web'] ] , function(){
    Route::get('/', 'DashboardController@getIndex');

    Route::get('/account', 'Pickup\AccountController@getIndex');
    Route::post('/account', 'Pickup\AccountController@postIndex');
    Route::get('/account/add', 'Pickup\AccountController@getAdd');
    Route::post('/account/add', 'Pickup\AccountController@postAdd');
    Route::get('/account/edit/{id}', 'Pickup\AccountController@getEdit');
    Route::post('/account/edit/{id}', 'Pickup\AccountController@postEdit');
    Route::post('/account/toggle', 'Pickup\AccountController@postToggle');
    Route::post('/account/del', 'Pickup\AccountController@postDel');
    Route::get('/account/printlabel/{sessionname}/{printparam}/{format?}', 'Pickup\AccountController@getPrintlabel');
    Route::get('/account/import', 'Pickup\AccountController@getImport');
    Route::post('/account/uploadimport', 'Pickup\AccountController@postUploadimport');
    Route::get('/account/commit/{sessid}', 'Pickup\AccountController@getCommit');
    Route::post('/account/commit/{sessid}', 'Pickup\AccountController@postCommit');
    Route::post('/account/dlxl', 'Pickup\AccountController@postDlxl');
    Route::get('/account/dl/{filename}', 'Pickup\AccountController@getDl');
    Route::get('/account/csv/{filename}', 'Pickup\AccountController@getCsv');



    Route::get('/incoming', 'Pickup\IncomingController@getIndex');
    Route::post('/incoming', 'Pickup\IncomingController@postIndex');
    Route::get('/incoming/printlabel/{sessionname}/{printparam}/{format?}', 'Pickup\IncomingController@getPrintlabel');
    Route::get('/incoming/import', 'Pickup\IncomingController@getImport');
    Route::post('/incoming/uploadimport', 'Pickup\IncomingController@postUploadimport');
    Route::get('/incoming/commit/{sessid}', 'Pickup\IncomingController@getCommit');
    Route::post('/incoming/commit/{sessid}', 'Pickup\IncomingController@postCommit');
    Route::post('/incoming/dlxl', 'Pickup\IncomingController@postDlxl');
    Route::get('/incoming/dl/{filename}', 'Pickup\IncomingController@getDl');
    Route::get('/incoming/csv/{filename}', 'Pickup\IncomingController@getCsv');
    Route::get('/incoming/add', 'Pickup\IncomingController@getAdd');
    Route::post('/incoming/add', 'Pickup\IncomingController@postAdd');
    Route::post('/incoming/assigndate', 'Pickup\IncomingController@postAssigndate');
    Route::post('/incoming/shipmentlist', 'Pickup\IncomingController@postShipmentlist');
    Route::post('/incoming/assigndevice', 'Pickup\IncomingController@postAssigndevice');
    Route::post('/incoming/deviceavail', 'Pickup\IncomingController@postDeviceavail');


    Route::get('/zoning', 'Pickup\ZoningController@getIndex');
    Route::post('/zoning', 'Pickup\ZoningController@postIndex');
    Route::post('/zoning/shipmentlist', 'Pickup\ZoningController@postShipmentlist');
    Route::post('/zoning/assigndevice', 'Pickup\ZoningController@postAssigndevice');
    Route::post('/zoning/deviceavail', 'Pickup\ZoningController@postDeviceavail');


    Route::get('/courierassign', 'Pickup\CourierassignController@getIndex');
    Route::post('/courierassign', 'Pickup\CourierassignController@postIndex');
    Route::post('/courierassign/assigncourier', 'Pickup\CourierassignController@postAssigncourier');

    Route::get('/dispatched', 'Pickup\DispatchedController@getIndex');
    Route::post('/dispatched', 'Pickup\DispatchedController@postIndex');

    Route::get('/delivered', 'Pickup\DeliveredController@getIndex');
    Route::post('/delivered', 'Pickup\DeliveredController@postIndex');

    Route::get('/canceled', 'Pickup\CanceledController@getIndex');
    Route::post('/canceled', 'Pickup\CanceledController@postIndex');

    Route::get('/quota', 'Pickup\QuotaController@getIndex');
    Route::post('/quota', 'Pickup\QuotaController@postIndex');

    Route::get('/type', 'Pickup\TypeController@getIndex');
    Route::post('/type', 'Pickup\TypeController@postIndex');
    Route::get('/type/add', 'Pickup\TypeController@getAdd');
    Route::post('/type/add', 'Pickup\TypeController@postAdd');
    Route::get('/type/edit/{id}', 'Pickup\TypeController@getEdit');
    Route::post('/type/edit/{id}', 'Pickup\TypeController@postEdit');
    Route::get('/type/printlabel/{sessionname}/{printparam}/{format?}', 'Pickup\TypeController@getPrintlabel');
    Route::get('/type/import', 'Pickup\TypeController@getImport');
    Route::post('/type/uploadimport', 'Pickup\TypeController@postUploadimport');
    Route::get('/type/commit/{sessid}', 'Pickup\TypeController@getCommit');
    Route::post('/type/commit/{sessid}', 'Pickup\TypeController@postCommit');
    Route::post('/type/dlxl', 'Pickup\TypeController@postDlxl');
    Route::get('/type/dl/{filename}', 'Pickup\TypeController@getDl');
    Route::get('/type/csv/{filename}', 'Pickup\TypeController@getCsv');

    Route::get('/member', 'MemberController@getIndex');
    Route::post('/member', 'MemberController@postIndex');
    Route::get('/member/add', 'MemberController@getAdd');
    Route::post('/member/add', 'MemberController@postAdd');
    Route::get('/member/edit/{id}', 'MemberController@getEdit');
    Route::post('/member/edit/{id}', 'MemberController@postEdit');
    Route::post('/member/del', 'MemberController@postDel');

    Route::get('/creditor', 'CreditorController@getIndex');
    Route::post('/creditor', 'CreditorController@postIndex');
    Route::get('/creditor/add', 'CreditorController@getAdd');
    Route::post('/creditor/add', 'CreditorController@postAdd');
    Route::get('/creditor/edit/{id}', 'CreditorController@getEdit');
    Route::post('/creditor/edit/{id}', 'CreditorController@postEdit');
    Route::post('/creditor/del', 'CreditorController@postDel');

});

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
    Route::resource('payment', 'Api\PaymentpickupapiController');
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
