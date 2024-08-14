<?php

namespace App\Integrations\Rahkaran\ValueObjects;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Voucher
 * @package App\ValueObjects\Rahkaran
 * @property $AuxiliaryNumber
 * @property $BranchRef
 * @property $Creator
 * @property $CreatorName
 * @property $Date
 * @property $Description
 * @property $Description_En
 * @property $FiscalYearRef
 * @property $IsCurrencyBased
 * @property $IsExternal
 * @property $LedgerRef
 * @property $Number
 * @property $State
 * @property $StateTitle
 * @property $VoucherTypeCode
 * @property $VoucherTypeOwnerSystem
 * @property $VoucherTypeRef
 * @property VoucherItem[]|Collection $VoucherItems
 */
class Voucher extends Model
{
    public const TYPE_SALE = 'sale';
    public const TYPE_REFUND = 'refund';

    /**
     * @var string[]
     */
    protected $fillable = [
        'AuxiliaryNumber',
        'BranchRef',
        'Creator',
        'CreatorName',
        'Date',
        'Description',
        'Description_En',
        'FiscalYearRef',
        'IsCurrencyBased',
        'IsExternal',
        'LedgerRef',
        'Number',
        'State',
        'StateTitle',
        'VoucherTypeCode',
        'VoucherTypeOwnerSystem',
        'VoucherTypeRef',
        'VoucherItems',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'Date' => 'datetime'
    ];

    /**
     * Gets voucher item collection
     *
     * @param $voucher_items
     * @return Collection
     */
    public function getVoucherItemsAttribute($voucher_items): Collection
    {
        return $voucher_items instanceof Collection ? $voucher_items : new Collection();
    }

    /**
     * Sets voucher item collection
     *
     * @param $voucher_items
     * @return $this
     */
    public function setVoucherItemsAttribute($voucher_items): Voucher
    {
        $this->attributes['VoucherItems'] = $voucher_items instanceof Collection ? $voucher_items : new Collection($voucher_items);
        return $this;
    }

    /**
     * Adds Voucher Item to current voucher
     *
     * @param VoucherItem $voucherItem
     */
    public function addVoucherItem(VoucherItem $voucherItem)
    {
        $voucher_items = $this->VoucherItems;

        if ($voucherItem->Credit || $voucherItem->Debit) {
            $voucher_items->add($voucherItem);
        }

        $this->VoucherItems = $voucher_items;
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
