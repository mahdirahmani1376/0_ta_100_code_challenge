<?php

namespace App\Services\Admin\BankGateway;

use App\Models\BankGateway;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;

class UpdateBankGatewayService
{
    public function __construct(private readonly BankGatewayRepositoryInterface $bankGatewayRepository)
    {
    }

    public function __invoke(BankGateway $bankGateway, array $data): BankGateway
    {
        // Copy every config field e.g. merchant_id, api_key, ... into 'config' and unset 'name' and 'name_fa'
        // as they have their own dedicated fields in the table , look at the example below
        // $data = ['name'=>'Zarinpal', 'merchant_id' => 1, 'api_key' => 2]
        // final result =>
        // $data = ['name'=>'Zarinpal','config'=> ['merchant_id=>1, 'api_key'=>2] ]

        $data['config'] = $data;
        unset($data['config']['name']);
        unset($data['config']['name_fa']);

        return $this->bankGatewayRepository->update($bankGateway, $data, [
            'name', 'name_fa', 'config',
        ]);
    }
}
