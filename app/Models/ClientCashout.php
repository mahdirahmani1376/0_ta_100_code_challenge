<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ClientCashout
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property int client_id
 * @property int client_bank_account_id
 * @property int zarinpal_payout_id
 * @property int admin_id
 * @property float amount
 * @property string admin_note
 * @property string status
 * @property boolean rejected_by_bank
 */
class ClientCashout extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const STATUS_PAYOUT_COMPLETE = 'complete';
    const STATUS_REJECTED = 'rejected';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_PENDING,
        self::STATUS_PAYOUT_COMPLETE,
        self::STATUS_REJECTED,
    ];

    public function clientBankAccount(): BelongsTo
    {
        return $this->belongsTo(ClientBankAccount::class);
    }
}
