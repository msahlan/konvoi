<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Pickup extends Eloquent {

    protected $collection = 'pickups';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Type',
        'transactionId',
        'accountId',
        'assignmentDate',
        'assignmentDateTs',
        'periodMonth',
        'active',
        'contractName',
        'contractNumber',
        'created',
        'creditor',
        'creditorName',
        'dueDate',
        'installmentAmt',
        'payerId',
        'payerName',
        'pickupAddress',
        'pickupCity',
        'pickupDate',
        'pickupDistrict',
        'pickupProvince',
        'pickupZIP',
        'status'
    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

}