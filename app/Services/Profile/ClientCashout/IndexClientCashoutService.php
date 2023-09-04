<?php

namespace App\Services\Profile\ClientCashout;

use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientCashoutService
{
    public function __construct(private readonly ClientCashoutRepositoryInterface $clientCashoutRepository)
    {
    }

    public function __invoke(int $clientId, array $data): LengthAwarePaginator
    {
        return $this->clientCashoutRepository->profileIndex($clientId, $data);
    }
}
