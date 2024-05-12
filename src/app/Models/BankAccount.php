<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankAccount
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property string title
 * @property string status
 * @property int order
 * @property string sheba_number
 * @property string account_number
 * @property string card_number
 * @property int rahkaran_id
 */
class BankAccount extends Model
{
    use SoftDeletes;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    protected $hidden = [
      'rahkaran_id'
    ];

    protected $fillable = [
        'title',
        'order',
        'sheba_number',
        'account_number',
        'card_number',
        'rahkaran_id',
    ];
}
