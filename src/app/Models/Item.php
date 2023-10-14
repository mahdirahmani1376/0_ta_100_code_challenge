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
 * @method ?string calculationType()
 * @property Invoice invoice
 */
class Item extends Model
{
    use HasFactory;
    use SoftDeletes;

    const TYPE_MASS_PAYMENT_INVOICE = 'MassPaymentInvoice';
    const TYPE_ADD_CLIENT_CREDIT = 'AddClientCredit';
    public const TYPE_ADD_CLOUD_CREDIT = 'AddCloudCredit';
    public const TYPE_DOMAIN_SERVICE = 'DomainService';
    public const TYPE_HOSTING = 'Hosting';
    public const TYPE_ADMIN_TIME = 'AdminTime';
    public const TYPE_CLOUD = 'Cloud';
    public const TYPE_REFUND_DOMAIN = 'RefundDomain';
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

    public function calculationType(): ?string
    {
        $hostingTypes = ["Hosting", "Upgrade", "PromoHosting"];
        $domainTypes = ["Domain", "DomainRegister", "DomainAddonIDP", "DomainTransfer", "PromoDomain", "DomainAddonDNS", "DomainService"];
        if (in_array($this->invoiceable_type, $hostingTypes)) {
            $types = $hostingTypes;
        } elseif (in_array($this->invoiceable_type, $domainTypes)) {
            $types = $domainTypes;
        } else {
            return null;
        }

        return $this->newQuery()
            ->where('id', '<', $this->id)
            ->where('invoiceable_id', $this->invoiceable_id)
            ->whereIn('invoiceable_type', $types)
            ->count() == 0 ? 'register' : 'renew';
    }
}
