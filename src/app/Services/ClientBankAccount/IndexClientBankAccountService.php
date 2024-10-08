<?php

namespace App\Services\ClientBankAccount;

use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientBankAccountService
{
    public function __construct(private readonly ClientBankAccountRepositoryInterface $clientBankAccountRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->clientBankAccountRepository->index($data);
    }
}
