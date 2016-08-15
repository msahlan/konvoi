<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Approval extends Eloquent {

    protected $collection = 'approvalrequest';

}