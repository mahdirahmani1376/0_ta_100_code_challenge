<?php

namespace App\Services\Admin\BankGateway;

use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexBankGatewayService
{
    private BankGatewayRepositoryInterface $bankGatewayRepository;

    public function __construct(BankGatewayRepositoryInterface $bankGatewayRepository)
    {
        $this->bankGatewayRepository = $bankGatewayRepository;
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->bankGatewayRepository->adminIndex($data);
    }
}
