<?php

namespace App\Repositories\Profile\Interface;

use App\Models\Profile;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;

interface ProfileRepositoryInterface extends EloquentRepositoryInterface
{
    public function findOrCreate(int $clientId): Profile;
}
