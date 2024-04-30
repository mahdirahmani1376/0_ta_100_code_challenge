<?php

namespace App\Actions\Tax;

use App\Services\Tax\GetTaxExcludeService;

class GetTaxExcludeAction
{
    public function __construct(
        private readonly GetTaxExcludeService $getTaxExcludeService
    )
    {
    }

    public function __invoke($amount): float|int
    {
        return ($this->getTaxExcludeService)($amount);
    }

}