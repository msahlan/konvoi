<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Credittype extends Eloquent {

    protected $collection = 'credittypes';

    protected $fillable = [
        'programName',
        'creditor',
        'Type',
        'createdDate',
        'lastUpdate',
        'ownerId',
        'ownerName',
        'creditorName'
    ];



}