<?php

namespace App\Integrations\Rahkaran\ValueObjects;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VoucherItem
 * @package App\ValueObjects\Rahkaran
 * @property $BaseCurrencyAmount
 * @property $DL4
 * @property $DL5
 * @property $DL6
 * @property $DLLevel4Title
 * @property $DLLevel5Title
 * @property $Credit
 * @property $Debit
 * @property $Description
 * @property $Description_En
 * @property $SLCode
 * @property mixed PartyRef
 * @property int|mixed TaxAmount
 * @property int|mixed TollAmount
 * @property int|mixed TaxStateType
 * @property int|mixed PurchaseOrSale
 * @property int|mixed ItemOrService
 * @property int|mixed TransactionType
 */
class VoucherItem extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'BaseCurrencyAmount',
        'DL4',
        'DL5',
        'DL6',
        'DLLevel4Title',
        'DLLevel5Title',
        'Credit',
        'Debit',
        'Description',
        'Description_En',
        'SLCode',
        'PartyRef',
        'TaxAmount',
        'TollAmount',
        'TaxStateType',
        'PurchaseOrSale',
        'ItemOrService',
        'TransactionType'
    ];

}
