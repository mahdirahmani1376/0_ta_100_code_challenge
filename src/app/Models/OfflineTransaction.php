<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CLass OfflineTransaction
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon paid_at
 * @property int profile_id
 * @property int invoice_id
 * @property int transaction_id
 * @property int bank_account_id
 * @property int admin_id
 * @property string amount
 * @property string status
 * @property string payment_method
 * @property string tracking_code
 * @property string mobile
 * @property string account_name
 * @property string description
 * @property Invoice invoice
 * @property Transaction transaction
 * @property BankAccount bankAccount
 */
class OfflineTransaction extends Model
{
    public const PAYMENT_GATEWAY_NAME = 'offline_bank';

    const PAYMENT_METHOD_OFFLINE_BANK = 'offline-bank';
    const PAYMENT_METHOD_SHABA_NUMBER = 'shaba-number';
    const PAYMENT_METHOD_CARD_TO_CARD = 'card-to-card';
    const PAYMENT_METHOD_ACCOUNT_NUMBER = 'account-number';
    const PAYMENT_METHODS = [
        self::PAYMENT_METHOD_OFFLINE_BANK,
        self::PAYMENT_METHOD_SHABA_NUMBER,
        self::PAYMENT_METHOD_CARD_TO_CARD,
        self::PAYMENT_METHOD_ACCOUNT_NUMBER,
    ];
    const STATUS_PENDING = 'pending'; // old status = 0
    const STATUS_CONFIRMED = 'confirmed';// old status = 1
    const STATUS_REJECTED = 'rejected';// old status = 2

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_REJECTED,
    ];

    protected $fillable = [
        'paid_at',
        'profile_id',
        'invoice_id',
        'bank_account_id',
        'admin_id',
        'status',
        'payment_method',
        'tracking_code',
        'mobile',
        'description',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];


    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
