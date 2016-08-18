<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Buyer extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'buyers';

    protected $guarded = array('id');

}