<?php

namespace App\Actions\Profile;

use App\Services\Profile\ListEverythingService;

class ListEverythingAction
{
    private ListEverythingService $listEverythingService;

    public function __construct(ListEverythingService $listEverythingService)
    {
        $this->listEverythingService = $listEverythingService;
    }

    public function __invoke(int $clientId, int $offset = null)
    {
        return ($this->listEverythingService)($clientId, $offset);
    }
}
