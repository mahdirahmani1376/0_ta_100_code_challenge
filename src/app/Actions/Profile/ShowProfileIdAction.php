<?php

namespace App\Actions\Profile;

use App\Models\Profile;
use App\Services\Profile\FindOrCreateProfileService;

class ShowProfileIdAction
{
    public function __construct(private readonly FindOrCreateProfileService $createProfileService)
    {
    }

    public function __invoke(int $profileId): Profile
    {
        return ($this->createProfileService)($profileId);
    }
}
