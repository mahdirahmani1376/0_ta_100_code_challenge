<?php

namespace App\Http\Controllers\Admin\BankGateway;

use App\Actions\Admin\BankGateway\DeleteBankGatewayAction;
use App\Models\BankGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeleteBankGatewayController
{
    private DeleteBankGatewayAction $deleteBankGatewayAction;

    public function __construct(DeleteBankGatewayAction $deleteBankGatewayAction)
    {
        $this->deleteBankGatewayAction = $deleteBankGatewayAction;
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
