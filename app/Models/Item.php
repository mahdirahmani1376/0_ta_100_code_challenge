<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Item
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property int invoice_id
 * @property int invoiceable_id
 * @property string invoiceable_type
 * @property float amount
 * @property float discount
 * @property Carbon from_date
 * @property Carbon to_date
 * @property string description
 *
 * @property Invoice invoice
 */
class Item extends Model
{
    use HasFactory;
    use SoftDeletes;
    const TYPE_MASS_PAYMENT_INVOICE    = 'MassPaymentInvoice';
    const TYPE_ADD_CLIENT_CREDIT       = 'AddClientCredit';
    protected $casts = [
        'deleted_at' => 'datetime',
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];

    protected $fillable = [
        'invoice_id',
        'invoiceable_id',
        'invoiceable_type',
        'amount',
        'discount',
        'from_date',
        'to_date',
        'description',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
