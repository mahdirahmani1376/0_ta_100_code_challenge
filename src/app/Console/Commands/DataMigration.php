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
use Illuminate\Database\UniqueConstraintViolationException;
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
                $newRow['display_order'] = $row['order'];
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
//            DB::table($bankAccountTableName)->truncate();
            $this->info("Truncated $bankAccountTableName");
            DB::table($bankAccountTableName)->insert($newBankAccounts);
            $this->info("End of data migrate for $bankAccountTableName");
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $bankAccountTableName");
            dump($e);
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
//            DB::table($tableName)->truncate();
            $this->info("Truncated $tableName");
            DB::table($tableName)->insert($mappedData);
            $this->info("End of data migrate for $tableName");
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump($e);
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
                $newRow['profile_id'] = self::createProfile($row['client_id']);
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
//            DB::table($tableName)->truncate();
            $this->info("Truncated $tableName");
            DB::table($tableName)->insert($mappedData);
            $this->info("End of data migrate for $tableName");
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump($e);
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
                $newRow['profile_id'] = self::createProfile($row['client_id']);
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
//            DB::table($tableName)->truncate();
            $this->info("Truncated $tableName");
            $this->info("Inserting mapped data into $tableName");
            $mappedDataCount = count($mappedData);
            $counter = 0;
            collect($mappedData)->chunk($this->chunkSize)->each(function ($rows) use (&$counter, $mappedDataCount, $tableName) {
                DB::table($tableName)->insert($rows->toArray());
                $this->info("Inserted $counter out of $mappedDataCount items.");
                $counter += $this->chunkSize;
            });
            $this->info("End of data migrate for $tableName");
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump($e);
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
                    $newRow['profile_id'] = self::createProfile($row['client_id']);
                    $newRow['name'] = $row['wallet'];
                    $newRow['balance'] = $row['credit'];
                    $newRow['is_active'] = true;

                    return $newRow;
                });
                $this->info('Mapping done');
//            DB::table($tableName)->truncate();
                $this->info("Truncated $tableName");
                DB::table($tableName)->insert($mappedData);
            }
            $this->info("End of data migrate for $tableName");
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump($e);
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
                    $profileId = self::createProfile($row['client_id']);
                    $newRow['profile_id'] = $profileId;
                    $newRow['wallet_id'] = Wallet::query()->where('profile_id', $profileId)->firstOrCreate([
                        'profile_id' => $profileId,
                        'name'       => Wallet::WALLET_DEFAULT_NAME,
                        'balance'    => 0,
                        'is_active'  => true,
                    ])->getKey();
                    if (Invoice::where('id', $row['invoice_id'])->doesntExist()) {
                        return false;
                    }
                    $newRow['invoice_id'] = $row['invoice_id'];
                    $newRow['admin_id'] = $row['admin_user_id'];
                    $newRow['amount'] = $row['amount'];
                    $newRow['description'] = $row['description'];

                    return $newRow;
                });
                collect($mappedData)->filter(fn($value) => $value)->each(function ($rows) use ($count, &$counter, $tableName) {
                    DB::table($tableName)->insert($rows);
                });
                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $tableName");
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump($e);
        }
    }

    private function migrateInvoice(): void
    {
        $tableName = (new Invoice())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $count = DB::connection('mainapp')->select('SELECT count(*) as count FROM `invoices`')[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')->select("SELECT * FROM `invoices`  LIMIT $this->chunkSize OFFSET $i");
                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    $newRow = [];
                    $newRow['id'] = $row['invoice_id'];
                    $newRow['created_at'] = $row['invoice_date'];
                    $newRow['updated_at'] = $row['updated_at'];
                    $newRow['profile_id'] = self::createProfile($row['client_id']);
                    $newRow['due_date'] = $row['due_date'];
                    $newRow['paid_at'] = $row['paid_date'];
                    $newRow['rahkaran_id'] = $row['rahkaran_id'];
                    $newRow['payment_method'] = $row['payment_method'];
                    $newRow['balance'] = $row['balance'];
                    $newRow['total'] = $row['total'];
                    $newRow['sub_total'] = $row['sub_total'];
                    $newRow['tax_rate'] = 9;
                    $newRow['tax'] = $row['tax1'] + $row['tax2'];
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
                    if ($row['manual_check'] == 1) {
                        $newRow['admin_id'] = 1; // TODO check this
                    } else {
                        $newRow['admin_id'] = null;
                    }
                    $newRow['is_credit'] = $row['is_credit'];
                    try {
                        $id = $row['invoice_id'];
                        $note = DB::connection('whmcs')->select("SELECT `notes` FROM `tblinvoices` where `id` = $id")[0]->notes;
                        $newRow['note'] = empty($note) ? null : $note;
                    } catch (Exception $exception) {
                        $newRow['note'] = null;
                    }

                    return $newRow;
                });
                DB::table($tableName)->insert($mappedData);
                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $tableName");
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump($e);
        }
    }

    private function migrateItem(): void
    {
        $tableName = (new Item())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $invoiceIds = implode(',', Invoice::query()->select('id')->get()->pluck('id')->toArray());
            $count = DB::connection('whmcs')->select("SELECT count(*) as count FROM `tblinvoiceitems` where `invoiceid` in ($invoiceIds)")[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('whmcs')->select("SELECT * FROM `tblinvoiceitems` where `invoiceid` in ($invoiceIds)  LIMIT $this->chunkSize OFFSET $i");
                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    if (Invoice::query()->find($row['invoiceid']) == null) {
                        return null;
                    }

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
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump($e);
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
                    $newRow['profile_id'] = self::createProfile($row['i_client_id']);
                    $newRow['invoice_id'] = $row['i_invoice_id'];
                    if (Transaction::where('id', $row['transaction_id'])->doesntExist()) {
                        return false;
                    }
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
                        throw new \Exception('invalid offline payment status id:' . $row['id'] . ' status:' . $row['status']);
                    }
                    $newRow['payment_method'] = $row['payment_method'];
                    $newRow['tracking_code'] = $row['tracking_code'];
                    $newRow['mobile'] = $row['mobile'];
                    $newRow['description'] = $row['description'];

                    return $newRow;
                });
                collect($mappedData)->filter(fn($value) => $value)->chunk($this->chunkSize)->each(function ($rows) use ($count, &$counter, $tableName) {
                    DB::table($tableName)->insert($rows->toArray());
                });
                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $tableName");
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump($e);
        }
    }

    private function migrateTransaction(): void
    {
        $tableName = (new Transaction())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $abnormalData = DB::connection('mainapp')
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
            }
            $count = DB::connection('mainapp')->select('SELECT count(*) as count
                                                                    FROM `transactions`
                                                                    LEFT JOIN invoices ON transactions.invoice_id = invoices.invoice_id
                                                                    WHERE invoices.client_id IS NOT NULL')[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')
                    ->select("SELECT transactions.*,
                                        invoices.invoice_id as i_invoice_id,
                                        invoices.client_id as i_client_id
                                    FROM `transactions`
                                    LEFT JOIN invoices ON transactions.invoice_id = invoices.invoice_id
                                    WHERE invoices.client_id IS NOT NULL
                                    LIMIT $this->chunkSize OFFSET $i");
                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    $newRow = [];

                    $newRow['id'] = $row['id'];
                    $newRow['created_at'] = $row['created_at'];
                    $newRow['updated_at'] = $row['updated_at'];
                    $newRow['profile_id'] = self::createProfile($row['i_client_id']);
                    $newRow['invoice_id'] = $row['invoice_id'];
                    $newRow['rahkaran_id'] = $row['rahkaran_id'];
                    $newRow['amount'] = $row['amount'];

                    if ($row['status'] == 0) {
                        $newRow['status'] = Transaction::STATUS_PENDING;
                    } elseif ($row['status'] == 1) {
                        $newRow['status'] = Transaction::STATUS_SUCCESS;
                    } elseif ($row['status'] == 2) {
                        $newRow['status'] = Transaction::STATUS_FAIL;
                    } elseif ($row['status'] == 4) { // BANK_REFERRAL
                        $newRow['status'] = Transaction::STATUS_PENDING;
                    } elseif ($row['status'] == 5) {
                        $newRow['status'] = Transaction::STATUS_PENDING;
                    } elseif ($row['status'] == 6) {
                        $newRow['status'] = Transaction::STATUS_PENDING_BANK_VERIFY;
                    } elseif ($row['status'] == 7) {
                        $newRow['status'] = Transaction::STATUS_FAIL;
                    } elseif ($row['status'] == 8) {
                        $newRow['status'] = Transaction::STATUS_SUCCESS;
                    } elseif ($row['status'] == 9) {
                        $newRow['status'] = Transaction::STATUS_PENDING_BANK_VERIFY;
                    } elseif ($row['status'] == 10) {
                        $newRow['status'] = Transaction::STATUS_FAIL;
                    } elseif ($row['status'] == 20) { // 20 = STATUS_IPG_FAILED_TO_START
                        $newRow['status'] = Transaction::STATUS_FAIL; // TODO CHECK
                    } elseif ($row['status'] == 26) {
                        $newRow['status'] = Transaction::STATUS_FAIL;
                    } elseif ($row['status'] == 28) {
                        $newRow['status'] = Transaction::STATUS_FAIL;
                    } elseif ($row['status'] == 29) {
                        $newRow['status'] = Transaction::STATUS_CANCELED;
                    } elseif ($row['status'] == 30) {
                        $newRow['status'] = Transaction::STATUS_REFUND;
                    } else {
                        throw new \Exception('Invalid status in transactions table id:' . $row['id'] . ' status:' . $row['status']);
                    }
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
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump($e);
        }
    }

    private function migrateInvoiceNumber(): void
    {
        $tableName = (new InvoiceNumber())->getTable();
        $this->alert("Beginning to migrate $tableName");
        try {
            $count = DB::connection('mainapp')->select('SELECT count(*) as count
                                                                    FROM `invoice_numbers`
                                                                    WHERE EXISTS(SELECT * FROM `invoices` WHERE `invoice_numbers`.`invoice_id` = `invoices`.`invoice_id`)')[0]->count;
            $progress = $this->output->createProgressBar($count);
            for ($i = 0; $i <= $count; $i += $this->chunkSize) {
                $oldData = DB::connection('mainapp')
                    ->select("SELECT *
                                FROM `invoice_numbers`
                                WHERE EXISTS(SELECT * FROM `invoices` WHERE `invoice_numbers`.`invoice_id` = `invoices`.`invoice_id`)
                                LIMIT $this->chunkSize OFFSET $i");
                $mappedData = Arr::map($oldData, function ($row) {
                    $row = (array)$row;
                    $newRow = [];

                    $newRow['id'] = $row['id'];
                    $newRow['created_at'] = $row['created_at'];
                    $newRow['updated_at'] = $row['updated_at'];
                    $newRow['deleted_at'] = $row['deleted_at'];
                    $newRow['invoice_number'] = $row['invoice_number'];
                    $newRow['fiscal_year'] = $row['fiscal_year'];
                    $newRow['type'] = $row['type'] == 'paid' ? InvoiceNumber::TYPE_PAID : InvoiceNumber::TYPE_REFUND;
                    $newRow['status'] = $row['status'] ? InvoiceNumber::STATUS_USED : InvoiceNumber::STATUS_UNUSED;
                    $newRow['invoice_id'] = $row['invoice_id'];

                    return $newRow;
                });
                DB::table($tableName)->insert($mappedData);
                $progress->advance($this->chunkSize);
            }
            $this->newLine();
            $this->info("End of data migrate for $tableName");
        } catch (\Exception $e) {
            $this->error("Something went wrong when migrating $tableName");
            dump($e);
        }
    }

    private static function createProfile(int $clientId)
    {
        try {
            Profile::unguard();
            Profile::query()->create([
                'id'        => $clientId,
                'client_id' => $clientId
            ]);
            DB::connection('mainapp')
                ->table('clients')
                ->where('id', $clientId)
                ->update(['finance_profile_id' => $clientId,]);
            return $clientId;
        } catch (UniqueConstraintViolationException $exception) {
            return $clientId;
        } catch (Exception $exception) {
            dump($exception);
            exit('error while making profile id');
        }
    }
}
