<?php

namespace App\Console\Commands;

use App\Exceptions\SystemException\MainAppInternalAPIException;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\Rahkaran\RahkaranService;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Class ImportProductsToRahkaranCommand
 * @package App\Console\Commands
 */
class ImportProductsToRahkaranCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rahkaran:import-products';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports products to rahkaran';

    public function __construct(
        public MainAppAPIService $mainAppAPIService,
        public RahkaranService $rahkaranService
    )
    {
        parent::__construct();
    }

    /**
     * @return int
     * @throws ValidationException|MainAppInternalAPIException
     */
    public function handle(): int
    {
        $this->info('Create new DLs');

        $products = $this->mainAppAPIService->adminListProducts();

        foreach ( $products as $product )
        {
            try {
                $code = 60003000 + $product['id'];
                $description = 'محصول ' . $product['name'];

                $dl_object = $this->rahkaranService->getDl($code);
                if (!$dl_object) {
                    $this->rahkaranService->createDl((string)$code, 15, $description, $description);
                }

            } catch(Throwable $e) {
                $this->error('Fail on product ' . $product['name']);
            }
        }


        return 0;
    }
}

