<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Menu extends Eloquent {

    protected $collection = 'menus';
    protected $fillable = array('*');

}