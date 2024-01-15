<?php

namespace Database\Seeders;

use App\Models\BankGateway;
use Illuminate\Database\Seeder;

class BankGatewaySeeder extends Seeder
{
    public function run(): void
    {
        // ----------- Zibal ----------------
//        BankGateway::create([
//            'name' => 'zibal',
//            'name_fa' => 'زیبال',
//            'status' => BankGateway::STATUS_ACTIVE,
//            'config' => [
//                'api_key' => 'ZMVaPzvUZj6Ux3MwT8z98YbSB5kZIzkV',
//                'start_url' => 'https://gateway.zibal.ir/start/',
//                'verify_url' => 'https://gateway.zibal.ir/v1/verify',
//                'merchant_id' => 'zibal',
//                'request_url' => 'https://gateway.zibal.ir/v1/request',
//                'terminal_id' => '2408938',
//            ],
//        ]);

        // ----------- OmidPay ----------------
        if (BankGateway::where('name', 'omidPay')->doesntExist()) {
            BankGateway::create([
                'name' => 'omidPay',
                'name_fa' => 'امید پی',
                'status' => BankGateway::STATUS_ACTIVE,
                'config' => [
                    'request_url' => 'https://ref.sayancard.ir/ref-payment/RestServices/mts/generateTokenWithNoSign/',
                    'start_url' => 'https://say.shaparak.ir/_ipgw_/MainTemplate/payment/',
                    'verify_url' => 'https://ref.sayancard.ir/ref-payment/RestServices/mts/verifyMerchantTrans/',
                    'merchant_id' => '411350718',
                    'terminal_id' => '41678655',
                    'password' => '342162',
                ],
            ]);
        }

        // ----------- BazaarPay ----------------
        if (BankGateway::where('name', 'bazaarPay')->doesntExist()) {
            BankGateway::create([
                'name' => 'bazaarPay',
                'name_fa' => 'بازار پی',
                'status' => BankGateway::STATUS_ACTIVE,
                'is_direct_payment_provider' => true,
                'config' => [
                    'direct_pay_url' => 'fill-here',
                    'trace_url' => 'fill-here',
                    'init_checkout_url' => 'fill-here',
                    'init_contract_url' => 'fill-here',
                    'authorization_token' => '123',
                ],
            ]);
        }
    }
}
