<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Video extends Eloquent {

    protected $collection = 'videos';

}