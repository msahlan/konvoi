<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

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