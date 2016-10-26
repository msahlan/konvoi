<?php
namespace App\Models;
//use Illuminate\Foundation\Auth\User as Authenticatable;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Creditor extends Eloquent
{

    protected $collection = 'creditors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'coName',
        'coPhone',
        'coFax',
        'coUrl',
        'address_1',
        'address_2',
        'phone',
        'fax',
        'city',
        'province',
        'countryOfOrigin',
        'pic',
        'picId',
        'picName'
    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
