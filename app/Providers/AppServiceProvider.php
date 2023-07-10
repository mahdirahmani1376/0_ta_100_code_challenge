<?php

namespace App\Providers;

use App\Repositories\BankAccount\BankAccountRepository;
use App\Repositories\BankAccount\Interface\BankAccountInterface;
use App\Repositories\BankGateway\BankGatewayRepository;
use App\Repositories\BankGateway\Interface\BankGatewayInterface;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use App\Repositories\ClientBankAccount\ClientBankAccountRepository;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountInterface;
use App\Repositories\ClientCashout\ClientCashoutRepository;
use App\Repositories\ClientCashout\Interface\ClientCashoutInterface;
use App\Repositories\Invoice\Interface\InvoiceInterface;
use App\Repositories\Invoice\Interface\InvoiceNumberInterface;
use App\Repositories\Invoice\Interface\ItemInterface;
use App\Repositories\Invoice\InvoiceNumberRepository;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Invoice\ItemRepository;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionInterface;
use App\Repositories\OfflineTransaction\OfflineTransactionRepository;
use App\Repositories\Transaction\Interface\TransactionInterface;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\Wallet\CreditTransactionRepository;
use App\Repositories\Wallet\Interface\CreditTransactionInterface;
use App\Repositories\Wallet\Interface\WalletInterface;
use App\Repositories\Wallet\WalletRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(BankAccountInterface::class, BankAccountRepository::class);
        $this->app->bind(BankGatewayInterface::class, BankGatewayRepository::class);
        $this->app->bind(ClientBankAccountInterface::class, ClientBankAccountRepository::class);
        $this->app->bind(ClientCashoutInterface::class, ClientCashoutRepository::class);
        $this->app->bind(InvoiceInterface::class, InvoiceRepository::class);
        $this->app->bind(ItemInterface::class, ItemRepository::class);
        $this->app->bind(InvoiceNumberInterface::class, InvoiceNumberRepository::class);
        $this->app->bind(OfflineTransactionInterface::class, OfflineTransactionRepository::class);
        $this->app->bind(TransactionInterface::class, TransactionRepository::class);
        $this->app->bind(WalletInterface::class, WalletRepository::class);
        $this->app->bind(CreditTransactionInterface::class, CreditTransactionRepository::class);
    }

    public function boot(): void
    {
    }
}
