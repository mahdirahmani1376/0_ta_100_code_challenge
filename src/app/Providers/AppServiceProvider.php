<?php

namespace App\Providers;

use App\Repositories\BankAccount\BankAccountRepository;
use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;
use App\Repositories\BankGateway\BankGatewayRepository;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use App\Repositories\ClientBankAccount\ClientBankAccountRepository;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;
use App\Repositories\ClientCashout\ClientCashoutRepository;
use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Invoice\Interface\InvoiceNumberRepositoryInterface;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use App\Repositories\Invoice\InvoiceNumberRepository;
use App\Repositories\Invoice\InvoiceRepository;
use App\Repositories\Invoice\ItemRepository;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;
use App\Repositories\OfflineTransaction\OfflineTransactionRepository;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\Wallet\CreditTransactionRepository;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use App\Repositories\Wallet\Interface\WalletRepositoryInterface;
use App\Repositories\Wallet\WalletRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(BankAccountRepositoryInterface::class, BankAccountRepository::class);
        $this->app->bind(BankGatewayRepositoryInterface::class, BankGatewayRepository::class);
        $this->app->bind(ClientBankAccountRepositoryInterface::class, ClientBankAccountRepository::class);
        $this->app->bind(ClientCashoutRepositoryInterface::class, ClientCashoutRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(ItemRepositoryInterface::class, ItemRepository::class);
        $this->app->bind(InvoiceNumberRepositoryInterface::class, InvoiceNumberRepository::class);
        $this->app->bind(OfflineTransactionRepositoryInterface::class, OfflineTransactionRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(CreditTransactionRepositoryInterface::class, CreditTransactionRepository::class);
    }

    public function boot(): void
    {
    }
}
