<?php

namespace App\Services\BankGateway;

use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use Illuminate\Support\Collection;

class IndexBankGatewayService
{
    public function __construct(private readonly BankGatewayRepositoryInterface $bankGatewayRepository)
    {
    }

    public function __invoke(bool $isAdmin = false): Collection
    {
        return $this->bankGatewayRepository->all($isAdmin);
    }
}
