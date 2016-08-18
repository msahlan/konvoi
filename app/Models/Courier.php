<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Courier extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'couriers';
/*
    protected $connection = 'mysql';
    protected $table = '';

    public function __construct(){

        $this->table = Config::get('jayon.incoming_delivery_table');

    }
*/

}