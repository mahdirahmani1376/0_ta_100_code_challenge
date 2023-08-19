<?php


return [
    'transactions' => [
        'limit' => [
            'min' => env('PAYMENT_MIN_TRANSACTION_LIMIT', 1000),
            'max' => env('PAYMENT_MAX_TRANSACTION_LIMIT', 100000000),
        ],
    ],
    'invoice_number' => [
        'current_invoice_id' => env('INVOICE_NUMBER_CURRENT_INVOICE_ID'), // TODO default value should be read from config table
        'current_fiscal_year' => env('INVOICE_NUMBER_CURRENT_FISCAL_YEAR'), // TODO default value should be read from config table
    ],
];


















