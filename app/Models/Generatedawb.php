<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

//use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Generatedawb extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'awb_generated';
    //protected $collection = 'districts';

}