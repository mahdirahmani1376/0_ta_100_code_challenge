<?php


return [
    'transactions' => [
        'limit' => [
            'min' => env('PAYMENT_MIN_TRANSACTION_LIMIT', 1000),
            'max' => env('PAYMENT_MAX_TRANSACTION_LIMIT', 100000000),
        ],
    ],
    'invoice_number' => [
        'current_invoice_id' => env('INVOICE_NUMBER_CURRENT_INVOICE_ID', 0), // TODO default value should be read from config table
        'current_fiscal_year' => env('INVOICE_NUMBER_CURRENT_FISCAL_YEAR', 1402), // TODO default value should be read from config table
    ],
    'bank_gateway' => [ // TODO callback urls should be main-app urls
        'callback_url' => env('MAINAPP_PUBLIC_BASE_URL') . '/api/finance-service/public/bank-gateway/{gateway}/callback/{transaction}/{source}',
        'cloud_callback_url' => env('MAINAPP_PUBLIC_BASE_URL') . '/api/finance-service/public/bank-gateway/{gateway}/callback/{transaction}/{source}',
        'result_redirect_url' => env('FRONT_END_BASE_URL') . '/callback/{transaction}',
        'result_cloud_redirect_url' => env('FRONT_END_CLOUD_BASE_URL') . '/callback/{transaction}',
    ],
];
