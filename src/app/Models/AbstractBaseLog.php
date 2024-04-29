<?php

namespace App\Models;

use App\Traits\MongoDate;
use App\Traits\SoftDeletes;
use DateTimeInterface;
use MongoDB\Laravel\Eloquent\Model;


class AbstractBaseLog extends Model
{
    use SoftDeletes, MongoDate;
    protected $connection = "mongodb";


    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
