<?php

namespace App\Actions\Profile;

use App\Services\Profile\ListEverythingService;

class ListEverythingAction
{
    public function __construct(private readonly ListEverythingService $listEverythingService)
    {
    }

    public function __invoke(int $clientId, int $offset = null, $perPage = 100)
    {
        return ($this->listEverythingService)($clientId, $offset, $perPage);
    }
}
