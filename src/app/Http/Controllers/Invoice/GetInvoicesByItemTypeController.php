<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\GetInvoicesByItemTypeAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice\MinimalInvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GetInvoicesByItemTypeController extends Controller
{
    public function __construct(private readonly GetInvoicesByItemTypeAction $getInvoicesByItemTypeAction)
    {
        parent::__construct();
    }

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'invoiceable_ids'     => 'required|array',
            'invoiceable_ids.*'   => 'required|numeric',
            'invoiceable_types'   => 'required|array',
            'invoiceable_types.*' => 'required|string',
            'status'              => 'nullable|array',
            'status.*'            => ['required', 'string', Rule::in(Invoice::STATUSES)],
            'profile_id'          => 'nullable|numeric',
            'limit'               => 'nullable|numeric'
        ]);

        $invoices = ($this->getInvoicesByItemTypeAction)($data);

        return MinimalInvoiceResource::collection($invoices);
    }
}

