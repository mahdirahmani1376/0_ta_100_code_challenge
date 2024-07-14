<?php

namespace App\Services\Profile;

use App\Models\Profile;
use App\Repositories\Profile\Interface\ProfileRepositoryInterface;

class FindProfileByIdService
{
    public function __construct(private readonly ProfileRepositoryInterface $profileRepository)
    {
    }

    public function __invoke(int $profileId): Profile
    {
        return $this->profileRepository->find($profileId);
    }
}
