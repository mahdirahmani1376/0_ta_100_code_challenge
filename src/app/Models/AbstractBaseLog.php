<?php

namespace App\Models;

use App\Traits\MongoDate;
use App\Traits\SoftDeletes;
use DateTimeInterface;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use MongoDB\Laravel\Eloquent\Model;

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
class AbstractBaseLog extends Model
{
    const PROVIDER_OUTGOING = "Outgoing";
    const PROVIDER_INCOMING = "Incoming";

    use SoftDeletes, MongoDate;
    public const ENDPOINT_WHMCS              = 'whmcs';
    public const ENDPOINT_CLOUD              = 'cloud_service';
    public const ENDPOINT_IRANNOAFARIN       = 'irannoafarin';
    public const ENDPOINT_SMS_SERVICE        = 'smsservice';
    public const ENDPOINT_ZARINPAL           = 'zarinpal';
    public const ENDPOINT_JIRA               = 'jira';
    public const ENDPOINT_ZIBAL              = 'zibal';
    public const ENDPOINT_ACTIVE_DIRECTORY   = 'active_directory';
    public const ENDPOINT_SHAHKAR            = 'shahkar';
    public const ENDPOINT_HETZNER            = 'hetzner';
    public const ENDPOINT_DOLLAR_PRICE       = 'dollar_price';
    public const ENDPOINT_AUTOVM             = 'autovm';
    public const ENDPOINT_TAKHFIFAN          = 'takhfifan';
    public const ENDPOINT_CPANEL             = 'cpanel';
    public const ENDPOINT_BACKUP_RESTORE     = 'backup_restore';
    public const ENDPOINT_NOTIFICATION       = 'notif_service';
    public const ENDPOINT_RAHKARAN           = 'rahkaran';
    public const ENDPOINT_DERAK              = 'derak';
    public const ENDPOINT_S3                 = 'S3';
    public const ENDPOINT_ZABBIX             =  "ZABBIX";
    public const ENDPOINT_ZIBAL_SERVICE      =  "zibal_service";
    public const ENDPOINT_CERTUM             =  "certum";
    public const ENDPOINT_CONFIG_SERVER      =  "config_server";
    public const ENDPOINT_DOMAIN_SERVICE     =  "domain_service";
    public const ENDPOINT_RAYCHAT_SERVICE    =  "Raychat";
    public const ENDPOINT_ASAN_PARDAKHT      =  "asan_pardakht";
    public const ENDPOINT_SADAD              =  "sadad";
    public const ENDPOINT_TELC               =  "Telc";
    public const ENDPOINT_DCIM               =  "DCIM";
    public const ENDPOINT_SHEBA              =  "SHEBA";
    public const ENDPOINT_TICKET_SERVICE     =  "ticketing_service";
    public const ENDPOINT_MATTERMOST_SERVICE =  "mattermost_service";
    public const ENDPOINT_NETWORK_MANAGEMENT =  "rtbh";
    public const ENDPOINT_PRODUCT_SERVICE    =  "product_service";
    public const ENDPOINT_SAMAN              = 'saman';
    public const ENDPOINTS = [
        self::ENDPOINT_WHMCS,
        self::ENDPOINT_CLOUD,
        self::ENDPOINT_IRANNOAFARIN,
        self::ENDPOINT_SMS_SERVICE,
        self::ENDPOINT_ZARINPAL,
        self::ENDPOINT_JIRA,
        self::ENDPOINT_ZIBAL,
        self::ENDPOINT_ACTIVE_DIRECTORY,
        self::ENDPOINT_SHAHKAR,
        self::ENDPOINT_HETZNER,
        self::ENDPOINT_AUTOVM,
    ];
    protected $connection = "mongodb";
    protected $table = '';

    public function make(): static
    {
        return new static();
    }

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

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
