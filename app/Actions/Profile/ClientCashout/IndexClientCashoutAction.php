<?php

namespace App\Actions\Profile\ClientCashout;

use App\Services\Profile\ClientCashout\IndexClientCashoutService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientCashoutAction
{
    private IndexClientCashoutService $indexClientCashoutService;

    public function __construct(IndexClientCashoutService $indexClientCashoutService)
    {
        $this->indexClientCashoutService = $indexClientCashoutService;
    }

    public function __invoke(int $clientId, array $data): LengthAwarePaginator
    {
        return ($this->indexClientCashoutService)($clientId, $data);
    }
}
