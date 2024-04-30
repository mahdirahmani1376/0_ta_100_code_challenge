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
        $data = ($this->getTaxExcludeAction)($amount);
        return response()->json([
            'data' => $data
        ]);
    }
}
