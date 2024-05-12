<?php

namespace App\Http\Controllers\Invoice\MoadianLog;

use App\Actions\Invoice\MoadianLog\InquiryMoadianAction;
use App\Http\Resources\Invoice\MoadianLog\MoadianLogResource;
use App\Models\MoadianLog;

class InquiryMoadianController
{
    public function __construct(private readonly InquiryMoadianAction $inquiryMoadianAction)
    {
    }

    public function __invoke(MoadianLog $moadianLog)
    {
        $result = ($this->inquiryMoadianAction)($moadianLog);

        return MoadianLogResource::make($result);
    }
}