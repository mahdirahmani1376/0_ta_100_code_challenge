<?php

namespace App\Exceptions\Base;

/**
 * Class ExceptionCodes
 * @package App\ValueObjects
 * {4|5}{exception_type:1~8}{exception_number:xxxx}
 */
final class ExceptionCodes
{
    public const REPOSITORY_DELETE_MODEL = '530001';
    public const REPOSITORY_MODEL_NOT_FOUND = '530002';
    public const LOCKED_INVOICE_ALREADY_IMPORTED_TO_RAHKARAN = '530003';
    public const MAIN_APP_INTERNAL_API = '530004';
}
