<?php

namespace App\Models;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

/**
 * @property mixed method
 * @property mixed|string endpoint
 * @property array|mixed request_header
 * @property Repository|Application|mixed request_url
 * @property array|mixed request_body
 * @property mixed|string provider
 * @property mixed|string response_header
 * @property mixed|string response_body
 * @property mixed response_status
 */
class SystemLog extends AbstractBaseLog
{
    protected $table = 'admin_logs';
    const PROVIDER_OUTGOING = "Outgoing";
    const PROVIDER_INCOMING = "Incoming";
    public const ENDPOINT_RAHKARAN = 'rahkaran';


    protected $fillable = [
        'method',
        'endpoint',
        'request_url',
        'request_body',
        'request_header',
        'response_header',
        'response_body',
        'response_status',
        'provider'
    ];
}
