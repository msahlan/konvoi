<?php
namespace App\Models;
//use Illuminate\Database\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Cardtype extends Eloquent {

    //protected $connection = 'mysql';
    //protected $table = 'devices';
    protected $collection = 'cardtypes';

}