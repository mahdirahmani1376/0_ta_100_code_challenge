<?php

namespace App\Services\Profile\ClientCashout;

use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientCashoutService
{
    private ClientCashoutRepositoryInterface $clientCashoutRepository;

    public function __construct(ClientCashoutRepositoryInterface $clientCashoutRepository)
    {
        $this->clientCashoutRepository = $clientCashoutRepository;
    }

    public function __invoke(int $clientId, array $data): LengthAwarePaginator
    {
        return $this->clientCashoutRepository->profileIndex($clientId, $data);
    }
}
