<?php

namespace App\Repositories\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceNumber;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\InvoiceNumberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceNumberRepository extends BaseRepository implements InvoiceNumberRepositoryInterface
{
    public string $model = InvoiceNumber::class;

    /**
     * @throws BindingResolutionException
     */
    public function index(array $data): LengthAwarePaginator
    {
        $query = self::newQuery();
        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where('invoice_id', 'LIKE', '%%' . $data['search'] . '%')
                    ->orWhere('fiscal_year', 'LIKE', '%%' . $data['search'] . '%')
                    ->orWhere('invoice_number', 'LIKE', '%%' . $data['search'] . '%');
            });
        }
        if (!empty($data['type'])) {
            $query->where('type', $data['type']);
        }
        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }
        if (!empty($data['invoice_id'])) {
            $query->where('invoice_id', $data['invoice_id']);
        }

        return self::paginate($query);
    }

    public function findByInvoice(Invoice $invoice): ?InvoiceNumber
    {
        return self::newQuery()
            ->where('invoice_id', $invoice->getKey())
            ->first();
    }

    public function use(Invoice $invoice, string $type, string $fiscalYear, $invoiceNumber = null): int
    {
        return self::newQuery()
            ->whereNull('invoice_id')
            ->where('type', $type)
            ->where('fiscal_year', $fiscalYear)
            ->where('status', InvoiceNumber::STATUS_UNUSED)
            ->when($invoiceNumber, function (Builder $query) use ($invoiceNumber) {
                $query->where('invoice_number', $invoiceNumber);
            })
            ->limit(1)
            ->update([
                'invoice_id' => $invoice->id,
                'status' => InvoiceNumber::STATUS_USED,
                'updated_at' => now(),
            ]);
    }

    public function getLatestInvoiceNumber(string $type, string $fiscalYear): ?int
    {
        return self::newQuery()
            ->where('type', $type)
            ->where('fiscal_year', $fiscalYear)
            ->max('invoice_number');
    }

    public function countUnused(string $type, string $fiscalYear): int
    {
        return self::newQuery()
            ->where('type', $type)
            ->where('fiscal_year', $fiscalYear)
            ->whereNull('invoice_id')
            ->count();
    }
}
