<?php

namespace App\Services\Tax;

use App\Integrations\MainApp\MainAppConfig;

class GetTaxExcludeService
{
    /**
     * Returns tax-excluded amount from given tax included amount
     *
     * @param $amount
     * @return float|int
     */
    public function __invoke($amount): float|int
    {
        return (($amount * 100) / (MainAppConfig::get(MainAppConfig::FINANCE_TAX_TOTAL_PERCENT) + 100));
    }
}