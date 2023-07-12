<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property int client_id
 * @property int invoice_id
 * @property int bank_account_id
 * @property string status
 * @property string payment_method
 * @property string tracking_code
 * @property string mobile
 * @property string description
 *
 * @property Invoice invoice
 * @property BankAccount bankAccount
 */
class OfflineTransaction extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending'; // old status = 0
    const STATUS_CONFIRMED = 'confirmed';// old status = 1
    const STATUS_REJECTED = 'rejected';// old status = 2

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
