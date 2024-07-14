<?php

namespace App\Http\Controllers\Profile;

use App\Actions\Profile\GetProfileSummaryAction;
use App\Http\Controllers\Controller;

class GetProfileSummaryController extends Controller
{
    public function __construct(private readonly GetProfileSummaryAction $profileSummaryAction)
    {
        parent::__construct();
    }

    public function __invoke(int $profileId)
    {
        return ($this->profileSummaryAction)($profileId);
    }
}
