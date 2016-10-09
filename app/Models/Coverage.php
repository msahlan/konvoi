<?php
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Coverage extends Eloquent {

    //protected $connection = 'mysql';
    //protected $table = 'devices';
    protected $collection = 'districts';

}