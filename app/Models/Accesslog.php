<?php
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Accesslog extends Eloquent {

    protected $collection = 'accesslog';

}