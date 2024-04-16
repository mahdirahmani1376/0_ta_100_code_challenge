<?php

namespace App\Console\Commands;

use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\Rahkaran\RahkaranService;
use App\Integrations\Rahkaran\ValueObjects\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ImportClientsToRahkaranCommand extends Command
{
    protected $signature = 'rahkaran:import-clients {clients?* : Client ids seperated by space,e.g. 1 2 3}';

    protected $description = 'Imports clients to rahkaran';

    protected int $errors = 0;

    private RahkaranService $rahkaranService;

    public function handle(): int
    {
        $this->rahkaranService = app(RahkaranService::class);
        App::setLocale('fa');
        $this->alert('Import clients into Rahkaran Date Time ' . now()->toDateTimeString());

        $clients = self::getClients(); // TODO fetch clients from MainApp

        $bar = $this->output->createProgressBar($clients->count());

        foreach ($clients as $client) {
            $this->import($client, [], true);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Completed Date Time ' . now()->toDateTimeString());

        return 0;
    }

    protected function getClients()
    {
        $financeProfileIds = [
            4,
        ];
        if (!empty($this->argument('clients'))) {
            $financeProfileIds = array_merge($financeProfileIds, $this->argument('clients'));
        }
        $this->info('Fetching client data from MainApp for: ' . implode(',', $financeProfileIds));
        $clients = MainAppAPIService::getClients($financeProfileIds, true);

        $this->info('Clients received, count:' . count($clients));

        return collect($clients);
    }

    protected function import(Client $client, array $ignore_fields = [], bool $retry = false)
    {
        $error_messages = $this->importClient($client, $ignore_fields);

        if (is_null($error_messages) || $retry == false) {
            return null;
        }

        foreach ($error_messages as $message) {

            if (Str::contains($message, [
                'ورود نام و نام خانوادگی الزامی می باشد'
            ])) {
                return null;
            }

            if (!in_array('company_address', $ignore_fields)) {

                if (Str::contains($message, 'Object reference not set to an instance of an object.') && $client->is_legal) {
                    array_push($ignore_fields, 'company_address');
                }
            }

            if ($client->is_legal && !in_array('company_address', $ignore_fields)) {

                if (Str::contains($message, [
                    'فیلد تلفن در آدرس اجباری است',
                    'فیلد آدرس اجباری است'
                ])) {
                    array_push($ignore_fields, 'company_address');
                    return $this->import($client, $ignore_fields, true);
                }
            }

            if (!$client->is_legal && !in_array('address', $ignore_fields)) {

                if (Str::contains($message, [
                    'فیلد تلفن در آدرس اجباری است',
                    'فیلد آدرس اجباری است'
                ])) {
                    array_push($ignore_fields, 'address');
                    return $this->import($client, $ignore_fields, true);
                }
            }

            if (!in_array('national_code', $ignore_fields)) {
                if (Str::contains($message, [
                    'کد ملی شخص باید یکتا باشد.',
                    'کد/شناسه ملی وارد شده معتبر نمی باشد',
                ])) {
                    array_push($ignore_fields, 'national_code');
                }
            }

            if (!in_array('company_registered_number', $ignore_fields)) {
                if (Str::contains($message, [
                    'فرمت کد اقتصادی',
                ])) {
                    array_push($ignore_fields, 'company_registered_number');

                    return $this->import($client, $ignore_fields, true);
                }
            }

            if (!in_array('company_national_code', $ignore_fields)) {
                if (Str::contains($message, [
                    'فرمت کد اقتصادی',
                ])) {
                    array_push($ignore_fields, 'company_national_code');

                    return $this->import($client, $ignore_fields, true);
                }
            }
        }

        return $this->import($client, $ignore_fields, false);
    }

    protected function importClient(Client $client, array $ignore_fields = []): ?array
    {
        try {
            $this->rahkaranService->createClientParty($client, $ignore_fields);
            return null;

        } catch (Throwable $exception) {
            $this->errors++;
            $error = "Import client error [{$this->errors}] Client #{$client->id} " . $exception->getMessage();
            Log::error($error);
            echo "\n Error! [{$this->errors}] $error \n";
            return explode(',', $exception->getMessage());
        }
    }
}
