<?php

namespace App\Models;

use Carbon\Carbon;
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
 * @property int profile_id
 * @property int client_bank_account_id
 * @property int zarinpal_payout_id
 * @property int admin_id
 * @property float amount
 * @property string admin_note
 * @property string status
 * @property string source
 * @property array actions
 * @property boolean rejected_by_bank
 */
class ClientCashout extends Model
{
    use SoftDeletes;

    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const STATUS_PAYOUT_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_PENDING,
        self::STATUS_PAYOUT_COMPLETED,
        self::STATUS_REJECTED,
    ];

    const ACTION_ACCEPT = 'accept';
    const ACTION_REJECT = 'reject';
    const ACTION_PENDING = 'pending';
    const ACTION_REJECT_BANK = 'reject_bank';

    protected $fillable = [
        'profile_id',
        'client_bank_account_id',
        'zarinpal_payout_id',
        'admin_id',
        'amount',
        'admin_note',
        'status',
        'rejected_by_bank',
        'source'
    ];

    public function getActionsAttribute(): array
    {
       if ($this->status === self::STATUS_REJECTED)
            return [self::ACTION_REJECT];
        elseif ($this->status === self::STATUS_PENDING)
            return [self::ACTION_ACCEPT, self::ACTION_REJECT, self::ACTION_REJECT_BANK,];
        else
            return [];
    }

    public function clientBankAccount(): BelongsTo
    {
        return $this->belongsTo(ClientBankAccount::class);
    }
}
