<?php

namespace App\Models;

use App\Integrations\Rahkaran\ValueObjects\Client;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Invoice
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
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
 * @property int $source_invoice
 *
 * @property Collection items
 * @property InvoiceNumber invoiceNumber
 * @property Collection transactions
 * @property HasMany|Collection creditTransactions
 * @property Collection offlineTransactions
 * @property Profile profile
 * @property Client client
 * @property MoadianLog moadianLog
 * @property array available_status_list
 */
class Invoice extends Model
{
    use SoftDeletes;

    const STATUS_CANCELED = 'canceled'; // old status 3
    const STATUS_UNPAID = 'unpaid'; // old status = 0
    const STATUS_PAID = 'paid'; // old status = 1
    const STATUS_DRAFT = 'draft';
    const STATUS_DELETED = 'deleted';
    const STATUS_PAYMENT_PENDING = 'payment_pending';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_COLLECTIONS = 'collections'; // old status = 7

    const STATUS_ALL = 'all';

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

    protected $fillable = [
        'created_at',
        'processed_at',
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
        'source_invoice'
    ];

    protected $appends = [
        'available_status_list'
    ];

    protected $hidden = [
        'rahkaran_id',
    ];

    protected $casts = [
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'due_date'     => 'datetime',
        'paid_at'      => 'datetime',
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

    public function moadianLog(): HasOne
    {
        return $this->hasOne(MoadianLog::class, 'invoice_id', 'id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function offlineTransactions(): HasMany
    {
        return $this->hasMany(OfflineTransaction::class);
    }

    public function getAvailableStatusListAttribute(): array
    {
        $status = [];

        if ($this->rahkaran_id) {
            return $status;
        }

        switch ($this->status) {
            case static::STATUS_DRAFT:
                $status = [
                    static::STATUS_UNPAID,
                    static::STATUS_CANCELED,
                ];
                break;
            case static::STATUS_UNPAID:
                $status = [
                    static::STATUS_CANCELED,
                    static::STATUS_DRAFT,
                    static::STATUS_PAYMENT_PENDING,
                    static::STATUS_COLLECTIONS,
                ];

                if ($this->balance == 0) {
                    $status[] = static::STATUS_PAID;
                }

                break;
            case static::STATUS_CANCELED:
                $status = [
                    static::STATUS_UNPAID
                ];
                break;
            case static::STATUS_PAYMENT_PENDING:
            case static::STATUS_COLLECTIONS:
                if ($this->balance == 0) {
                    $status[] = static::STATUS_PAID;
                }
                $status = [
                    static::STATUS_UNPAID
                ];
                break;
            case static::STATUS_REFUNDED:
            case static::STATUS_PAID:
                $status = [];
                break;
        }

        return $status;
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class, 'invoice_id', 'id');
    }

}
