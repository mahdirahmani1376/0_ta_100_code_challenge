<?php

namespace App\Http\Controllers\BankGateway;

use App\Actions\BankGateway\DeleteBankGatewayAction;
use App\Models\BankGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeleteBankGatewayController
{
    public function __construct(private readonly DeleteBankGatewayAction $deleteBankGatewayAction)
    {
    }

    /**
     * @param BankGateway $bankGateway
     * @return JsonResponse
     */
    public function __invoke(BankGateway $bankGateway)
    {
        ($this->deleteBankGatewayAction)($bankGateway);

        return response()->json([], Response::HTTP_ACCEPTED);
    }
}
