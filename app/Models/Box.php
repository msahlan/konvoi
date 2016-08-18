<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Box extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'box_list';

}