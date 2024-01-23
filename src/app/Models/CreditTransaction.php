<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CreditTransaction
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property int profile_id
 * @property int wallet_id
 * @property int invoice_id
 * @property int admin_id
 * @property float amount
 * @property string description

 * @property Invoice invoice
 * @property Profile profile
 */
class CreditTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'profile_id',
        'wallet_id',
        'invoice_id',
        'admin_id',
        'amount',
        'description',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
