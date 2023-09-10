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
        'callback_url' => 'http://localhost:6051/api/finance-service/public/gateway/callback/{transaction}/{gateway}/{source}',
        'cloud_callback_url' => 'http://localhost:6051/api/finance-service/public/gateway/callback/{transaction}/{gateway}/{source}', // TODO maybe remove cloud url?
        'result_redirect_url' => 'redirect',
        'result_cloud_redirect_url' => 'cloud-redirect',
    ],
];


















