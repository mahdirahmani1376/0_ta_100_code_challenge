<?php

namespace App\Services;

use App\Models\AbstractBaseLog;
use Illuminate\Support\Facades\Log;

class LogService
{
    public static function store(AbstractBaseLog $logModel, array $data): ?AbstractBaseLog
    {
        try {
            return $logModel->query()->create([
                'method'          => data_get($data, 'method'),
                'endpoint'        => data_get($data, 'endpoint'),
                'request_url'     => data_get($data, 'request_url'),
                'request_body'    => json_encode(data_get($data, 'request_body')),
                'request_header'  => json_encode(data_get($data, 'request_header')),
                'provider'        => data_get($data, 'provider'),
                'response_header' => data_get($data, 'response_header'),
                'response_body'   => data_get($data, 'response_body'),
                'response_status' => data_get($data, 'response_status')
            ]);
        } catch (\Exception $e) {
            Log::error('fail to store information in mongoDB', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
