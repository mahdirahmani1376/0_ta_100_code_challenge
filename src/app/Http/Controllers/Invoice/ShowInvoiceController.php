<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\ShowInvoiceByCriteriaAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice\ShowInvoiceResource;
use Illuminate\Http\Request;

class ShowInvoiceController extends Controller
{
    public function __construct(
        private readonly ShowInvoiceByCriteriaAction $showInvoiceByCriteriaAction
    )
    {
        parent::__construct();
    }

    /**
     * @param Request $request
     * @param $invoice_id
     * @return ShowInvoiceResource
     */
    public function __invoke(Request $request, $invoice_id)
    {
        $criteria = [
            'id'=> $invoice_id,
        ];

        if ($profile_id = $request->profile_id) {
            $criteria['profile_id'] = $profile_id;
        }

        $invoice = ($this->showInvoiceByCriteriaAction)($criteria);

        return ShowInvoiceResource::make($invoice);
    }
}
