<?php

namespace App\Services\BankGateway;

use App\Integrations\BankGateway\Interface\BankGatewayInterface;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use Illuminate\Support\Str;

class MakeBankGatewayProviderByNameService
{
    public function __construct(private readonly BankGatewayRepositoryInterface $bankGatewayRepository)
    {
    }

    public function __invoke(string $name): BankGatewayInterface
    {
        $bankGatewayModel = $this->bankGatewayRepository->findByName($name);

        $provider = "App\\Integrations\\BankGateway\\" . Str::ucfirst($name);

        return $provider::make($bankGatewayModel);
    }
}
