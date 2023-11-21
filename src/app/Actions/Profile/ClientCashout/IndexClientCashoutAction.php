<?php

namespace App\Actions\Profile\ClientCashout;

use App\Services\Profile\ClientCashout\IndexClientCashoutService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientCashoutAction
{
    public function __construct(private readonly IndexClientCashoutService $indexClientCashoutService)
    {
    }

    public function __invoke(int $profileId, array $data): LengthAwarePaginator
    {
        return ($this->indexClientCashoutService)($profileId, $data);
    }
}
