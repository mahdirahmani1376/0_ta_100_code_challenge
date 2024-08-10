<?php

namespace App\Models;

use App\Traits\MongoDate;
use Carbon\Carbon;
use MongoDB\Laravel\Eloquent\SoftDeletes;

/**
 * @property string $logable_type
 * @property int $logable_id
 * @property string $action
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon deleted_at
 */
class ClientLog extends AbstractBaseLog
{
    use SoftDeletes, MongoDate;

    protected $connection = "mongodb";

    protected $collection = 'client_changes';

    protected $fillable = [
        "logable_type",
        "logable_id",
        "request",
        "action",
    ];

    protected $casts = [
        "created_at" => 'datetime',
        "updated_at" => 'datetime'
    ];

    public function logable()
    {
        return $this->morphTo();
    }
}
