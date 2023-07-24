<?php

namespace App\Services\Admin\ClientCashout;

use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientCashoutService
{
    private ClientCashoutRepositoryInterface $clientCashoutRepository;

    public function __construct(ClientCashoutRepositoryInterface $clientCashoutRepository)
    {
        $this->clientCashoutRepository = $clientCashoutRepository;
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->clientCashoutRepository->adminIndex($data);
    }
}
