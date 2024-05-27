<?php

namespace App\Actions;

use Illuminate\Support\Str;

class BaseAction
{
    public function __construct()
    {
        change_log()->setAction(Str::afterLast(static::class, '\\'));
    }
}