<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Invoice
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon due_date
 * @property Carbon paid_at
 * @property int client_id
 * @property int rahkaran_id
 * @property string payment_method
 * @property int total
 * @property int sub_total
 * @property int tax_rate
 * @property int tax
 * @property string status
 * @property boolean is_mass_payment
 * @property int admin_id
 * @property boolean is_credit
 *
 * @property InvoiceNumber invoiceNumber
 */
class Invoice extends Model
{
    use HasFactory;

    const STATUS_CANCELED = 'canceled'; // old status 3
    const STATUS_UNPAID = 'unpaid'; // old status = 0
    const STATUS_PAID = 'paid'; // old status = 1
    const STATUS_DRAFT = 'draft';
    const STATUS_DELETED = 'deleted';
    const STATUS_PAYMENT_PENDING = 'payment_pending';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_COLLECTIONS = 'collections'; // old status = 7

    const STATUSES = [
        self::STATUS_CANCELED,
        self::STATUS_UNPAID,
        self::STATUS_PAID,
        self::STATUS_DRAFT,
        self::STATUS_DELETED,
        self::STATUS_PAYMENT_PENDING,
        self::STATUS_REFUNDED,
        self::STATUS_COLLECTIONS,
    ];

    protected $fillable = [
        'due_date',
        'paid_at',
        'client_id',
        'payment_method',
        'total',
        'sub_total',
        'tax_rate',
        'tax',
        'status',
        'is_mass_payment',
        'admin_id',
        'is_credit',
    ];

    protected $hidden = [
        'rahkaran_id',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function invoiceNumber(): HasOne
    {
        return $this->hasOne(InvoiceNumber::class);
    }
}
