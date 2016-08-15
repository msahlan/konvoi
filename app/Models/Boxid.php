<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Boxid extends Eloquent {

    //protected $connection = 'mysql';
    //protected $table = 'box_list';

    protected $collection = 'boxidlog';

}