<?php

namespace App\Services\Admin\BankGateway;

use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexBankGatewayService
{
    public function __construct(private readonly BankGatewayRepositoryInterface $bankGatewayRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->bankGatewayRepository->adminIndex($data);
    }
}
