<?php

namespace App\Services\BankGateway;

use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class IndexBankGatewayService
{
    public function __construct(private readonly BankGatewayRepositoryInterface $bankGatewayRepository)
    {
    }

    public function __invoke(array $data): Collection|LengthAwarePaginator
    {
        return $this->bankGatewayRepository->index($data);
    }
}
