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

    const STATUS_ = '';
    const STATUSES = [
        self::STATUS_,
        self::STATUS_,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
