<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseParentController;
use Illuminate\Support\Str;

class BaseController extends BaseParentController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        change_log()->setAction(Str::afterLast(static::class, '\\'));
    }
}