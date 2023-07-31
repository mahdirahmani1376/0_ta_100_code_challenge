<?php

namespace App\Services\Profile\ClientBankAccount;

use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexClientBankAccountService
{
    private ClientBankAccountRepositoryInterface $clientBankAccountRepository;

    public function __construct(ClientBankAccountRepositoryInterface $clientBankAccountRepository)
    {
        $this->clientBankAccountRepository = $clientBankAccountRepository;
    }

    public function __invoke(int $clientId, array $data): LengthAwarePaginator
    {
        return $this->clientBankAccountRepository->profileIndex($clientId, $data);
    }
}
