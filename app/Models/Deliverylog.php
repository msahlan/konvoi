<?php
namespace App\Models;

class Deliverylog extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'delivery_log';
    /*
    public function __construct(){

        $this->table = Config::get('jayon.incoming_delivery_table');

    }
    */

}