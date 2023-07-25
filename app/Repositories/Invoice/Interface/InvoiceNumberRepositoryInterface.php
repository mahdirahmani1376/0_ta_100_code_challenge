<?php

namespace App\Repositories\Invoice\Interface;

use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface InvoiceNumberRepositoryInterface extends EloquentRepositoryInterface
{
    public function adminIndex(array $data): LengthAwarePaginator;
}
