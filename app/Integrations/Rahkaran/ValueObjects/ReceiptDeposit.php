<?php

namespace App\Integrations\Rahkaran\ValueObjects;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReceiptCashMoney
 * @package App\ValueObjects\Rahkaran
 * @property $AccountingOperationID
 * @property $Amount
 * @property $BankAccountID
 * @property $BaseCurrencyAbbreviation
 * @property $BaseCurrencyExchangeRate
 * @property $CashFlowFactorID
 * @property $CounterPartDLCode
 * @property $Date
 * @property $Description
 * @property $Description_En
 * @property $Number
 * @property $OperationalCurrencyExchangeRate
 */
class ReceiptDeposit extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'AccountingOperationID',
        'Amount',
        'BankAccountID',
        'BaseCurrencyAbbreviation',
        'BaseCurrencyExchangeRate',
        'CashFlowFactorID',
        'CounterPartDLCode',
        'Date',
        'Description',
        'Description_En',
        'Number',
        'OperationalCurrencyExchangeRate',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'AccountingOperationID' => 19,
        'BankAccountID'         => 17,
        'Description'           => '',
        'Description_En'        => ''
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'Date' => 'datetime'
    ];

    /**
     * Changes date time format
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return '/Date(' . $date->format('U') . '000+0430)/';
    }
}
