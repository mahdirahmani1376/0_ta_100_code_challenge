<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property int profile_id
 * @property string status
 * @property string provider
 * @property array config
 */
class DirectPayment extends Model
{
    const STATUS_INIT = 'init';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const PROVIDER_BAZAAR_PAY = 'bazaarPay';

    protected $fillable = [
        'profile_id',
        'status',
        'provider',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];
}
