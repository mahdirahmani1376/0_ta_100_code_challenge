<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Transaction
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property int client_id
 * @property int invoice_id
 * @property int rahkaran_id
 * @property float amount
 * @property string status
 * @property string description
 * @property string ip
 * @property string tracking_code
 * @property string reference_id
 */
class Transaction extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const STATUS_REFUND = 'refund';
    const STATUS_PENDING_BANK_VERIFY = 'pending_bank_verify';
    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_SUCCESS,
        self::STATUS_FAIL,
        self::STATUS_REFUND,
        self::STATUS_PENDING_BANK_VERIFY,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
