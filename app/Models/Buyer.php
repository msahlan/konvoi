<?php
namespace App\Models;
class Buyer extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'buyers';

    protected $guarded = array('id');

}