<?php

namespace App\Actions\Invoice\MoadianLog;

use App\Exceptions\Http\BadRequestException;
use App\Integrations\Moadian\MoadianService;
use App\Models\MoadianLog;
use Illuminate\Support\Facades\Log;

class InquiryMoadianAction
{
    public function __invoke(MoadianLog $moadianLog): MoadianLog
    {
        if ($moadianLog->status == MoadianLog::STATUS_SUCCESS) {
            throw new BadRequestException(__('finance.error.MoadianAlreadySuccessful'));
        }

        try {

            MoadianService::inquiryMoadian($moadianLog);
            $moadianLog->refresh();

            return $moadianLog;
        } catch (\Exception $exception) {
            Log::emergency($exception->getMessage());

            throw new BadRequestException(__('finance.error.MoadianInquiryFataError'));
        }
    }
}