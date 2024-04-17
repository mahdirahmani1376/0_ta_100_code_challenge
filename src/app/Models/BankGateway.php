<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankGateway
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property string name
 * @property string name_fa
 * @property string status
 * @property boolean is_direct_payment_provider
 * @property int order
 * @property array config
 * @property int rahkaran_id
 */
class BankGateway extends Model
{
    use SoftDeletes;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    protected $casts = [
        'config' => 'array',
    ];

    protected $fillable = [
        'name',
        'name_fa',
        'config',
        'is_direct_payment_provider',
        'order',
    ];
}
