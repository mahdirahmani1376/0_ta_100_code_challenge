<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\SoftDeletes;

/**
 * @property $logable_type
 * @property $logable_id
 * @property $before
 * @property $after
 * @property $action
 * @property $created_at
 * @property $updated_at
 */
class FinanceLog extends AbstractBaseLog
{
    const EDIT_INVOICE_ITEM = "edit_invoice_item";
    const CALLBACK_DATA = "gateway_callback";

    use SoftDeletes;

    protected $connection = "mongodb";

    protected $collection = 'finance_logs';

    protected $fillable = [
        "logable_type",
        "logable_id",
        "request",
        "before",
        "after",
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
