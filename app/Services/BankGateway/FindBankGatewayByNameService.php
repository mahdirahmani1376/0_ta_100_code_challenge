<?php

namespace App\Services\BankGateway;

use App\Integrations\BankGateway\Interface\BankGatewayInterface;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;

class FindBankGatewayByNameService
{
    private BankGatewayRepositoryInterface $bankGatewayRepository;

    public function __construct(BankGatewayRepositoryInterface $bankGatewayRepository)
    {
        $this->bankGatewayRepository = $bankGatewayRepository;
    }

    public function __invoke(string $name): BankGatewayInterface
    {
        $bankGatewayModel = $this->bankGatewayRepository->findByName($name);

        try {
            $provider = "App\\Integrations\\BankGateway\\" . $name;

            return $provider::make($bankGatewayModel);
        } catch (\Exception $e) {
            // TODO make proper exception
            info($e->getTrace());
            info($e->getMessage());
        }
    }
}
