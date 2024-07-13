<?php

namespace App\Models;

use App\Traits\MongoDate;
use Carbon\Carbon;
use MongoDB\Laravel\Eloquent\SoftDeletes;

/**
* @property string $logable_type
* @property int $logable_id
* @property string $request
* @property string $before
* @property string $after
* @property int $admin_user_id
* @property string $action
* @property Carbon $created_at
* @property Carbon $updated_at
* @property Carbon deleted_at
 */
class AdminLog extends AbstractBaseLog
{
    use SoftDeletes, MongoDate;

    protected $connection = "mongodb";

    protected $collection = 'admin_changes';

    protected $fillable = [
        "logable_type",
        "logable_id",
        "request",
        "before",
        "after",
        "admin_user_id",
        "action",
    ];

    protected $casts = [
        "created_at" => 'datetime',
        "updated_at" => 'datetime'
    ];
}
