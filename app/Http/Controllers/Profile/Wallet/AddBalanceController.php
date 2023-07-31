<?php

namespace App\Http\Controllers\Profile\Wallet;

use App\Actions\Profile\Wallet\AddBalanceAction;
use App\Http\Requests\Profile\Wallet\AddBalanceRequest;
use App\Http\Resources\Profile\Invoice\InvoiceResource;

class AddBalanceController
{
    private AddBalanceAction $addBalanceAction;

    public function __construct(AddBalanceAction $addBalanceAction)
    {
        $this->addBalanceAction = $addBalanceAction;
    }

    /**
     * @param AddBalanceRequest $request
     * @return InvoiceResource
     */
    public function __invoke(AddBalanceRequest $request)
    {
        $invoice = ($this->addBalanceAction)($request->validated());

        return InvoiceResource::make($invoice);
    }
}
