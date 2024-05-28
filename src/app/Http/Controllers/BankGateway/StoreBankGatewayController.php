<?php

namespace App\Http\Controllers\BankGateway;

use App\Actions\BankGateway\StoreBankGatewayAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankGateway\StoreBankGatewayRequest;
use App\Http\Resources\BankGateway\BankGatewayResource;

class StoreBankGatewayController extends Controller
{
    public function __construct(private readonly StoreBankGatewayAction $storeBankGatewayAction)
    {
        parent::__construct();
    }

    /**
     * @param StoreBankGatewayRequest $request
     * @return BankGatewayResource
     */
    public function __invoke(StoreBankGatewayRequest $request)
    {
        $bankGateway = ($this->storeBankGatewayAction)($request->validated());

        return BankGatewayResource::make($bankGateway);
    }
}
