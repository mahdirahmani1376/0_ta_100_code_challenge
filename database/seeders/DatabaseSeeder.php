<?php

namespace Database\Seeders;

use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\MainApp\MainAppConfig;
use App\Models\BankAccount;
use App\Models\BankGateway;
use App\Models\InvoiceNumber;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
//        BankAccount::create([
//            'title' => 'بانک ملت',
//            'status' => BankAccount::STATUS_ACTIVE,
//            'display_order' => 1,
//            'sheba_number' => '060560080981000866153001',
//            'account_number' => '809-810-866153-1',
//            'card_number' => '6219-8610-3582-1643',
//            'rahkaran_id' => '213441412414414',
//        ]);
//        BankGateway::create([
//            'name' => 'zibal',
//            'name_fa' => 'زیبال',
//            'status' => BankGateway::STATUS_ACTIVE,
//            'config' => [
//                'status' => 'active',
//                'api_key' => 'ZMVaPzvUZj6Ux3MwT8z98YbSB5kZIzkV',
//                'start_url' => 'https://gateway.zibal.ir/start/',
//                'verify_url' => 'https://gateway.zibal.ir/v1/verify',
//                'merchant_id' => 'zibal',
//                'request_url' => 'https://gateway.zibal.ir/v1/request',
//                'terminal_id' => '2408938',
//            ],
//        ]);
//        InvoiceNumber::create([
//            'invoice_number' => 1,
//            'fiscal_year' => 1402,
//            'status' => InvoiceNumber::STATUS_UNUSED,
//            'type' => InvoiceNumber::TYPE_PAID,
//        ]);
//        InvoiceNumber::create([
//            'invoice_number' => 1,
//            'fiscal_year' => 1402,
//            'status' => InvoiceNumber::STATUS_UNUSED,
//            'type' => InvoiceNumber::TYPE_REFUND,
//        ]);

        MainAppAPIService::storeConfig(MainAppConfig::CRON_AUTO_INVOICE_CANCELLATION_DAYS, 1);
        MainAppAPIService::storeConfig(MainAppConfig::CRON_AUTO_DOMAIN_INVOICE_CANCELLATION_DAYS, 1);
        MainAppAPIService::storeConfig(MainAppConfig::CRON_AUTO_CLOUD_INVOICE_CANCELLATION_DAYS, 1);
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_1, 1);
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_2, 5);
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_3, 10);
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_SMS_1, 5);
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_SMS_2, 15);
    }
}
