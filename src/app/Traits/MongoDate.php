<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use MongoDB\BSON\UTCDateTime;

/**
 * Trait MongoDate
 * @package App\Traits
 */
trait MongoDate
{

    /**
     * @param Builder $builder
     * @param $date
     * @param string $column
     * @return Builder
     */
    public function scopeDate(Builder $builder, $date, $column = "created_at"): Builder
    {
        $start = Carbon::create($date)->startOfDay()->format('Uu');
        $end = Carbon::create($date)->endOfDay()->format('Uu');

        $start = substr($start, 0, -3);
        $end = substr($end, 0, -3);

        $builder->whereBetween($column, [
            new UTCDateTime($start),
            new UTCDateTime($end)
        ]);

        return $builder;
    }

}
