<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    public function __construct()
    {
        change_log()->setAction(Str::afterLast(static::class, '\\'));
    }
}