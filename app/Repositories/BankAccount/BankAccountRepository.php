<?php

namespace App\Repositories\BankAccount;

use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class BankAccountRepository extends BaseRepository implements BankAccountRepositoryInterface
{
    public string $model = BankAccount::class;
}
