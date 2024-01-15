<?php

namespace Database\Seeders;

use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\MainApp\MainAppConfig;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BankGatewaySeeder::class);
//        BankAccount::create([
//            'title' => 'بانک ملت',
//            'status' => BankAccount::STATUS_ACTIVE,
//            'display_order' => 1,
//            'sheba_number' => '060560080981000866153001',
//            'account_number' => '809-810-866153-1',
//            'card_number' => '6219-8610-3582-1643',
//            'rahkaran_id' => '213441412414414',
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

        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_HOUR, 15);
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL, '1,5,10');
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_SUBJECT, 'ایمیل یاد اور');
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_LINK, "<a href=\"https://hostiran.net/profile/panel/finance/invoice/:invoice_id\">شماره #:invoice_id</a><br>");
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_MESSAGE, ":client_name عزیز
<br>
<br>
این ایمیل به منزله اطلاعیه جهت پرداخت پیش فاکتور زیر ارسال شده است :
<br>
<br>
    ");
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_SMS, '5,15');
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_SMS_MESSAGE, "اطلاعیه" . "\n" . "مشترک گرامی هاست ایران; شما صورتحساب پرداخت نشده دارید" . "\n" ."\n");
        MainAppAPIService::storeConfig(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_SMS_LINK, "شماره صورتحساب: :invoice_id" . "\n" . "لینک پرداخت: https://hostiran.net/pay?id=:invoice_id" . "\n");
        MainAppAPIService::storeConfig(MainAppConfig::FINANCE_INVOICE_CREATE_MESSAGE, ":client_name عزیز
<br>
<br>
این اطلاعیه به منزله ایجاد پیش فاکتور  شماره :invoice_id در تاریخ :created_at است.
<br>
<br>
    ");
        MainAppAPIService::storeConfig(MainAppConfig::FINANCE_INVOICE_CREATE_SUBJECT, 'پیش فاکتور جدید');

    }
}
