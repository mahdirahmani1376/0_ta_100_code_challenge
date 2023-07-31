<?php


return [
    'transactions' => [
        'limit' => [
            'min' => env('PAYMENT_MIN_TRANSACTION_LIMIT', 1000),
            'max' => env('PAYMENT_MAX_TRANSACTION_LIMIT', 100000000)
        ]
    ],
];


















