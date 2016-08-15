<?php
namespace App\Models;
class Application extends Eloquent {

    protected $connection = 'mysql';
    protected $table = 'applications';
/*
    protected $connection = 'mysql';
    protected $table = '';

    public function __construct(){

        $this->table = Config::get('jayon.incoming_delivery_table');

    }
*/

}