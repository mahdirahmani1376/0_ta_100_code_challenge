<?php

namespace App\Models;

use Carbon\Carbon;
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
    use SoftDeletes;

    const TYPE_MASS_PAYMENT_INVOICE = 'MassPaymentInvoice';
    const TYPE_ADD_CLIENT_CREDIT = 'AddClientCredit';
    const TYPE_ADD_CLOUD_CREDIT = 'AddCloudCredit';
    const TYPE_DOMAIN_SERVICE = 'DomainService';
    const TYPE_HOSTING = 'Hosting'; // TODO => ProductService
    const TYPE_PRODUCT_SERVICE = 'ProductService';
    const TYPE_ADMIN_TIME = 'AdminTime';
    const TYPE_CLOUD = 'Cloud';
    const TYPE_CLOUD_IP = 'CloudIp';
    const TYPE_REFUND_DOMAIN = 'RefundDomain';
    const TYPE_ADD_FUNDS = 'AddFunds';
    const TYPE_DOMAIN_ADDON_DNS = 'DomainAddonDNS';
    const TYPE_PROMO_DOMAIN = 'PromoDomain';
    const TYPE_DOMAIN_TRANSFER = 'DomainTransfer';
    const TYPE_DOMAIN_ADDON_IP = 'DomainAddonIDP';
    const TYPE_DOMAIN_REGISTER = 'DomainRegister';
    const TYPE_DOMAIN = 'Domain';
    const TYPE_UPGRADE = 'Upgrade';
    const TYPE_DOMAIN_HOSTING = 'PromoHosting';
    const TYPE_ITEM = 'Item';
    const TYPE_PRODUCT_SERVICE_UPGRADE = 'ProductServiceUpgrade';
    const TYPE_CHANGE_SERVICE = 'ChangeService';
    const TYPE_PARTNER_DISCOUNT = 'PartnerDiscount';
    const TYPE_PARTNER_COMMISSION = 'PartnerCommission';
    const TYPE_PARTNER_PAYMENT = 'PartnerPayment';
    const TYPE_AFFILIATION = 'affiliation';

    public const Invoiceable_Types = [
        self::TYPE_MASS_PAYMENT_INVOICE,
        self::TYPE_ADD_CLIENT_CREDIT,
        self::TYPE_ADD_CLOUD_CREDIT,
        self::TYPE_DOMAIN_SERVICE,
        self::TYPE_HOSTING,
        self::TYPE_PRODUCT_SERVICE,
        self::TYPE_ADMIN_TIME,
        self::TYPE_CLOUD,
        self::TYPE_CLOUD_IP,
        self::TYPE_REFUND_DOMAIN,
        self::TYPE_ADD_FUNDS,
        self::TYPE_DOMAIN_ADDON_DNS,
        self::TYPE_PROMO_DOMAIN,
        self::TYPE_DOMAIN_TRANSFER,
        self::TYPE_DOMAIN_ADDON_IP,
        self::TYPE_DOMAIN_REGISTER,
        self::TYPE_DOMAIN,
        self::TYPE_UPGRADE,
        self::TYPE_DOMAIN_HOSTING,
        self::TYPE_ITEM,
        self::TYPE_PRODUCT_SERVICE_UPGRADE,
        self::TYPE_CHANGE_SERVICE,
        self::TYPE_PARTNER_DISCOUNT,
        self::TYPE_PARTNER_COMMISSION,
        self::TYPE_PARTNER_PAYMENT,
        self::TYPE_AFFILIATION,
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'from_date'  => 'datetime',
        'to_date'    => 'datetime',
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
        $hostingTypes = [self::TYPE_HOSTING, self::TYPE_UPGRADE, self::TYPE_DOMAIN_HOSTING];
        $domainTypes = [self::TYPE_DOMAIN, self::TYPE_DOMAIN_REGISTER, self::TYPE_DOMAIN_ADDON_IP, self::TYPE_DOMAIN_TRANSFER, self::TYPE_PROMO_DOMAIN, self::TYPE_DOMAIN_ADDON_DNS, self::TYPE_DOMAIN_SERVICE];
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
