<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use App\Models\Deliverydetail;

use App\Helpers\Prefs;


use Config;

use Auth;
use Event;
use View;
use Input;
use Request;
use Image;
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

class UploadapiController extends Controller {
    public $controller_name = '';

    public function  __construct()
    {
        //$this->model = "Member";
        $this->controller_name = strtolower( str_replace('Controller', '', get_class()) );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postFile()
    {

        $key = Request::input('key');

        //$user = \Apiauth::user($key);

        $user = Device::where('key','=',$key)->first();

        $appname = (Request::has('app'))?Request::input('app'):'app.name';


        if(!$user){
            $actor = 'no id : no name';
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'device not found, upload image failed'));

            return Response::json(array('status'=>'ERR:NODEVICE', 'timestamp'=>time(), 'message'=>$image_id ));
        }

        $parent_id = Request::input('parid');

        $parent_class = Request::input('parclass');

        $file_id = Request::input('fid');

        $image_id = Request::input('img');

        $ns = Request::input('ns');

        $isSignature = Request::input('signature');

        $lat = Request::input('lat');

        $lon = Request::input('lon');

        if( isset($file_id) && $file_id != '' ){
            $rstring = $file_id;
        }else{
            $rstring = str_random(15);
        }

        $deviceId = Request::input('deviceid');
        $deviceKey = Request::input('deviceid');


        $result = '';

        $timepath = date('Ym',time());

        //$destinationPath = realpath('storage/media').'/'.$rstring;

        if(Request::hasFile('imagefile')){

            $file = Request::file('imagefile');

            $destinationPath = realpath('storage/media2').'/'.$timepath.'/'.$rstring;

            $filename = $file->getClientOriginalName();
            $filemime = $file->getMimeType();
            $filesize = $file->getSize();
            $extension = $file->getClientOriginalExtension(); //if you need extension of the file

            $filename = str_replace(config('kickstart.invalidchars'), '-', $filename);

            $uploadSuccess = $file->move($destinationPath, $filename);

            $is_image = true;
            $is_audio = false;
            $is_video = false;
            $is_pdf = false;
            $is_doc = false;

            $is_image = $this->isImage($filemime);
            $is_audio = $this->isAudio($filemime);
            $is_video = $this->isVideo($filemime);
            $is_pdf = $this->isPdf($filemime);

            if(!($is_image || $is_audio || $is_video || $is_pdf)){
                $is_doc = true;
            }else{
                $is_doc = false;
            }

            $exif = array();

            if($is_image){

                $ps = config('picture.sizes');

                $thumbnail = Image::make($destinationPath.'/'.$filename)
                    ->fit($ps['thumbnail']['width'],$ps['thumbnail']['height'])
                    ->save($destinationPath.'/th_'.$filename);

                $medium = Image::make($destinationPath.'/'.$filename)
                    ->fit($ps['medium']['width'],$ps['medium']['height'])
                    ->save($destinationPath.'/med_'.$filename);

                $large = Image::make($destinationPath.'/'.$filename)
                    ->fit($ps['large']['width'],$ps['large']['height'])
                    ->save($destinationPath.'/lrg_'.$filename);

                $full = Image::make($destinationPath.'/'.$filename)
                    ->save($destinationPath.'/full_'.$filename);

                $image_size_array = array(
                    'thumbnail_url'=> URL::to('storage/media2/'.$timepath.'/'.$rstring.'/'.$ps['thumbnail']['prefix'].$filename),
                    'large_url'=> URL::to('storage/media2/'.$timepath.'/'.$rstring.'/'.$ps['large']['prefix'].$filename),
                    'medium_url'=> URL::to('storage/media2/'.$timepath.'/'.$rstring.'/'.$ps['medium']['prefix'].$filename),
                    'full_url'=> URL::to('storage/media2/'.$timepath.'/'.$rstring.'/'.$ps['full']['prefix'].$filename),
                );

                $exif = Image::make($destinationPath.'/'.$filename)
                    ->exif();

            }else{

                if($is_audio){
                    $thumbnail_url = URL::to('images/audio.png');
                }elseif($is_video){
                    $thumbnail_url = URL::to('images/video.png');
                }else{
                    $thumbnail_url = URL::to('images/media.png');
                }

                $image_size_array = array(
                    'thumbnail_url'=> $thumbnail_url,
                    'large_url'=> '',
                    'medium_url'=> '',
                    'full_url'=> ''
                );
            }


            $item = array(
                    'ns'=>$ns,
                    'parent_id'=> $parent_id,
                    'parent_class'=> $parent_class,
                    'url'=> URL::to('storage/media/'.$rstring.'/'.$filename),
                    'temp_dir'=> $destinationPath,
                    'file_id'=> $rstring,
                    'is_image'=>$is_image,
                    'is_audio'=>$is_audio,
                    'is_video'=>$is_video,
                    'is_signature'=>$isSignature,
                    'is_pdf'=>$is_pdf,
                    'is_doc'=>$is_doc,
                    'latitude'=>$lat,
                    'longitude'=>$lon,
                    'name'=> $filename,
                    'type'=> $filemime,
                    'size'=> $filesize,
                    'deleted'=>0,
                    'deviceId'=>$deviceId,
                    'deviceKey'=>$deviceKey,
                    'appname'=>$appname,
                    'createdDate'=>new MongoDate(),
                    'lastUpdate'=>new \MongoDate()
                );

            foreach($image_size_array as $k=>$v){
                $item[$k] = $v;
            }


            $item['_id'] = new \MongoId($image_id);

            $im = \Uploaded::find($image_id);
            if($im){

                foreach($item as $k=>$v){
                    if($k != '_id'){
                        $im->{$k} = $v;
                    }
                }

                $im->save();

            }else{
                \Uploaded::insertGetId($item);
            }

            $actor = $user->identifier.' : '.$user->devname;
            \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'upload image'));

            return \Response::json(array('status'=>'OK', 'timestamp'=>time(), 'message'=>$image_id ));


        }

        $actor = $user->identifier.' : '.$user->devname;
        \Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'upload image failed'));

        return \Response::json(array('status'=>'ERR:NOFILE', 'timestamp'=>time(), 'message'=>$image_id ));

    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postFiles()
    {

        $key = Request::input('key');

        $user = Apiauth::user($key);

        $parent_id = Request::input('parid');

        $parent_class = Request::input('parclass');

        $image_id = Request::input('img');

        $ns = Request::input('ns');

        $rstring = str_random(15);

        $result = '';

        $destinationPath = realpath('storage/media').'/'.$rstring;

        if(isset($_FILES['file'])){

            $file = $_FILES['file'];

            $filename = $file['name'];
            $filemime = $file['type'];
            $filesize = $file['size'];
            $extension = '.jpg'; //if you need extension of the file

            $tmp_name = $file['tmp_name'];

            $filename = str_replace(config('kickstart.invalidchars'), '-', $filename);

            //$uploadSuccess = $file->move($destinationPath, $filename);

            @move_uploaded_file($tmp_name, $destinationPath.'/'.$filename);

            $is_image = true;
            $is_audio = false;
            $is_video = false;
            $is_pdf = false;
            $is_doc = false;



            $is_image = $this->isImage($filemime);
            $is_audio = $this->isAudio($filemime);
            $is_video = $this->isVideo($filemime);
            $is_pdf = $this->isPdf($filemime);

            if(!($is_image || $is_audio || $is_video || $is_pdf)){
                $is_doc = true;
            }else{
                $is_doc = false;
            }

            if($is_image){

                $ps = config('picture.sizes');

                $thumbnail = Image::make($destinationPath.'/'.$filename)
                    ->fit($ps['thumbnail']['width'],$ps['thumbnail']['height'])
                    ->save($destinationPath.'/th_'.$filename);

                $medium = Image::make($destinationPath.'/'.$filename)
                    ->fit($ps['medium']['width'],$ps['medium']['height'])
                    ->save($destinationPath.'/med_'.$filename);

                $large = Image::make($destinationPath.'/'.$filename)
                    ->fit($ps['large']['width'],$ps['large']['height'])
                    ->save($destinationPath.'/lrg_'.$filename);

                $full = Image::make($destinationPath.'/'.$filename)
                    ->save($destinationPath.'/full_'.$filename);

                $image_size_array = array(
                    'thumbnail_url'=> URL::to('storage/media/'.$rstring.'/'.$ps['thumbnail']['prefix'].$filename),
                    'large_url'=> URL::to('storage/media/'.$rstring.'/'.$ps['large']['prefix'].$filename),
                    'medium_url'=> URL::to('storage/media/'.$rstring.'/'.$ps['medium']['prefix'].$filename),
                    'full_url'=> URL::to('storage/media/'.$rstring.'/'.$ps['full']['prefix'].$filename),
                );

            }else{

                if($is_audio){
                    $thumbnail_url = URL::to('images/audio.png');
                }elseif($is_video){
                    $thumbnail_url = URL::to('images/video.png');
                }else{
                    $thumbnail_url = URL::to('images/media.png');
                }

                $image_size_array = array(
                    'thumbnail_url'=> $thumbnail_url,
                    'large_url'=> '',
                    'medium_url'=> '',
                    'full_url'=> ''
                );
            }


            $item = array(
                    'ns'=>$ns,
                    'parent_id'=> $parent_id,
                    'parent_class'=> $parent_class,
                    'url'=> URL::to('storage/media/'.$rstring.'/'.$filename),
                    'temp_dir'=> $destinationPath,
                    'file_id'=> $rstring,
                    'is_image'=>$is_image,
                    'is_audio'=>$is_audio,
                    'is_video'=>$is_video,
                    'is_pdf'=>$is_pdf,
                    'is_doc'=>$is_doc,
                    'name'=> $filename,
                    'type'=> $filemime,
                    'size'=> $filesize,
                    'deleted'=>0,
                    'createdDate'=>new MongoDate(),
                    'lastUpdate'=>new MongoDate()
                );

            foreach($image_size_array as $k=>$v){
                $item[$k] = $v;
            }


            $item['_id'] = new MongoId($image_id);

            $im = Uploaded::find($image_id);
            if($im){

            }else{
                Uploaded::insertGetId($item);
            }

            $actor = $user->fullname.' : '.$user->email;
            Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'upload image'));

            return Response::json(array('status'=>'OK', 'timestamp'=>time(), 'message'=>$image_id ));


        }

        $actor = $user->fullname.' : '.$user->email;
        Event::fire('log.api',array($this->controller_name, 'post' ,$actor,'upload image failed'));

        return Response::json(array('status'=>'ERR:NOFILE', 'timestamp'=>time(), 'message'=>$image_id ));

    }

    private function isAudio($mime){
        return preg_match('/^audio/',$mime);
    }

    private function isVideo($mime){
        return preg_match('/^video/',$mime);
    }

    private function isImage($mime){
        return preg_match('/^image/',$mime);
    }

    private function isPdf($mime){
        return preg_match('/pdf/',$mime);
    }


}