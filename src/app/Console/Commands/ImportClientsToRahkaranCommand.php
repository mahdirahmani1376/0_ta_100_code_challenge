<?php

namespace App\Console\Commands;

use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\Rahkaran\RahkaranService;
use App\Integrations\Rahkaran\ValueObjects\Client;
use App\Models\Profile;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ImportClientsToRahkaranCommand extends Command
{
    protected $signature = 'rahkaran:import-clients {--clients=}';

    protected $description = 'Imports clients to rahkaran';

    protected int $errors = 0;

    private RahkaranService $rahkaranService;

    public function handle(): int
    {
        $this->rahkaranService = app(RahkaranService::class);
        App::setLocale('fa');
        $this->alert('Import clients into Rahkaran Date Time ' . now()->toDateTimeString());

        $profiles = $this->getProfiles();

        $bar = $this->output->createProgressBar($profiles->count());

        foreach ($profiles as $profile) {
            $client = data_get($profile->toArray(), 'client');

            if (!$client) {
                $this->info('Client not found for profile #' . $profile->id);
                continue;
            }
            $this->import($client, [], true);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Completed Date Time ' . now()->toDateTimeString());

        return 0;
    }

    protected function getProfiles(): \Illuminate\Support\Collection
    {
        $clients = collect([]);

	$inputClients = !empty($this->option('clients')) ? explode(',', $this->option('clients')) : []; 

	if ( count($inputClients) == 0 )
	{
		$this->error('No clients inputed');
		exit;
	}

        Profile::query()->whereIn('client_id',  $inputClients)->whereNull('rahkaran_id')->chunk(1000, function (Collection $profiles) use (&$clients) {
            $mainAppClients = MainAppAPIService::getClients($profiles->pluck('id')->toArray(), true);
            $mainAppClients = collect($mainAppClients);
            $profiles->each(function (Profile $profile) use ($mainAppClients) {
                $profile->offsetSet('client', $mainAppClients->firstWhere('finance_profile_id', $profile->id));
            });
            $clients->push($profiles);
        });

        return $clients->flatten();
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
                break;
            }

            if (Str::contains($message, 'Object reference not set to an instance of an object.') && $client->is_legal) {
                array_push($ignore_fields, 'company_address');
            }

            if (Str::contains($message, [
                    'فیلد تلفن در آدرس اجباری است',
                    'فیلد آدرس اجباری است',
                    'فیلد تلفن در آدرس اجباری است',
	    ]))
	    {
                array_push($ignore_fields, 'company_address');
                array_push($ignore_fields, 'address');
            }

            if (Str::contains($message, [
                    'کد ملی شخص باید یکتا باشد.',
                    'کد/شناسه ملی وارد شده معتبر نمی باشد',
            ])) {
                array_push($ignore_fields, 'national_code');
            }

            if (Str::contains($message, [
                'فرمت کد اقتصادی',
            ])) {
                array_push($ignore_fields, 'company_registered_number');
            }
        }

        return $this->import($client, $ignore_fields, true);
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
