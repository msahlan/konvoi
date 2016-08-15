<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Shipment extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'delivery_order_active';

    protected $guarded = array('id');
    /*
    public function __construct(){

        $this->table = Config::get('jayon.incoming_delivery_table');

    }
    */

}