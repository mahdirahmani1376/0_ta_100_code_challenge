<?php

namespace App\Services\Profile;

use App\Models\Profile;
use App\Repositories\Profile\Interface\ProfileRepositoryInterface;

class FindOrCreateProfileService
{
    public function __construct(private readonly ProfileRepositoryInterface $profileRepository)
    {
    }

    public function __invoke(int $clientId): Profile
    {
        return $this->profileRepository->findOrCreate($clientId);
    }
}
