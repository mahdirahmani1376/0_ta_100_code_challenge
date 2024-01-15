<?php

namespace App\Repositories\BankGateway\Interface;

use App\Models\BankGateway;
use App\Models\DirectPayment;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;

interface DirectPaymentRepositoryInterface extends EloquentRepositoryInterface
{
    public function findByProfileId(int $profileId): ?DirectPayment;

    public function listProvidersByProfileId(int $profileId): array;
}
