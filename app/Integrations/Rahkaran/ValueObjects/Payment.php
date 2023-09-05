<?php

namespace App\Integrations\Rahkaran\ValueObjects;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 * @package App\ValueObjects\Rahkaran
 * @property $BranchID
 * @property $CashID
 * @property $CounterPartDLCode
 * @property $Date
 * @property $Description
 * @property $Description_En
 * @property $IsApproved
 * @property $Number
 * @property $SecondNumber
 * @property PaymentDeposit[] $PaymentDeposits
 */
class Payment extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'BranchID',
        'CashID',
        'CounterPartDLCode',
        'Date',
        'Description',
        'Description_En',
        'IsApproved',
        'Number',
        'SecondNumber',
        'PaymentCashMoneys'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'Date' => 'datetime'
    ];

    /**
     * Gets Payment Deposit collection
     *
     * @param $payment_deposits
     * @return Collection|array|null
     */
    public function getPaymentDepositsAttribute($payment_deposits)
    {
        return $payment_deposits;
    }

    /**
     * Sets Payment Deposit collection
     *
     * @param $payment_deposits
     * @return $this
     */
    public function setPaymentDepositsAttribute($payment_deposits): Payment
    {
        $this->attributes['PaymentDeposits'] = $payment_deposits instanceof Collection ? $payment_deposits : new Collection($payment_deposits);
        return $this;
    }

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
