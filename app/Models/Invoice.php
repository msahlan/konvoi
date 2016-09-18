<?php
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Invoice extends Eloquent {

    //protected $connection = 'mysql';
    //protected $table = 'devices';
    protected $collection = 'invoices';

}