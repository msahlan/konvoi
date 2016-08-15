<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Clicklog extends Eloquent {

    protected $collection = 'adclick';

}