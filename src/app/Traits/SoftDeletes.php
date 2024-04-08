<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes as EloquentSoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Trait SoftDeletes
 * @package App\Traits
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @mixin Model
 */
trait SoftDeletes
{
    use EloquentSoftDeletes;
    use HasDatetime;
}
