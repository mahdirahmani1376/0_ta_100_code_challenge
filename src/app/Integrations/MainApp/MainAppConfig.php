<?php

namespace App\Integrations\MainApp;

use Illuminate\Support\Facades\Cache;
use Throwable;

class MainAppConfig
{
    const CRON_FINANCE_INVOICE_REMINDER_HOUR = 'CRON_FINANCE_INVOICE_REMINDER_HOUR';
    const CRON_FINANCE_INVOICE_REMINDER_EMAIL = 'CRON_FINANCE_INVOICE_REMINDER_EMAIL';
    const CRON_FINANCE_INVOICE_REMINDER_EMAIL_SUBJECT = 'CRON_FINANCE_INVOICE_REMINDER_EMAIL_SUBJECT';
    const CRON_FINANCE_INVOICE_REMINDER_EMAIL_MESSAGE = 'CRON_FINANCE_INVOICE_REMINDER_EMAIL_MESSAGE';
    const CRON_FINANCE_INVOICE_REMINDER_EMAIL_LINK = 'CRON_FINANCE_INVOICE_REMINDER_EMAIL_LINK';
    const CRON_FINANCE_INVOICE_REMINDER_SMS = 'CRON_FINANCE_INVOICE_REMINDER_SMS';
    const CRON_FINANCE_INVOICE_REMINDER_SMS_MESSAGE = 'CRON_FINANCE_INVOICE_REMINDER_SMS_MESSAGE';
    const CRON_FINANCE_INVOICE_REMINDER_SMS_LINK = 'CRON_FINANCE_INVOICE_REMINDER_SMS_LINK';
    const INVOICE_NUMBER_CURRENT_PAID_INVOICE_NUMBER = 'INVOICE_NUMBER_CURRENT_PAID_INVOICE_NUMBER';
    const INVOICE_NUMBER_CURRENT_REFUNDED_INVOICE_NUMBER = 'INVOICE_NUMBER_CURRENT_REFUNDED_INVOICE_NUMBER';
    const INVOICE_NUMBER_CURRENT_INVOICE_ID = 'INVOICE_NUMBER_CURRENT_INVOICE_ID';
    const INVOICE_NUMBER_CURRENT_FISCAL_YEAR = 'INVOICE_NUMBER_CURRENT_FISCAL_YEAR';
    const FINANCE_SERVICE_DEFAULT_TAX = 'FINANCE_SERVICE_DEFAULT_TAX';
    const RAHKARAN_FISCAL_YEAR_REF = 'FINANCE_RAHKARAN_FISCAL_YEAR_REF';
    const MAX_TRANSACTION_AMOUNT = 'FIANCE_MAX_TRANSACTION_AMOUNT';

    public static function get($key, $default = null, $refresh = false)
    {
        if ($refresh) {
            Cache::forget($key);
        }

        $value = Cache::rememberForever($key, function () use ($key) {
            try {
                return MainAppAPIService::getConfig($key);
            } catch (Throwable $exception) {
                \Log::warning("Get cache value failed ($key) / {$exception->getMessage()}", $exception->getTrace());
                return null;
            }
        });

        return $value ?? $default;
    }
}
