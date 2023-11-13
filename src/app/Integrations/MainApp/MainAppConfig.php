<?php

namespace App\Integrations\MainApp;

use Illuminate\Support\Facades\Cache;

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
    const FINANCE_INVOICE_CREATE_MESSAGE = 'FINANCE_INVOICE_CREATE_MESSAGE';
    const FINANCE_INVOICE_CREATE_SUBJECT = 'FINANCE_INVOICE_CREATE_SUBJECT';

    public static function get($key, $default = null, $noCache = false)
    {
        $value = $noCache ? null : Cache::get($key);
        if (is_null($value)) {
            try {
                $value = MainAppAPIService::getConfig($key);
            } catch (\Exception $e) {
                $value = $default;
            }
            Cache::put($key, $value, config('cache.main_app_config_ttl', 3600));
        }

        return $value;
    }
}
