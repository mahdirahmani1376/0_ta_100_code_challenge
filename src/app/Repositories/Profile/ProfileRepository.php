<?php

namespace App\Repositories\Profile;

use App\Models\Profile;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Profile\Interface\ProfileRepositoryInterface;

class ProfileRepository extends BaseRepository implements ProfileRepositoryInterface
{
    public string $model = Profile::class;

    public function findOrCreate(int $clientId): Profile
    {
        return self::newQuery()->firstOrCreate(['client_id' => $clientId,]);
    }
}
