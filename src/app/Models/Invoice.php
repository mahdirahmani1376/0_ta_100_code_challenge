<?php

namespace App\Models;

use App\Integrations\Rahkaran\ValueObjects\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property Carbon processed_at
 * @property int profile_id
 * @property int rahkaran_id
 * @property string payment_method
 * @property double balance
 * @property double total
 * @property double sub_total
 * @property int tax_rate
 * @property double tax
 * @property string status
 * @property boolean is_mass_payment
 * @property int admin_id
 * @property boolean is_credit
 * @property string note
 *
 * @property Collection items
 * @property InvoiceNumber invoiceNumber
 * @property Collection transactions
 * @property Collection offlineTransactions
 * @property Profile profile
 * @property Client client
 * @property MoadianLog moadianLog
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

    const PAYMENT_METHOD_CREDIT = 'client_credit';

    const DEFAULT_TAX_RATE = 11;

    protected $fillable = [
        'created_at',
        'due_date',
        'paid_at',
        'profile_id',
        'payment_method',
        'total',
        'sub_total',
        'tax_rate',
        'tax',
        'status',
        'is_mass_payment',
        'admin_id',
        'is_credit',
        'note',
    ];

    protected $hidden = [
        'rahkaran_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }


    public function invoiceNumber(): HasOne
    {
        return $this->hasOne(InvoiceNumber::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function offlineTransactions(): HasMany
    {
        return $this->hasMany(OfflineTransaction::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function moadianLog(): HasOne
    {
        return $this->hasOne(MoadianLog::class, 'invoice_id', 'id');
    }
}
