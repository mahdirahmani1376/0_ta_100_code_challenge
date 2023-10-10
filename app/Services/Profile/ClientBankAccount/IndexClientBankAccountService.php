<?php

namespace App\Services\Profile\ClientBankAccount;

use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientBankAccountService
{
    public function __construct(private readonly ClientBankAccountRepositoryInterface $clientBankAccountRepository)
    {
    }

    public function __invoke(int $clientId, array $data): LengthAwarePaginator
    {
        return $this->clientBankAccountRepository->profileIndex($clientId, $data);
    }
}
