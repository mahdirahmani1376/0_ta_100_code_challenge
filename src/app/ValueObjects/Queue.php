<?php

namespace App\ValueObjects;

/**
 * Class Queue
 * @package App\ValueObjects
 */
class Queue
{
    public const REGISTRATION_QUEUE = 'registration_queue';
    public const FORGET_PASSWORD_QUEUE = 'forget_password_queue';
    public const DEFAULT_QUEUE = 'default';
    public const CHANGE_IDENTIFIER_QUEUE = 'change_identifier';
    public const SYSTEM_LOG_QUEUE = 'system_log_queue';
    public const SYSTEM_SYNC = 'system_sync';
    public const SYSTEM_SYNC_HIGH_PRIORITY = 'system_sync_high_priority';
    public const CLOUD_QUEUE = 'cloud_services';
    public const DATACENTER_QUEUE = 'datacenter_queue';
    public const CLIENT_LOG_QUEUE = "client_log_queue";
    public const AUTHORIZATION_QUEUE = "client_authorization";
    public const BULK_MESSAGE_QUEUE = 'bulk_message_queue';
    public const UPLOAD_ISO = 'upload_iso';
}
