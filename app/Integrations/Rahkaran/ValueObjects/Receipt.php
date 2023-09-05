<?php

namespace App\Integrations\Rahkaran\ValueObjects;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Receipt
 * @package App\ValueObjects\Rahkaran
 * @property $BranchID
 * @property $CashID
 * @property $CounterPartDLCode
 * @property $Date
 * @property $ApproveDate
 * @property $Description
 * @property $Description_En
 * @property $IsApproved
 * @property $Number
 * @property $SecondNumber
 * @property $TotalOperationalCurrencyAmount
 * @property $StandardDescriptionPattern
 * @property ReceiptDeposit[] $ReceiptDeposits
 */
class Receipt extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'BranchID',
        'CashID',
        'CounterPartDLCode',
        'Date',
        'ApproveDate',
        'Description',
        'Description_En',
        'IsApproved',
        'Number',
        'SecondNumber',
        'ReceiptCashMoneys',
        'StandardDescriptionPattern',
        'TotalOperationalCurrencyAmount'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'Date' => 'datetime'
    ];

    /**
     * Gets Receipt Deposit collection
     *
     * @param $receipt_deposits
     * @return Collection|array|null
     */
    public function getReceiptDepositsAttribute($receipt_deposits)
    {
        return $receipt_deposits;
    }

    /**
     * Sets Receipt Deposit collection
     *
     * @param $receipt_deposits
     * @return $this
     */
    public function setReceiptDepositsAttribute($receipt_deposits): Receipt
    {
        $this->attributes['ReceiptDeposits'] = $receipt_deposits instanceof Collection ? $receipt_deposits : new Collection($receipt_deposits);
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
