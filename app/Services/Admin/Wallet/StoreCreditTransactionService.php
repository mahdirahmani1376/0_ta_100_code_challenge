<?php

namespace App\Services\Admin\Wallet;

use App\Models\Wallet;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;

class StoreCreditTransactionService
{
    private CreditTransactionRepositoryInterface $creditTransactionRepository;

    public function __construct(CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
        $this->creditTransactionRepository = $creditTransactionRepository;
    }

    public function __invoke(Wallet $wallet, array $data)
    {
        $data['client_id'] = $wallet->client_id;
        $data['wallet_id'] = $wallet->getKey();
        $data['created_at'] = $data['date'] ?? now();
        $data['admin_id'] = request('admin_id');

        return $this->creditTransactionRepository->create($data, [
            'client_id',
            'wallet_id',
            'created_at',
            'admin_id',
            'amount',
            'description',
        ]);
    }
}
