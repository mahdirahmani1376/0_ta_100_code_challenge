<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property int invoice_id
 * @property string status
 * @property string reference_code
 * @property string error
 * @property string tax_id
 */
class MoadianLog extends Model
{
    public const STATUS_INIT = 'init';
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILURE = 'failure';

    public const STATUSES = [
        self::STATUS_INIT,
        self::STATUS_PENDING,
        self::STATUS_SUCCESS,
        self::STATUS_FAILURE,
    ];

    protected $fillable = [
        'invoice_id',
        'status',
        'reference_code',
        'error',
        'tax_id',
    ];
}
