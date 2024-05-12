<?php

namespace App\Services\ClientCashout;

use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientCashoutService
{
    public function __construct(private readonly ClientCashoutRepositoryInterface $clientCashoutRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->clientCashoutRepository->index($data);
    }
}
