<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MessageQueue extends Eloquent {

    protected $collection = 'mq';

}