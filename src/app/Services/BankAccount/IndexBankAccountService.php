<?php

namespace App\Services\BankAccount;

use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexBankAccountService
{
    public function __construct(private readonly BankAccountRepositoryInterface $bankAccountRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->bankAccountRepository->index($data);
    }
}
