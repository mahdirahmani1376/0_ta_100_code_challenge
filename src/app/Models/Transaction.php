<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Transaction
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property int profile_id
 * @property int invoice_id
 * @property int rahkaran_id
 * @property float amount
 * @property string status
 * @property string payment_method
 * @property string description
 * @property string ip
 * @property string tracking_code
 * @property string reference_id
 * @property string callback_url
 *
 * @property Invoice invoice
 * @property OfflineTransaction offlineTransaction
 */
class Transaction extends Model
{
    public const PREFIX_CREDIT_TRANSACTION = 'CREDIT_TRANSACTION_';
    const PAYMENT_METHOD_OFFLINE = 'offline';
    const PAYMENT_METHOD_BARTER = 'barter';
    const PAYMENT_METHOD_CREDIT = 'client_credit';
    const PAYMENT_METHOD_INSURANCE = 'insurance';
    const PAYMENT_METHODS = [
        self::PAYMENT_METHOD_OFFLINE,
        self::PAYMENT_METHOD_BARTER,
        self::PAYMENT_METHOD_CREDIT,
        self::PAYMENT_METHOD_INSURANCE
    ];
    const STATUS_PENDING = 'pending'; // old status = 0
    const STATUS_SUCCESS = 'success'; // old status = 1
    const STATUS_FAIL = 'fail'; // old status = 2
    const STATUS_REFUND = 'refund'; // old status 30

    public const STATUS_FRAUD = 'fraud'; // old status 21
    const STATUS_PENDING_BANK_VERIFY = 'pending_bank_verify'; // old status 6
    const STATUS_CANCELED = 'canceled';
    const STATUSES = [
        self::STATUS_FRAUD,
        self::STATUS_PENDING,
        self::STATUS_SUCCESS,
        self::STATUS_FAIL,
        self::STATUS_REFUND,
        self::STATUS_PENDING_BANK_VERIFY,
        self::STATUS_CANCELED,
    ];

    protected $fillable = [
        'profile_id',
        'created_at',
        'invoice_id',
        'rahkaran_id',
        'amount',
        'status',
        'payment_method',
        'description',
        'ip',
        'tracking_code',
        'reference_id',
        'callback_url'
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function offlineTransaction(): BelongsTo
    {
        return $this->belongsTo(OfflineTransaction::class, 'id', 'transaction_id');
    }
}
