<?php

namespace App\Actions\Invoice\MoadianLog;

use App\Services\Invoice\MoadianLog\IndexMoadianLogService;

class IndexMoadianLogAction
{
    public function __construct(private readonly IndexMoadianLogService $indexMoadianLogService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->indexMoadianLogService)($data);
    }
}