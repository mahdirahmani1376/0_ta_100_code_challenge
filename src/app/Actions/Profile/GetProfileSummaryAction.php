<?php

namespace App\Actions\Profile;

use App\Models\Profile;
use App\Services\Invoice\IndexLatestInvoiceService;
use App\Services\Profile\FindOrCreateProfileService;
use App\Services\Profile\FindProfileByIdService;

class GetProfileSummaryAction
{
    public function __construct(
        private readonly FindProfileByIdService    $findProfileByIdService,
        private readonly IndexLatestInvoiceService $indexLatestInvoiceService
    )
    {
    }

    public function __invoke(int $profileId): array
    {
        $profile = ($this->findProfileByIdService)($profileId);

        $latest = ($this->indexLatestInvoiceService)($profileId);

        return [
            'latest_invoices' => $latest,
            'financial'       => [
                'total'       => $profile->invoices()->count(),
                'in_progress' => $latest->count()
            ]
        ];
    }
}
