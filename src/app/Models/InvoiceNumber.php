<?php

namespace App\Models;

use Carbon\Carbon;
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
    use SoftDeletes;

    /**
     * Pending   :  ready for assignment
     * Allocated :  allocated for future assignment
     * Active    :  assigned to an invoice
     */
    const STATUS_PENDING = '0';
    const STATUS_ALLOCATED = '2';
    const STATUS_ACTIVE = '1';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE,
    ];

    const TYPE_PAID = 'paid';
    const TYPE_REFUNDED = 'refunded';

    const TYPES = [
        self::TYPE_PAID,
        self::TYPE_REFUNDED,
    ];

    protected $fillable = [
        'id',
        'invoice_number',
        'fiscal_year',
        'type',
        'invoice_id',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
