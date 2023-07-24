<?php

namespace App\Actions\Admin\ClientCashout;

use App\Services\Admin\ClientCashout\IndexClientCashoutService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientCashoutAction
{
    private IndexClientCashoutService $indexClientCashoutService;

    public function __construct(IndexClientCashoutService $indexClientCashoutService)
    {
        $this->indexClientCashoutService = $indexClientCashoutService;
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return ($this->indexClientCashoutService)($data);
    }
}
