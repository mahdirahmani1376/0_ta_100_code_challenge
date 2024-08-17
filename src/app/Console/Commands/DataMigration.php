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
use App\Models\MoadianLog;
use App\Models\OfflineTransaction;
use App\Models\Profile;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DataMigration extends Command
{
    protected $signature = 'app:data-migration';

    protected $description = 'Command description';

    protected int $chunkSize = 7000;

    public function handle()
    {
        ini_set('memory_limit', '4096M');
        $this->info("#### START DATA MIGRATION ####");
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $start_time = Carbon::now();
        $action = $this->choice('Which command to execute?', [
            'General',
            'Invoice',
            'Wallet'
        ]);

        if ($action == 'General') {
//            self::updateMainAppClients();
            self::migrateProfiles();
            self::migrateBankAccount();
            self::migrateBankGateway();
            self::migrateClientBankAccount();
            self::migrateClientCashout();
            self::migrateMoadianLog();
        }

        if ($action == 'Invoice') {
            self::migrateInvoice();
            self::migrateItem();
            self::migrateTransaction();
            self::migrateOfflineTransaction();
            self::migrateInvoiceNumber();
            self::syncInvoiceTaxRates();
        }

        if ($action == 'Wallet') {
            self::migrateWallet();
            self::migrateCreditTransaction();
        }

        $process_time = Carbon::now()->diffInSeconds($start_time);
        $this->info("#### END DATA MIGRATION in {$process_time} seconds");
    }

    private function calcProcessTime($process, $start = null)
    {
        if ($start) {
            $process_time = Carbon::now()->diffInSeconds($start);
            $minutes = round($process_time / 60);
            $this->info("#### END $process in {$process_time} seconds $minutes minute");
        }
    }

    private function migrateProfiles()
    {
        $now = Carbon::now();
        $profileTableName = (new Profile())->getTable();
        $this->alert("Beginning to migrate $profileTableName");
        try {
            $count = DB::connection('mainapp')->select('SELECT count(*) as count FROM `clients`')[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')->select("SELECT * FROM `clients` LIMIT $this->chunkSize OFFSET {$i}");
                $mappedData = [];
                foreach ($oldData as $row) {
                    $row = (array)$row;
                    $mappedData[] = [
                        'rahkaran_id' => $row['rahkaran_id'],
                        'client_id'   => $row['id'],
                        'id'          => $row['id'],
                        'created_at'  => $row['updated_at'],
                    ];
                }

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
            $this->calcProcessTime(__FUNCTION__, $now);
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
        $now = Carbon::now();
        try {
            $this->alert('Set client.id to finance profile id');
            DB::connection('mainapp')->select('
                UPDATE clients AS cl 
                SET cl.finance_profile_id = cl.id
                WHERE cl.finance_profile_id IS NULL
            ');
            $this->calcProcessTime(__FUNCTION__, $now);
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
        $now = Carbon::now();
        $bankAccountTableName = (new BankAccount)->getTable();
        $this->alert("Beginning to migrate $bankAccountTableName");
        try {
            $oldBankAccounts = DB::connection('mainapp')->select('SELECT * FROM `bank_accounts`');
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
            DB::table($bankAccountTableName)->insert($newBankAccounts);
            $this->info("End of data migrate for $bankAccountTableName");
            $this->compareCounts(
                'bank_accounts',
                DB::connection('mainapp')->table('bank_accounts')->count(),
                $bankAccountTableName,
                BankAccount::withTrashed()->count()
            );
            $this->calcProcessTime(__FUNCTION__, $now);

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
        $now = Carbon::now();
        $tableName = (new BankGateway())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $oldData = DB::connection('mainapp')->select('SELECT * FROM `payment_gateways`');
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
            DB::table($tableName)->insert($mappedData);
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'payment_gateways',
                DB::connection('mainapp')->table('payment_gateways')->count(),
                $tableName,
                BankGateway::withTrashed()->count()
            );
            $this->calcProcessTime(__FUNCTION__, $now);
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
        $now = Carbon::now();
        $tableName = (new ClientBankAccount())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $oldData = DB::connection('mainapp')->select('SELECT * FROM `client_bank_accounts`');
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
            DB::table($tableName)->insert($mappedData);
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'client_bank_accounts',
                DB::connection('mainapp')->table('client_bank_accounts')->count(),
                $tableName,
                ClientBankAccount::withTrashed()->count()
            );
            $this->calcProcessTime(__FUNCTION__, $now);
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
        $now = Carbon::now();
        $tableName = (new ClientCashout())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $oldData = DB::connection('mainapp')->select('SELECT * FROM `client_cashouts`');
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
            DB::table($tableName)->insert($mappedData);
            $this->compareCounts(
                'client_cashouts',
                DB::connection('mainapp')->table('client_cashouts')->count(),
                $tableName,
                ClientCashout::withTrashed()->count()
            );
            $this->calcProcessTime(__FUNCTION__, $now);
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
        $now = Carbon::now();
        $tableName = (new Wallet())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $count = DB::connection('mainapp')->select('SELECT count(*) as count FROM `credits`')[0]->count;
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')->select("SELECT 
                    cc.id         as id,
                    cc.created_at as created_at,
                    cc.updated_at as updated_at,
                    NULL          as deleted_at,
                    cc.client_id  as profile_id,
                    cc.wallet     as name,
                    cc.credit     as balance,
                    1             as is_active 
                    FROM `credits` as cc LIMIT $this->chunkSize OFFSET $i"
                );
                $mappedData = [];
                foreach ($oldData as $row) {
                    $mappedData[] = (array)$row;
                }
                DB::table($tableName)->insert($mappedData);
            }
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'credits',
                DB::connection('mainapp')->table('credits')->count(),
                $tableName,
                Wallet::withTrashed()->count()
            );
            $this->calcProcessTime(__FUNCTION__, $now);
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
        $now = Carbon::now();
        $tableName = (new CreditTransaction())->getTable();
        $fulTableName = DB::connection('mysql')->getDatabaseName() . '.' . $tableName;
        $main_db = DB::connection('mainapp')->getDatabaseName();
        $main_db_credit_transactions = $main_db . '.credit_transactions';
        $this->alert("Beginning to migrate $tableName");
        $total = DB::connection('mainapp')->table('credit_transactions')->count();
        $chunkSize = 10_000; // Define your chunk size here
        try {
            $progress = $this->output->createProgressBar($total);
            for ($offset = 0; $offset < $total; $offset += $chunkSize) {
                $query = "INSERT INTO $fulTableName
(id,
 created_at,
 updated_at,
 profile_id,
 wallet_id,
 invoice_id,
 admin_id,
 amount,
 description)
    (SELECT ct.id            as id,
            ct.created_at    as created_at,
            ct.updated_at    as updated_at,
            ct.client_id     as profile_id,
            ct.credit_id     as wallet_id,
            ct.invoice_id    as invoice_id,
            ct.admin_user_id as admin_id,
            ct.amount        as amount,
            ct.description   as description
     from $main_db_credit_transactions as ct LIMIT $chunkSize OFFSET $offset)";

                DB::connection('mysql')->select($query);
                $progress->advance($chunkSize);
            }
            $this->newLine();

            $this->compareCounts(
                'credit_transactions',
                DB::connection('mainapp')->table('credit_transactions')->count(),
                $tableName,
                CreditTransaction::withTrashed()->count()
            );
            $this->calcProcessTime(__FUNCTION__, $now);
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
        $now = Carbon::now();
        $tableName = (new Invoice())->getTable();
        $main_invoices_table_name = DB::connection('mainapp')->getDatabaseName() . '.invoices';
        $whmcs_invoices_table_name = DB::connection('whmcs')->getDatabaseName() . '.tblinvoices';
        $fulTableName = DB::connection('mysql')->getDatabaseName() . '.' . $tableName;
        $this->alert("Beginning to migrate $tableName");
        try {
            $query = "
            INSERT INTO $fulTableName
(id,
 created_at,
 updated_at,
 deleted_at,
 profile_id,
 due_date,
 processed_at,
 paid_at,
 rahkaran_id,
 payment_method,
 balance,
 total,
 sub_total,
 tax_rate,
 tax,
 is_mass_payment,
 admin_id,
 source_invoice,
 status)
    (SELECT inv.invoice_id                        as id,
            inv.invoice_date                      as created_at,
            inv.updated_at                        as updated_at,
            inv.deleted_at                        as deleted_at,
            inv.client_id                         as profile_id,
            inv.due_date                          as due_date,
            CASE
                WHEN inv.status = 1 THEN inv.paid_date
                WHEN inv.status = 6 THEN inv.paid_date
                WHEN inv.status = 7 THEN inv.invoice_date
                ELSE NULL
                END                               as processed_at,
            inv.paid_date                         as paid_at,
            inv.rahkaran_id                       as rahkaran_id,
            inv.payment_method                    as payment_method,
            inv.balance                           as balance,
            inv.total                             as total,
            inv.sub_total                         as sub_total,
            10 as tax_rate,
            inv.tax1 + inv.tax2                   as tax,
            inv.is_mass_payment                   as is_mass_payment,
            IF(inv.manual_check = TRUE, 1, NULL)  as admin_id,
            inv.source_invoice                    as source_invoice,
            CASE
                WHEN inv.status = 0 THEN 'unpaid'
                WHEN inv.status = 1 THEN 'paid'
                WHEN inv.status = 2 THEN 'draft'
                WHEN inv.status = 3 THEN 'canceled'
                WHEN inv.status = 4 THEN 'deleted'
                WHEN inv.status = 5 THEN 'payment_pending'
                WHEN inv.status = 6 THEN 'refunded'
                WHEN inv.status = 7 THEN 'collections'
                END                               as status
            FROM $main_invoices_table_name as inv)";

            DB::connection('mysql')->select($query);
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
            $this->calcProcessTime(__FUNCTION__, $now);
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
        $now = Carbon::now();
        $tableName = (new Item())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $count = DB::connection('whmcs')->select("SELECT count(*) as count FROM `tblinvoiceitems`")[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('whmcs')->select("SELECT 
                    it.id          as id,
                    NOW()          as created_at,
                    NOW()          as updated_at,
                    invoiceid      as invoice_id,
                    it.relid       as invoiceable_id,
                    it.type        as invoiceable_type,
                    it.amount      as amount,
                    0              AS discount,
                    it.description as description FROM `tblinvoiceitems` as it LIMIT $this->chunkSize OFFSET $i"
                );
                $mappedData = [];
                foreach ($oldData as $row) {
                    $mappedData[] = (array)$row;
                }
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
            $this->calcProcessTime(__FUNCTION__, $now);

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
        $now = Carbon::now();
        $tableName = (new OfflineTransaction())->getTable();
        $main_db = DB::connection('mainapp')->getDatabaseName();
        $main_db_offline_payments = $main_db . '.offline_payments';
        $main_db_invoices = $main_db . '.invoices';
        $main_db_transactions = $main_db . '.transactions';
        $fulTableName = DB::connection('mysql')->getDatabaseName() . '.' . $tableName;
        $this->alert("Beginning to migrate $tableName");
        try {

            $query = "
            INSERT INTO $fulTableName
(id,
 created_at,
 updated_at,
 paid_at,
 profile_id,
 invoice_id,
 transaction_id,
 bank_account_id,
 admin_id,
 amount,
 status,
 payment_method,
 tracking_code,
 mobile,
 description)
SELECT op.id              as id,
       op.created_at      as created_at,
       op.updated_at      as updated_at,
       op.paid_date       as paid_at,
       inv.client_id      as profile_id,
       inv.invoice_id     as invoice_id,
       op.transaction_id  as transaction_id,
       IF(op.bank_account_id > 0, op.bank_account_id, 0) as bank_account_id,
       op.admin_user_id   as admin_id,
       op.amount          as amount,
       case op.status
           when op.status = 0 then 'pending'
           when op.status = 1 then 'confirmed'
           when op.status = 2 then 'rejected'
           ELSE 'rejected'
           end            as status,
       IF(op.payment_method != NULL, op.payment_method, 'unknown')  as payment_method,
       op.tracking_code   as tracking_code,
       op.mobile          as mobile,
       op.description     as description

FROM $main_db_offline_payments as op
         LEFT JOIN $main_db_transactions as trx ON op.transaction_id = trx.id
         LEFT JOIN $main_db_invoices as inv ON trx.invoice_id = inv.invoice_id
WHERE inv.client_id IS NOT NULL
            ";

            DB::connection('mysql')->select($query);

            $this->newLine();
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'offline_payments',
                DB::connection('mainapp')->table('offline_payments')
                    ->leftJoin($main_db_transactions, 'offline_payments.transaction_id', '=', "{$main_db_transactions}.id")
                    ->leftJoin($main_db_invoices, "$main_db_transactions.invoice_id", '=', "{$main_db_invoices}.invoice_id")
                    ->where("{$main_db_invoices}.client_id", "!=", NULL)
                    ->count(),
                $tableName,
                OfflineTransaction::count()
            );
            $this->calcProcessTime(__FUNCTION__, $now);
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
        $now = Carbon::now();
        $tableName = (new Transaction())->getTable();
        $main_db = DB::connection('mainapp')->getDatabaseName();
        $main_db_transactions = $main_db . '.transactions';
        $main_db_invoices = $main_db . '.invoices';
        $fulTableName = DB::connection('mysql')->getDatabaseName() . '.' . $tableName;
        $this->alert("Beginning to migrate $tableName");
        try {
            $query = "INSERT INTO $fulTableName
(id,
 created_at,
 updated_at,
 profile_id,
 invoice_id,
 rahkaran_id,
 amount,
 status,
 payment_method,
 description,
 ip,
 tracking_code,
 reference_id,
 callback_url)
SELECT trx.id             as id,
       trx.created_at     as created_at,
       trx.updated_at     as updated_at,
       inv.client_id      as profile_id,
       inv.invoice_id     as invoice_id,
       trx.rahkaran_id    as rahkaran_id,
       trx.amount         as amount,
       case trx.status
           when trx.status = 0 OR trx.status = 3 OR trx.status = 4 OR trx.status = 5 then 'pending'
           when trx.status = 1 OR trx.status = 8 OR trx.status = 25 then 'success'
           when trx.status = 2 OR trx.status = 7 OR trx.status = 10 OR trx.status = 11 OR trx.status = 12 OR
                trx.status = 13 OR trx.status = 14 OR trx.status = 15 OR trx.status = 16 OR trx.status = 17 OR
                trx.status = 18 OR trx.status = 19 OR trx.status = 21 OR trx.status = 22 OR trx.status = 23 OR
                trx.status = 20 OR trx.status = 26 OR trx.status = 24 OR trx.status = 27 OR trx.status = 28
               then 'success'
           when trx.status = 6 OR trx.status = 9 then 'pending_bank_verify'
           when trx.status = 29 then 'canceled'
           when trx.status = 30 then 'refund'
           else 'unknown'
           end            as status,
       trx.payment_method as payment_method,
       trx.description    as description,
       trx.ip             as ip,
       trx.tracking_code  as tracking_code,
       trx.reference_id   as reference_id,
       trx.callback_url   as callback_url

FROM $main_db_transactions as trx
         LEFT JOIN $main_db_invoices as inv ON trx.invoice_id = inv.invoice_id
WHERE inv.client_id IS NOT NULL";

            DB::connection('mysql')->select($query);
            $this->newLine();
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'transactions',
                DB::connection('mainapp')->table('transactions')->count(),
                $tableName,
                Transaction::count()
            );
            $this->calcProcessTime(__FUNCTION__, $now);
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
        $now = Carbon::now();
        $tableName = (new InvoiceNumber())->getTable();
        $main_db = DB::connection('mainapp')->getDatabaseName();
        $main_db_invoice_numbers = $main_db . '.invoice_numbers';
        $fulTableName = DB::connection('mysql')->getDatabaseName() . '.' . $tableName;
        $this->alert("Beginning to migrate $tableName");
        try {

            $query = "
            INSERT INTO $fulTableName
(id,
 created_at,
 updated_at,
 deleted_at,
 invoice_number,
 fiscal_year,
 type,
 status,
 invoice_id)
    SELECT invn.id                                    as id,
            invn.created_at                            as created_at,
            invn.updated_at                            as updated_at,
            invn.deleted_at                            as deleted_at,
            invn.invoice_number                        as invoice_number,
            invn.fiscal_year                           as fiscal_year,
            IF(invn.type = 'paid', 'paid', 'refunded') as type,
            IF(invn.status = TRUE, '1', '0')           as status,
            invn.invoice_id                            as invoice_id
     from $main_db_invoice_numbers as invn";
            DB::connection('mysql')->select($query);
            $this->newLine();
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'invoice_numbers',
                DB::connection('mainapp')->table('invoice_numbers')->count(),
                $tableName,
                InvoiceNumber::withTrashed()->count()
            );
            $this->calcProcessTime(__FUNCTION__, $now);

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
        $now = Carbon::now();
        try {
            $this->alert('Sync credit transactions with wallet ids');
            DB::connection('mysql')->select('
                update credit_transactions as ct join wallets as ww on ww.profile_id = ct.profile_id
                set wallet_id = ww.id
                where ct.wallet_id = 0
            ');
            $this->info('End sync credit transactions with wallet ids');
            $this->calcProcessTime(__FUNCTION__, $now);
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

    private function syncInvoiceTaxRates()
    {
        $whmcs_tax_rates = [
            3, 9, 4, 5, 0, 6, 10, ""
        ];

        foreach ($whmcs_tax_rates as $rate) {
            $this->info('Start invoices with tax : ' . $rate);
            $invoices = DB::connection('whmcs')->table('tblinvoices')
                ->select(['id', 'taxrate'])->where('taxrate', 3)
                ->orderBy('id')
                ->chunk(10000, function (Collection $whmcs_invoices) use ($rate) {
                    $finance_invoices = DB::connection('mysql')->table('invoices')
                        ->whereIn('id', $whmcs_invoices->pluck('id'))
                        ->update([
                            'tax_rate' => is_numeric($rate) ? $rate : 0
                        ]);

                    $this->info("Update {$finance_invoices} rows successfully tax rate to => $rate");
                });

        }
    }

    private function migrateMoadianLog()
    {
        $now = Carbon::now();
        $tableName = (new MoadianLog())->getTable();
        $main_db = DB::connection('mainapp')->getDatabaseName();
        $main_db_moadian = $main_db . '.moadian_logs';
        $fulTableName = DB::connection('mysql')->getDatabaseName() . '.' . $tableName;
        $this->alert("Beginning to migrate $tableName");
        try {

            $query = "INSERT INTO $tableName
(id,
 created_at,
 updated_at,
 invoice_id,
 status,
 reference_code,
 tax_id,
 error)
SELECT id,
       created_at,
       updated_at,
       invoice_id,
       status,
       reference_code,
       tax_id,
       error
from $main_db_moadian as ml";
            DB::connection('mysql')->select($query);
            $this->newLine();
            $this->info("End of data migrate for $tableName");

            $this->compareCounts(
                'moadian_logs',
                DB::connection('mainapp')->table('moadian_logs')->count(),
                $tableName,
                MoadianLog::count()
            );
            $this->calcProcessTime(__FUNCTION__, $now);

        } catch (Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump([
                'error'  => substr($e->getMessage(), 0, 500),
                'method' => __FUNCTION__
            ]);
        }
    }
}
