<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Tag extends Eloquent {

    protected $collection = 'tags';
    //protected $fillable = array('*');

}