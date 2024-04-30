<?php

namespace App\Services\Tax;

use App\Integrations\MainApp\MainAppConfig;

class GetTaxExcludeService
{
    /**
     * Returns tax-excluded amount from given tax included amount
     *
     * @param $amount
     * @return array
     */
    public function __invoke($amount)
    {
        $tax = (MainAppConfig::get(MainAppConfig::FINANCE_TAX_TOTAL_PERCENT) + 100);
        return [
            'tax'    => $tax,
            'amount' => (($amount * 100) / $tax)
        ];
    }
}