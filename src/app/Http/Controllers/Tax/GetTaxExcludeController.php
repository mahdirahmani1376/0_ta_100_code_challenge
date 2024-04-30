<?php

namespace App\Http\Controllers\Tax;

use App\Actions\Tax\GetTaxExcludeAction;
use App\Http\Controllers\Controller;

class GetTaxExcludeController extends Controller
{
    public function __construct(
        private readonly GetTaxExcludeAction $getTaxExcludeAction
    )
    {
    }

    public function __invoke($amount)
    {
        $value = ($this->getTaxExcludeAction)($amount);
        return response()->json(['tax_exclude' => $value]);
    }
}
