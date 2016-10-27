<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Creditaccount extends Eloquent {

    protected $collection = 'creditaccounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contractNumber',
        'contractName',
        'creditor',
        'Type',
        'programName',
        'bankCard',
        'productDescription',
        'dueDate',
        'installmentAmt',
        'pickupDate',
        'pickupAddress',
        'pickupProvince',
        'pickupCity',
        'pickupDistrict',
        'pickupZIP',
        'active',
        'payerEmail',
        'phone',
        'mobile',
        'creditorName',
        'payerId',
        'payerName'
    ];



}