<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Trips extends Eloquent
{
    protected $collection = 'trips';
}