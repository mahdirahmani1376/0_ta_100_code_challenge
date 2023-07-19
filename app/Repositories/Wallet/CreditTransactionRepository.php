<?php

namespace App\Repositories\Wallet;

use App\Models\CreditTransaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CreditTransactionRepository extends BaseRepository implements CreditTransactionRepositoryInterface
{
    public string $model = CreditTransaction::class;

    public function indexByClientId(int $clientId): LengthAwarePaginator
    {
        return $this->paginate(
            $this->newQuery()
                ->where('client_id', $clientId)
        );
    }

    public function sum(int $clientId): int
    {
        return $this->newQuery()
            ->where('client_id', $clientId)
            ->sum('amount');
    }
}
