<?php

namespace App\Traits;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Trait HasDatetime
 * @package App\Traits
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method Builder|static createdAt(?string $date)
 * @method Builder|static updatedAt(?string $date)
 * @mixin Model
 */
trait HasDatetime
{
    public function scopeCreatedAt(Builder $builder, ?string $date = null): Builder
    {
        if ($date) {
            $builder->whereDate('created_at', $date);
        }

        return $builder;
    }

    public function scopeUpdatedAt(Builder $builder, ?string $date = null): Builder
    {
        if ($date) {
            $builder->where('updated_at', $date);
        }

        return $builder;
    }

    /**
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
