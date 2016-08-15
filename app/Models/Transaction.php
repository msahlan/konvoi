<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Transaction extends Eloquent {

    protected $collection = 'transactions';
    protected $fillable = array('*');
}