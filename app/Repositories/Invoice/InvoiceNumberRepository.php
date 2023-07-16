<?php

namespace App\Repositories\Invoice;

use App\Models\InvoiceNumber;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\InvoiceNumberRepositoryInterface;

class InvoiceNumberRepository extends BaseRepository implements InvoiceNumberRepositoryInterface
{
    public string $model = InvoiceNumber::class;
}
