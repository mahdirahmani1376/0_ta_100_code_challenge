<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class InvoiceNumber
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property int invoice_id
 * @property string invoice_number
 * @property string fiscal_year
 * @property string status
 * @property string type
 *
 * @property Invoice invoice
 */
class InvoiceNumber extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_UNUSED = 'unused';
    const STATUS_USED = 'used';

    const STATUSES = [
        self::STATUS_UNUSED,
        self::STATUS_USED,
    ];

    const TYPE_PAID = 'paid';
    const TYPE_REFUND = 'refund';

    const TYPES = [
        self::TYPE_PAID,
        self::TYPE_REFUND,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
