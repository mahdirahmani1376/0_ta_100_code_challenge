<?php

namespace App\Repositories\Invoice\Interface;

use App\Repositories\Base\Interface\EloquentRepositoryInterface;

interface InvoiceRepositoryInterface extends EloquentRepositoryInterface
{
    public function adminIndex(array $data, array $paginationParam);
}
