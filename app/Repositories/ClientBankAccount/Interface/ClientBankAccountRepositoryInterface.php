<?php

namespace App\Repositories\ClientBankAccount\Interface;

use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClientBankAccountRepositoryInterface extends EloquentRepositoryInterface
{
    public function adminIndex(array $data): LengthAwarePaginator;
}
