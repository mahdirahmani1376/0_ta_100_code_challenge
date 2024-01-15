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
    public const BAZAAR_PAY_API = '530005';
    public const MIN_DATE_OUT_OF_RANGE_FISCAL_YEAR = '570058';
    public const MAX_DATE_OUT_OF_RANGE_FISCAL_YEAR = '570059';
    public const INVOICE_IS_CREDIT_OR_MASS_PAYMENT = '410095';
    public const UPDATING_PAID_OR_REFUNDED_INVOICE_NOT_ALLOWED = '420029';
    public const INVOICE_HAS_ACTIVE_TRANSACTIONS = '420032';
    public const AT_LEAST_ONE_INVOICE_ITEM_MUST_REMAIN = '420031';
    public const APPLY_CREDIT_TO_CREDIT_INVOICE_NOT_ALLOWED = '410088';
    public const AMOUNT_IS_MORE_THAN_INVOICE_BALANCE = '410090';
    public const OFFLINE_PAYMENT_APPLY = "410133";
    public const INVOICE_CANCELLATION_FAILED = '410084';
    public const NOT_AUTHORIZED = '410110';
}
