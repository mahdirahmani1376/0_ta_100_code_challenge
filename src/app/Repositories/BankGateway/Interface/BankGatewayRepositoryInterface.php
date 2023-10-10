<?php

namespace App\Repositories\BankGateway\Interface;

use App\Models\BankGateway;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BankGatewayRepositoryInterface extends EloquentRepositoryInterface
{
    public function adminIndex(array $data): LengthAwarePaginator;

    public function findByName(string $name): ?BankGateway;
}
