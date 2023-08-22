<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Integrations\MainApp\MainAppService;
use App\Models\BankAccount;
use App\Models\BankGateway;
use App\Models\InvoiceNumber;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // TODO seeder for Both types of InvoiceNumber
        BankAccount::create([
            'title' => 'بانک ملت',
            'status' => BankAccount::STATUS_ACTIVE,
            'display_order' => 1,
            'sheba_number' => '060560080981000866153001',
            'account_number' => '809-810-866153-1',
            'card_number' => '6219-8610-3582-1643',
            'rahkaran_id' => '213441412414414',
        ]);
        BankGateway::create([
            'name' => 'zibal',
            'name_fa' => 'زیبال',
            'status' => BankGateway::STATUS_ACTIVE,
            'config' => [
                'status' => 'active',
                'api_key' => 'ZMVaPzvUZj6Ux3MwT8z98YbSB5kZIzkV',
                'start_url' => 'https://gateway.zibal.ir/start/',
                'verify_url' => 'https://gateway.zibal.ir/v1/verify',
                'merchant_id' => 'zibal',
                'request_url' => 'https://gateway.zibal.ir/v1/request',
                'terminal_id' => '2408938',
            ],
        ]);
        InvoiceNumber::create([
            'invoice_number' => 1,
            'fiscal_year' => 1402,
            'status' => InvoiceNumber::STATUS_UNUSED,
            'type' => InvoiceNumber::TYPE_PAID,
        ]);
        InvoiceNumber::create([
            'invoice_number' => 1,
            'fiscal_year' => 1402,
            'status' => InvoiceNumber::STATUS_UNUSED,
            'type' => InvoiceNumber::TYPE_REFUND,
        ]);

        MainAppService::storeConfig('CRON_AUTO_INVOICE_CANCELLATION_DAYS', 1);
        MainAppService::storeConfig('CRON_AUTO_DOMAIN_INVOICE_CANCELLATION_DAYS', 1);
        MainAppService::storeConfig('CRON_FINANCE_INVOICE_REMINDER_DAYS_1', 1);
        MainAppService::storeConfig('CRON_FINANCE_INVOICE_REMINDER_DAYS_2', 1);
    }
}
