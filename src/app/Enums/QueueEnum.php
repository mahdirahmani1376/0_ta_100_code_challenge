<?php

namespace App\Enums;

enum QueueEnum: string
{
    const PROCESS_INVOICE_NUMBER = 'process_invoices_number';
    const PROCESS_INVOICE_REMINDER = 'process_invoice_reminder';
    const PROCESS_LOGS = 'update_system_log';

    const WORKER_1 = [
        self::PROCESS_LOGS
    ];

    const WORKER_2 = [
        self::PROCESS_INVOICE_REMINDER,
    ];

    const WORKER_3 = [
        self::PROCESS_INVOICE_NUMBER
    ];
}
