<?php

namespace App\Enums;

enum QueueEnum: string
{
    const PROCESS_DEFAULT = 'default';
    const PROCESS_INVOICE_NUMBER = 'process_invoices_number';
    const PROCESS_INVOICE_REMINDER = 'process_invoice_reminder';
    const PROCESS_LOGS = 'update_system_log';
    const PROCESS_INVOICE = 'process_invoice';

    const WORKER_1 = [
        self::PROCESS_LOGS
    ];

    const WORKER_2 = [
        self::PROCESS_INVOICE_REMINDER,
        self::PROCESS_INVOICE_NUMBER,
	self::PROCESS_INVOICE,
	self::PROCESS_DEFAULT
    ];
}
