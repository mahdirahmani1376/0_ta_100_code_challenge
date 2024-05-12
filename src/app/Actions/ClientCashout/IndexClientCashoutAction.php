<?php

namespace App\Actions\ClientCashout;

use App\Services\ClientCashout\IndexClientCashoutService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientCashoutAction
{
    public function __construct(private readonly IndexClientCashoutService $indexClientCashoutService)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return ($this->indexClientCashoutService)($data);
    }
}
