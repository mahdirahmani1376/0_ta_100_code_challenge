<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CreditTransaction
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated
 * @property int client_id
 * @property int wallet_id
 * @property int invoice_id
 * @property int admin_id
 * @property float amount
 * @property string description
 */
class CreditTransaction extends Model
{
    use HasFactory;

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
