<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Merchant extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'members';

}