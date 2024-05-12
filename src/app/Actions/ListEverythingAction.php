<?php

namespace App\Actions;

use App\Services\Profile\ListEverythingService;

class ListEverythingAction
{
    public function __construct(private readonly ListEverythingService $listEverythingService)
    {
    }

    public function __invoke(int $profileId, int $offset = null, $perPage = 100)
    {
        return ($this->listEverythingService)($profileId, $offset, $perPage);
    }
}
