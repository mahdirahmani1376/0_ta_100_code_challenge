<?php

namespace App\Integrations\MainApp;

use Illuminate\Support\Facades\Cache;

class MainAppConfig
{
    const CRON_AUTO_INVOICE_CANCELLATION_DAYS = 'CRON_AUTO_INVOICE_CANCELLATION_DAYS';
    const CRON_AUTO_DOMAIN_INVOICE_CANCELLATION_DAYS = 'CRON_AUTO_DOMAIN_INVOICE_CANCELLATION_DAYS';
    const CRON_AUTO_CLOUD_INVOICE_CANCELLATION_DAYS = 'CRON_AUTO_CLOUD_INVOICE_CANCELLATION_DAYS';
    const CRON_FINANCE_INVOICE_REMINDER_EMAIL_1 = 'CRON_FINANCE_INVOICE_REMINDER_EMAIL_1';
    const CRON_FINANCE_INVOICE_REMINDER_EMAIL_2 = 'CRON_FINANCE_INVOICE_REMINDER_EMAIL_2';
    const CRON_FINANCE_INVOICE_REMINDER_EMAIL_3 = 'CRON_FINANCE_INVOICE_REMINDER_EMAIL_3';
    const CRON_FINANCE_INVOICE_REMINDER_SMS_1 = 'CRON_FINANCE_INVOICE_REMINDER_SMS_1';
    const CRON_FINANCE_INVOICE_REMINDER_SMS_2 = 'CRON_FINANCE_INVOICE_REMINDER_SMS_2';

    public static function get($key, $default = null)
    {
        $value = Cache::get($key);
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
