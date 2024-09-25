<?php

namespace App\Repositories\BankGateway;

use App\Models\DirectPayment;
use App\Repositories\BankGateway\Interface\DirectPaymentRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class DirectPaymentRepository extends BaseRepository implements DirectPaymentRepositoryInterface
{
    public string $model = DirectPayment::class;

    public function findByProfileId(int $profileId): ?DirectPayment
    {
        return self::newQuery()
            ->where('profile_id', $profileId)
            ->whereIn('status', [DirectPayment::STATUS_INIT, DirectPayment::STATUS_ACTIVE,])
            ->first();
    }

    public function listProvidersByProfileId(int $profileId): array
    {
        return self::newQuery()
            ->where('profile_id', $profileId)
            ->whereIn('status', [DirectPayment::STATUS_ACTIVE,])
            ->select('provider')
            ->distinct()
            ->get()
            ->pluck('provider')
            ->toArray();
    }
}
