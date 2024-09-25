<?php

namespace App\Console\Commands;

use App\Integrations\BankGateway\BazaarPay;
use App\Models\DirectPayment;
use App\Repositories\BankGateway\Interface\DirectPaymentRepositoryInterface;
use App\Services\BankGateway\DirectPayment\UpdateDirectPaymentService;
use App\Services\BankGateway\MakeBankGatewayProviderByNameService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckBazaarPayCommand extends Command
{
    protected $signature = 'bazaar-pay:check {--debug}';

    protected $description = 'Command description';

    public function __construct(
        private readonly DirectPaymentRepositoryInterface     $directPaymentRepository,
        private readonly UpdateDirectPaymentService           $updateDirectPaymentService,
        private readonly MakeBankGatewayProviderByNameService $makeBankGatewayProviderByNameService,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $directPaymentQuery = $this->directPaymentRepository
            ->newQuery()
            ->where('provider', 'bazaarPay')
            ->whereIn('status', [
                DirectPayment::STATUS_INIT,
                DirectPayment::STATUS_ACTIVE
            ]);

        $directPaymentsCounts = $directPaymentQuery->count();

        $this->info("total number of direct payment with status init,active available = $directPaymentsCounts");

        $bar = $this->output->createProgressBar($directPaymentsCounts);
        $bar->start();

        /** @var BazaarPay $bazaarPayBankGatewayProvider */
        $bazaarPayBankGatewayProvider = ($this->makeBankGatewayProviderByNameService)('bazaarPay');

        foreach ($directPaymentQuery->get() as $directPayment) {

            if (app()->environment('local') && $this->option('debug')) {
                Http::fake([
                    // Define the fake response for the URL pattern
                    '*' => Http::response([
                        'status' => 'cancelled',
                    ], 200),
                ]);
            }



            $response = $bazaarPayBankGatewayProvider->sendTraceRequest($directPayment);

            $responseStatus = $response->json('status');
            $newStatus = $this->matchDirectPayEnums($responseStatus);

            if ($directPayment->status != $newStatus) {

                if (!$this->option('debug')) {
                    ($this->updateDirectPaymentService)($directPayment, [
                        'status' => $newStatus,
                    ]);
                    $this->info(PHP_EOL . "Direct payment with id:$directPayment->id has status changed to $newStatus");
                } else {
                    $this->warn(PHP_EOL . "DEBUG: direct payment with id:$directPayment->id received status: $newStatus");
                }

            }

            $bar->advance();
        }

        $bar->finish();
        $this->info(PHP_EOL . 'command finished');

    }

    /**
     * this helper functions matches response enums to the direct pay enums
     * @see https://github.com/BazaarPay/Docs/blob/main/fa/direct-pay.md#نمونه-خطاها
     */
    private function matchDirectPayEnums($enum)
    {
        $enumsDictionary = [
            'active'    => DirectPayment::STATUS_ACTIVE,
            'new'       => DirectPayment::STATUS_INIT,
            'declined'  => DirectPayment::STATUS_INACTIVE,
            'cancelled' => DirectPayment::STATUS_INACTIVE,
            'expired'   => DirectPayment::STATUS_INACTIVE,
        ];

        $result = data_get($enumsDictionary, $enum);

        if (empty($result)) {
            $this->error(PHP_EOL . "no mapping fount for direct payment status {$enum}");
            Log::error("no mapping fount for direct payment status {$enum}");
        }

        return $result;

    }
}
