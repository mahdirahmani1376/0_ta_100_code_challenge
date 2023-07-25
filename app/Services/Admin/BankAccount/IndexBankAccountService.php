<?php

namespace App\Services\Admin\BankAccount;

use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexBankAccountService
{
    private BankAccountRepositoryInterface $bankAccountRepository;

    public function __construct(BankAccountRepositoryInterface $bankAccountRepository)
    {
        $this->bankAccountRepository = $bankAccountRepository;
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->bankAccountRepository->adminIndex($data);
    }
}
