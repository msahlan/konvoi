<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Importsession extends Eloquent {

    protected $collection = 'importsessions';
    protected $fillable = array('*');

}