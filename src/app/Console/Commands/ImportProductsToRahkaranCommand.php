<?php

namespace App\Console\Commands;

use App\Entities\Product;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\Rahkaran\RahkaranService;
use App\Traits\InteractsWithFinance;
use App\Traits\InteractsWithProduct;
use App\Traits\InteractsWithWhmcs;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

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
     * @throws ValidationException
     */
    public function handle(): int
    {
        $this->info('Create new DLs');

        $products = $this->mainAppAPIService->adminListProducts(['perPage' => 200]);
        foreach ( $products['data'] as $product )
        {
            try {
                $code = 60003000 + $product['id'];
                $description = 'محصول ' . $product['name'];

                $dl_object = $this->rahkaranService->getDl($code);
                if (!$dl_object)
                    $this->rahkaranService->createDl((string)$code, 15, $description, $description);
            } catch(\Exception $e) {
                $this->error('Fail on product ' . $product['name']);
            }
        }


        return 0;
    }
}

