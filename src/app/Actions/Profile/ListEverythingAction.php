<?php

namespace App\Actions\Profile;

use App\Services\Profile\ListEverythingService;

class ListEverythingAction
{
    public function __construct(private readonly ListEverythingService $listEverythingService)
    {
    }

    public function __invoke(int $clientId, int $offset = null)
    {
        return ($this->listEverythingService)($clientId, $offset);
    }
}
