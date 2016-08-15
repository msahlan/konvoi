<?php
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Document extends Eloquent {

    //protected $connection = 'mysql';
    //protected $table = 'devices';
    protected $collection = 'documents';

}