<?php

namespace App\Services\Invoice\MoadianLog;

use App\Repositories\Invoice\Interface\MoadianLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class IndexMoadianLogService
{
    public function __construct(private readonly MoadianLogRepositoryInterface $moadianLogRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator|Collection
    {
        return $this->moadianLogRepository->index($data);
    }
}