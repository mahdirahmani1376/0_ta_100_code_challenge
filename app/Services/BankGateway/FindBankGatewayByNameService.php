<?php

namespace App\Services\BankGateway;

use App\Integrations\BankGateway\Interface\BankGatewayInterface;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use Illuminate\Support\Str;

class FindBankGatewayByNameService
{
    public function __construct(private readonly BankGatewayRepositoryInterface $bankGatewayRepository)
    {
    }

    public function __invoke(string $name): BankGatewayInterface
    {
        $bankGatewayModel = $this->bankGatewayRepository->findByName($name);

        try {
            $provider = "App\\Integrations\\BankGateway\\" . Str::ucfirst($name);

            return $provider::make($bankGatewayModel);
        } catch (\Exception $e) {
            // TODO make proper exception
            info($e->getTrace());
            info($e->getMessage());
        }
    }
}
