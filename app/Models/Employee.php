<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Employee extends Eloquent {

    protected $collection = 'employees';

}