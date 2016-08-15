<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Activelog extends Eloquent {

    protected $collection = 'log';

}