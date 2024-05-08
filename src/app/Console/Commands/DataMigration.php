<?php

namespace App\Console\Commands;

use App\Models\BankAccount;
use App\Models\BankGateway;
use App\Models\ClientBankAccount;
use App\Models\ClientCashout;
use App\Models\CreditTransaction;
use App\Models\Invoice;
use App\Models\InvoiceNumber;
use App\Models\Item;
use App\Models\OfflineTransaction;
use App\Models\Profile;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DataMigration extends Command
{
    protected $signature = 'app:data-migration';

    protected $description = 'Command description';

    protected int $chunkSize = 2000;

    public function handle()
    {
        ini_set('memory_limit', '4096M');
        $this->info("#### START DATA MIGRATION ####");
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $start_time = Carbon::now();
        self::migrateProfiles();
        self::updateMainAppClients();
        self::migrateBankAccount();
        self::migrateBankGateway();
        self::migrateWallet();
        self::migrateInvoice();
        self::migrateClientBankAccount();
        self::migrateClientCashout();
        self::migrateCreditTransaction();
        self::migrateItem();
        self::migrateTransaction();
        self::migrateOfflineTransaction();
        self::migrateInvoiceNumber();
        self::syncWallets();
        $process_time = Carbon::now()->diffInSeconds($start_time);
        $this->info("#### END DATA MIGRATION in {$process_time} seconds");
    }

    private function migrateProfiles()
    {
        $profileTableName = (new Profile())->getTable();
        $this->alert("Beginning to migrate $profileTableName");
        try {
            $count = DB::connection('mainapp')->select('SELECT count(*) as count FROM `clients`')[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')->select("SELECT * FROM `clients` LIMIT $this->chunkSize OFFSET {$i}");
                $this->info('Fetched data');
                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    $newRow = [];
                    $newRow['rahkaran_id'] = $row['rahkaran_id'];
                    $newRow['client_id'] = $row['id'];
                    $newRow['id'] = $row['id'];
                    $newRow['created_at'] = $newRow['updated_at'] = now();
                    return $newRow;
                });
                $this->info('Mapping done');
                DB::table($profileTableName)->insert($mappedData);
                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $profileTableName");

            $this->compareCounts(
                'clients',
                DB::connection('mainapp')->table('clients')->count(),
                $profileTableName,
                Profile::count()
            );
        } catch (\Throwable $e) {
            $this->error("Something went wrong when migrating $profileTableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function updateMainAppClients()
    {
        try {
            $this->alert('Set client.id to finance profile id');
            DB::connection('mainapp')->select('
                UPDATE clients AS cl 
                SET cl.finance_profile_id = cl.id
                WHERE cl.finance_profile_id IS NULL
            ');
            $this->info('End Set client.id to finance profile id');
        } catch (\Throwable $exception) {
            dump([
                'error'  => substr($exception->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
            $this->error('Something went wrong when Set client.id to finance profile id');
        }
    }

    private function migrateBankAccount(): void
    {
        $bankAccountTableName = (new BankAccount)->getTable();
        $this->alert("Beginning to migrate $bankAccountTableName");
        try {
            $oldBankAccounts = DB::connection('mainapp')->select('SELECT * FROM `bank_accounts`');
            $this->info('Fetched data');
            $newBankAccounts = Arr::map($oldBankAccounts, function ($row) {
                $row = (array)$row;
                $newRow = [];

                $newRow['id'] = $row['id'];
                $newRow['created_at'] = $row['created_at'];
                $newRow['updated_at'] = $row['updated_at'];
                $newRow['deleted_at'] = $row['deleted_at'];
                $newRow['title'] = $row['title'];
                $newRow['order'] = $row['order'];
                if ($row['active'] == 0) {
                    $newRow['deleted_at'] = Carbon::now();
                    $newRow['status'] = BankAccount::STATUS_INACTIVE;
                } else {
                    $newRow['status'] = BankAccount::STATUS_ACTIVE;
                }

                $newRow['sheba_number'] = $row['sheba_number'];
                $newRow['account_number'] = $row['account_number'];
                $newRow['card_number'] = $row['card_number'];
                $newRow['rahkaran_id'] = $row['rahkaran_id'];

                return $newRow;
            });
            $this->info('Mapping done');
            DB::table($bankAccountTableName)->insert($newBankAccounts);
            $this->info("End of data migrate for $bankAccountTableName");

            $this->compareCounts(
                'bank_accounts',
                DB::connection('mainapp')->table('bank_accounts')->count(),
                $bankAccountTableName,
                BankAccount::withTrashed()->count()
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $bankAccountTableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function migrateBankGateway(): void
    {
        $tableName = (new BankGateway())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $oldData = DB::connection('mainapp')->select('SELECT * FROM `payment_gateways`');
            $this->info('Fetched data');
            $mappedData = Arr::map($oldData, function ($row) {
                $row = (array)$row;
                $newRow = [];

                $newRow['id'] = $row['id'];
                $newRow['created_at'] = $row['created_at'];
                $newRow['updated_at'] = $row['updated_at'];
                $newRow['deleted_at'] = $row['deleted_at'];
                $newRow['name'] = $row['name'];
                $newRow['name_fa'] = $row['label'];
                if ($row['status'] == 'active') {
                    $newRow['status'] = BankGateway::STATUS_ACTIVE;
                } else {
                    $newRow['deleted_at'] = Carbon::now();
                    $newRow['status'] = BankGateway::STATUS_INACTIVE;
                }

                $config = [];
                if (!empty($row['merchant_id'])) {
                    $config['merchant_id'] = $row['merchant_id'];
                }
                if (!empty($row['request_url'])) {
                    $config['request_url'] = $row['request_url'];
                }
                if (!empty($row['verify_url'])) {
                    $config['verify_url'] = $row['verify_url'];
                }
                if (!empty($row['start_url'])) {
                    $config['start_url'] = $row['start_url'];
                }
                if (!empty($row['username'])) {
                    $config['username'] = $row['username'];
                }
                if (!empty($row['password'])) {
                    $config['password'] = $row['password'];
                }
                if (!empty($row['terminal_id'])) {
                    $config['terminal_id'] = $row['terminal_id'];
                }
                if (!empty($row['api_key'])) {
                    $config['api_key'] = $row['api_key'];
                }
                $newRow['config'] = json_encode($config);

                return $newRow;
            });
            $this->info('Mapping done');
            DB::table($tableName)->insert($mappedData);
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'payment_gateways',
                DB::connection('mainapp')->table('payment_gateways')->count(),
                $tableName,
                BankGateway::withTrashed()->count()
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function migrateClientBankAccount(): void
    {
        $tableName = (new ClientBankAccount())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $oldData = DB::connection('mainapp')->select('SELECT * FROM `client_bank_accounts`');
            $this->info('Fetched data');
            $mappedData = Arr::map($oldData, function ($row) {
                $row = (array)$row;
                $newRow = [];

                $newRow['id'] = $row['id'];
                $newRow['created_at'] = $row['created_at'];
                $newRow['updated_at'] = $row['updated_at'];
                $newRow['deleted_at'] = $row['deleted_at'];
                $newRow['profile_id'] = $row['client_id'];
                $newRow['zarinpal_bank_account_id'] = $row['zarinpal_bank_account_id'];
                $newRow['bank_name'] = $row['bank_name'];
                $newRow['owner_name'] = $row['deposit_owner'];
                $newRow['sheba_number'] = $row['sheba_number'];
                $newRow['account_number'] = null;
                $newRow['card_number'] = $row['card_number'];
                $newRow['status'] = $row['status'];
                if ($newRow['status'] == 'reject') $newRow['status'] = ClientBankAccount::STATUS_REJECTED;

                return $newRow;
            });
            $this->info('Mapping done');
            DB::table($tableName)->insert($mappedData);
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'client_bank_accounts',
                DB::connection('mainapp')->table('client_bank_accounts')->count(),
                $tableName,
                ClientBankAccount::withTrashed()->count()
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function migrateClientCashout(): void
    {
        $tableName = (new ClientCashout())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $oldData = DB::connection('mainapp')->select('SELECT * FROM `client_cashouts`');
            $this->info('Fetched data');
            $mappedData = Arr::map($oldData, function ($row) {
                $row = (array)$row;
                $newRow = [];
                $newRow['id'] = $row['id'];
                $newRow['created_at'] = $row['created_at'];
                $newRow['updated_at'] = $row['updated_at'];
                $newRow['deleted_at'] = $row['deleted_at'];
                $newRow['profile_id'] = $row['client_id'];
                $newRow['client_bank_account_id'] = $row['bank_account_id'];
                $newRow['zarinpal_payout_id'] = $row['payout_id'];
                $newRow['admin_id'] = $row['admin_id'];
                $newRow['amount'] = $row['amount'];
                $newRow['admin_note'] = $row['admin_note'];
                $newRow['status'] = $row['status'];
                if ($newRow['status'] == 'reject') $newRow['status'] = ClientCashout::STATUS_REJECTED;
                if ($newRow['status'] == 'complete') $newRow['status'] = ClientCashout::STATUS_PAYOUT_COMPLETED;
                $newRow['rejected_by_bank'] = $row['bank_rejected'];

                return $newRow;
            });
            $this->info('Mapping done');
            $this->info("Inserting mapped data into $tableName");
            DB::table($tableName)->insert($mappedData);
            $this->info("End of data migrate for $tableName");
            $this->compareCounts(
                'client_cashouts',
                DB::connection('mainapp')->table('client_cashouts')->count(),
                $tableName,
                ClientCashout::withTrashed()->count()
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function migrateWallet(): void
    {
        $tableName = (new Wallet())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $count = DB::connection('mainapp')->select('SELECT count(*) as count FROM `credits`')[0]->count;
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')->select("SELECT * FROM `credits` LIMIT $this->chunkSize OFFSET $i");
                $this->info('Fetched data');
                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    $newRow = [];
                    $newRow['id'] = $row['id'];
                    $newRow['created_at'] = $row['created_at'];
                    $newRow['updated_at'] = $row['updated_at'];
                    $newRow['profile_id'] = $row['client_id'];
                    $newRow['name'] = $row['wallet'];
                    $newRow['balance'] = $row['credit'];
                    $newRow['is_active'] = true;
                    return $newRow;
                });
                $this->info('Mapping done');
                DB::table($tableName)->insert($mappedData);
            }
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'credits',
                DB::connection('mainapp')->table('credits')->count(),
                $tableName,
                Wallet::withTrashed()->count()
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function migrateCreditTransaction(): void
    {
        $tableName = (new CreditTransaction())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $count = DB::connection('mainapp')->select('SELECT count(*) as count FROM `credit_transactions`')[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')->select("SELECT * FROM `credit_transactions` LIMIT $this->chunkSize OFFSET $i");
                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    $newRow = [];
                    $newRow['id'] = $row['id'];
                    $newRow['created_at'] = $row['created_at'];
                    $newRow['updated_at'] = $row['updated_at'];
                    $newRow['profile_id'] = $row['client_id'];
                    $newRow['wallet_id'] = 0;
                    $newRow['invoice_id'] = $row['invoice_id'];
                    $newRow['admin_id'] = $row['admin_user_id'];
                    $newRow['amount'] = $row['amount'];
                    $newRow['description'] = $row['description'];

                    return $newRow;
                });
                DB::table($tableName)->insert($mappedData);
                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'credit_transactions',
                DB::connection('mainapp')->table('credit_transactions')->count(),
                $tableName,
                CreditTransaction::withTrashed()->count()
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function migrateInvoice(): void
    {
        $tableName = (new Invoice())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $count = DB::connection('mainapp')->select("SELECT count(*) as count FROM `invoices`")[0]->count;

            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $whmcs_invoices = DB::connection('whmcs')->getDatabaseName() . '.tblinvoices';
                $oldData = DB::connection('mainapp')->select(
                    "SELECT inv.*,winv.taxrate,winv.notes FROM `invoices` as inv LEFT JOIN $whmcs_invoices as winv on winv.id=inv.invoice_id LIMIT $this->chunkSize OFFSET $i"
                );
                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    $newRow = [];
                    $newRow['id'] = $row['invoice_id'];
                    $newRow['created_at'] = $row['invoice_date'];
                    $newRow['updated_at'] = $row['updated_at'];
                    $newRow['profile_id'] = $row['client_id'];
                    $newRow['due_date'] = $row['due_date'];
                    $newRow['processed_at'] = $row['created_at'];
                    $newRow['paid_at'] = $row['paid_date'];
                    $newRow['rahkaran_id'] = $row['rahkaran_id'];
                    $newRow['payment_method'] = $row['payment_method'];
                    $newRow['balance'] = $row['balance'];
                    $newRow['total'] = $row['total'];
                    $newRow['sub_total'] = $row['sub_total'];
                    $newRow['tax_rate'] = $row['taxrate'] ?? 0;
                    $newRow['tax'] = $row['tax1'] + $row['tax2'];
                    $newRow['deleted_at'] = $row['deleted_at'];
                    $newStatus = null;
                    if ($row['status'] == 0) {
                        $newStatus = Invoice::STATUS_UNPAID;
                    } elseif ($row['status'] == 1) {
                        $newStatus = Invoice::STATUS_PAID;
                    } elseif ($row['status'] == 2) {
                        $newStatus = Invoice::STATUS_DRAFT;
                    } elseif ($row['status'] == 3) {
                        $newStatus = Invoice::STATUS_CANCELED;
                    } elseif ($row['status'] == 4) {
                        $newStatus = Invoice::STATUS_DELETED;
                    } elseif ($row['status'] == 5) {
                        $newStatus = Invoice::STATUS_PAYMENT_PENDING;
                    } elseif ($row['status'] == 6) {
                        $newStatus = Invoice::STATUS_REFUNDED;
                    } elseif ($row['status'] == 7) {
                        $newStatus = Invoice::STATUS_COLLECTIONS;
                    }
                    $newRow['status'] = $newStatus;
                    $newRow['is_mass_payment'] = $row['is_mass_payment'];
                    $newRow['admin_id'] = $row['manual_check'] == 1 ? 1 : null; // TODO check this
                    $newRow['is_credit'] = $row['is_credit'];
                    $newRow['note'] = $row['notes'];

                    return $newRow;
                });
                DB::table($tableName)->insert($mappedData);
                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $tableName");

            $this->info('invoice_counts');
            $this->table(
                [
                    'main_app:invoices',
                    "finance:$tableName",
                    'whmcs:tblinvoices'
                ],
                [
                    [
                        DB::connection('mainapp')->table('invoices')->count(),
                        Invoice::withTrashed()->count(),
                        DB::connection('whmcs')->table('tblinvoices')->count()
                    ]
                ]
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function migrateItem(): void
    {
        $tableName = (new Item())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $count = DB::connection('whmcs')->select("SELECT count(*) as count FROM `tblinvoiceitems`")[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('whmcs')->select("SELECT * FROM `tblinvoiceitems` LIMIT $this->chunkSize OFFSET $i");
                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    $newRow = [];
                    $newRow['id'] = $row['id'];
                    $newRow['created_at'] = Carbon::now()->toDateTimeString();
                    $newRow['updated_at'] = Carbon::now()->toDateTimeString();
                    $newRow['invoice_id'] = $row['invoiceid'];
                    $newRow['invoiceable_id'] = $row['relid'];
                    $newRow['invoiceable_type'] = $row['type'];
                    $newRow['amount'] = $row['amount'];
                    $newRow['discount'] = 0;
                    $newRow['from_date'] = null;
                    $newRow['to_date'] = null;
                    $newRow['description'] = $row['description'];

                    return $newRow;
                });
                DB::table($tableName)->insert($mappedData);
                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'tblinvoiceitems',
                DB::connection('whmcs')->table('tblinvoiceitems')->count(),
                $tableName,
                Item::withTrashed()->count()
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function migrateOfflineTransaction(): void
    {
        $tableName = (new OfflineTransaction())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $abnormalData = DB::connection('mainapp')
                ->select('SELECT offline_payments.* ,
                                        transactions.id as t_id,
                                        transactions.invoice_id as t_invoice_id,
                                        invoices.invoice_id as i_invoice_id,
                                        invoices.client_id as i_client_id
                                FROM offline_payments
                                LEFT JOIN transactions ON offline_payments.transaction_id = transactions.id
                                LEFT JOIN invoices ON transactions.invoice_id = invoices.invoice_id
                                WHERE invoices.client_id IS NULL');
            if (!empty($abnormalData)) {
                $this->error('SKIPPING ABNORMAL DATA (transactions.id):');
                collect((array)$abnormalData)->each(function ($row) {
                    $row = (array)$row;
                    $this->error($row['id']);
                });
            }

            $count = DB::connection('mainapp')->select('SELECT count(*) as count
                                                                FROM offline_payments
                                                                LEFT JOIN transactions ON offline_payments.transaction_id = transactions.id
                                                                LEFT JOIN invoices ON transactions.invoice_id = invoices.invoice_id
                                                                WHERE invoices.client_id IS NOT NULL')[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')
                    ->select("SELECT offline_payments.* ,
                                        transactions.id as t_id,
                                        transactions.invoice_id as t_invoice_id,
                                        invoices.invoice_id as i_invoice_id,
                                        invoices.client_id as i_client_id

                                FROM offline_payments
                                LEFT JOIN transactions ON offline_payments.transaction_id = transactions.id
                                LEFT JOIN invoices ON transactions.invoice_id = invoices.invoice_id
                                WHERE invoices.client_id IS NOT NULL
                                 LIMIT $this->chunkSize OFFSET $i");
                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    $newRow = [];
                    $newRow['id'] = $row['id'];
                    $newRow['created_at'] = $row['created_at'];
                    $newRow['updated_at'] = $row['updated_at'];
                    $newRow['paid_at'] = $row['paid_date'];
                    $newRow['profile_id'] = $row['i_client_id'];
                    $newRow['invoice_id'] = $row['i_invoice_id'];
                    $newRow['transaction_id'] = $row['transaction_id'];
                    $newRow['bank_account_id'] = $row['bank_account_id'];
                    $newRow['admin_id'] = $row['admin_user_id'];
                    $newRow['amount'] = strlen($row['amount']) > 0 ? $row['amount'] : 0;
                    if ($row['status'] == 0) {
                        $newRow['status'] = OfflineTransaction::STATUS_PENDING;
                    } elseif ($row['status'] == 1) {
                        $newRow['status'] = OfflineTransaction::STATUS_CONFIRMED;
                    } elseif ($row['status'] == 2) {
                        $newRow['status'] = OfflineTransaction::STATUS_REJECTED;
                    } else {
                        throw new Exception('invalid offline payment status id:' . $row['id'] . ' status:' . $row['status']);
                    }
                    $newRow['payment_method'] = $row['payment_method'];
                    $newRow['tracking_code'] = $row['tracking_code'];
                    $newRow['mobile'] = $row['mobile'];
                    $newRow['description'] = $row['description'];
                    $newRow['callback_url'] = $row['callback_url'];

                    return $newRow;
                });

                DB::table($tableName)->insert($mappedData);
                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'offline_payments',
                DB::connection('mainapp')->table('offline_payments')->count(),
                $tableName,
                OfflineTransaction::count()
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function migrateTransaction(): void
    {
        $tableName = (new Transaction())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            /*$abnormalData = DB::connection('mainapp')
                ->select('SELECT transactions.*,
                                        invoices.invoice_id as i_invoice_id,
                                        invoices.client_id as i_client_id
                                FROM `transactions`
                                LEFT JOIN invoices ON transactions.invoice_id = invoices.invoice_id
                                WHERE invoices.client_id IS NULL');
            if (!empty($abnormalData)) {
                $this->error('SKIPPING ABNORMAL DATA (transactions.id):');
                collect((array)$abnormalData)->each(function ($row) {
                    $row = (array)$row;
                    $this->error($row['id']);
                });
            }*/
            $count = DB::connection('mainapp')->select('SELECT count(*) as count
                                                                    FROM `transactions`
                                                                    LEFT JOIN invoices ON transactions.invoice_id = invoices.invoice_id
                                                                    WHERE invoices.client_id IS NOT NULL')[0]->count;
            $progress = $this->output->createProgressBar($count);
            $mainAppTransactions = [];
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')
                    ->select("SELECT transactions.*,
                                        invoices.invoice_id as i_invoice_id,
                                        invoices.client_id as i_client_id
                                    FROM `transactions`
                                    LEFT JOIN invoices ON transactions.invoice_id = invoices.invoice_id
                                    WHERE invoices.client_id IS NOT NULL
                                    LIMIT $this->chunkSize OFFSET $i");
                $mappedData = Arr::map($oldData, function ($row) use (&$mainAppTransactions) {
                    $row = (array)$row;
                    $newRow = [];

                    $newRow['id'] = $row['id'];
                    $newRow['created_at'] = $row['created_at'];
                    $newRow['updated_at'] = $row['updated_at'];
                    $newRow['profile_id'] = $row['i_client_id'];
                    $newRow['invoice_id'] = $row['invoice_id'];
                    $newRow['rahkaran_id'] = $row['rahkaran_id'];
                    $newRow['amount'] = $row['amount'];
                    $status = $newRow['status'] = match ($row['status']) {
                        0, 3, 4, 5 => Transaction::STATUS_PENDING,
                        1, 8, 25 => Transaction::STATUS_SUCCESS,
                        2, 7, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 22, 23, 20, 26, 24, 27, 28 => Transaction::STATUS_FAIL,
                        6, 9 => Transaction::STATUS_PENDING_BANK_VERIFY,
                        29 => Transaction::STATUS_CANCELED,
                        30 => Transaction::STATUS_REFUND,
                        default => throw new Exception('Invalid status in transactions table id:' . $row['id'] . ' status:' . $row['status']),
                    };

                    $mainAppTransactions[] = $status;

                    $newRow['payment_method'] = $row['payment_method'];
                    $newRow['description'] = $row['description'];
                    $newRow['ip'] = $row['ip'];
                    $newRow['tracking_code'] = $row['tracking_code'];
                    $newRow['reference_id'] = $row['reference_id'];

                    return $newRow;
                });

                DB::table($tableName)->insert($mappedData);

                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $tableName");

            $financeTransactionCounts = collect($mappedData)->groupBy('status')->map->count();
            $mainAppTransactionCounts = collect(array_count_values($mainAppTransactions));

            $this->info('count of each transaction in finance');
            $this->table([$financeTransactionCounts->keys()->toArray()], [$financeTransactionCounts->values()->toArray()]);

            $this->info('count of each transaction in main_app');
            $this->table([$mainAppTransactionCounts->keys()->toArray()], [$mainAppTransactionCounts->values()->toArray()]);

            $this->compareCounts(
                'transactions',
                DB::connection('mainapp')->table('transactions')->count(),
                $tableName,
                Transaction::count()
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function migrateInvoiceNumber(): void
    {
        $tableName = (new InvoiceNumber())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $count = DB::connection('mainapp')->select('SELECT count(*) as count FROM `invoice_numbers`')[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')
                    ->select("SELECT * FROM `invoice_numbers` LIMIT $this->chunkSize OFFSET $i");

                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    $newRow = [];

                    $newRow['id'] = $row['id'];
                    $newRow['created_at'] = $row['created_at'];
                    $newRow['updated_at'] = $row['updated_at'];
                    $newRow['deleted_at'] = $row['deleted_at'];
                    $newRow['invoice_number'] = $row['invoice_number'];
                    $newRow['fiscal_year'] = $row['fiscal_year'];
                    $newRow['type'] = $row['type'] == 'paid' ? InvoiceNumber::TYPE_PAID : InvoiceNumber::TYPE_REFUNDED;
                    $newRow['status'] = $row['status'] ? InvoiceNumber::STATUS_ACTIVE : InvoiceNumber::STATUS_PENDING;
                    $newRow['invoice_id'] = $row['invoice_id'];

                    return $newRow;
                });
                DB::table($tableName)->insert($mappedData);
                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'invoice_numbers',
                DB::connection('mainapp')->table('invoice_numbers')->count(),
                $tableName,
                InvoiceNumber::withTrashed()->count()
            );

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }

    private function syncWallets()
    {
        try {
            $this->alert('Sync credit transactions with wallet ids');
            DB::connection('mysql')->select('
                update credit_transactions as ct join wallets as ww on ww.profile_id = ct.profile_id
                set wallet_id = ww.id
                where ct.wallet_id = 0
            ');
            $this->info('End sync credit transactions with wallet ids');
        } catch (\Throwable $exception) {
            dump([
                'error'  => substr($exception->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
            $this->error('Something went wrong when sync credit transactions with wallet ids');
        }
    }

    public function compareCounts($mainAppTableName, $mainAppCount, $financeTableName, $financeCount)
    {
        $this->info('count of each record');
        $this->table(["main_app:$mainAppTableName", "finance:$financeTableName"], [
            [$mainAppCount, $financeCount]
        ]);
    }
}
