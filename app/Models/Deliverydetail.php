<?php
namespace App\Models;

class Deliverydetail extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'delivery_order_details';
    /*
    public function __construct(){

        $this->table = Config::get('jayon.incoming_delivery_table');

    }
    */

}