<?php

namespace App\Services\BankGateway;

use App\Repositories\BankGateway\BankGatewayRepository;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

class IndexBankGatewayService
{
    /** @var BankGatewayRepository $bankGatewayRepository */
    private BankGatewayRepositoryInterface $bankGatewayRepository;

    public function __construct(BankGatewayRepositoryInterface $bankGatewayRepository)
    {
        $this->bankGatewayRepository = $bankGatewayRepository;
    }

    /**
     * @throws BindingResolutionException
     */
    public function __invoke(): Collection
    {
        return $this->bankGatewayRepository->all();
    }
}
