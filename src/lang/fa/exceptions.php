<?php


use App\Exceptions\Base\ExceptionCodes;

return [
    ExceptionCodes::REPOSITORY_DELETE_MODEL => 'مدل :model :id را نمی توان حذف نمود.',
    ExceptionCodes::REPOSITORY_MODEL_NOT_FOUND => 'مدل مورد نظر یافت نشد.',
    ExceptionCodes::LOCKED_INVOICE_ALREADY_IMPORTED_TO_RAHKARAN => 'فاکتور :invoice_id قابل ویرایش نیست.',
    ExceptionCodes::MAIN_APP_INTERNAL_API => 'MainApp internal api failed :url :param',
    ExceptionCodes::MIN_DATE_OUT_OF_RANGE_FISCAL_YEAR => 'حداقل تاریخ شروع باید از :fiscal_year_start_day بزرگتر باشد, مقدار کنونی :from_date',
    ExceptionCodes::MAX_DATE_OUT_OF_RANGE_FISCAL_YEAR => 'حداکثر تاریخ شروع باید از :fiscal_year_end_day کوچکتر باشد, مقدار کنونی :to_date',
    ExceptionCodes::INVOICE_IS_CREDIT_OR_MASS_PAYMENT => ':message',
    ExceptionCodes::UPDATING_PAID_OR_REFUNDED_INVOICE_NOT_ALLOWED => 'فاکتور مورد نظر قابل ویرایش نمی باشد.',
    ExceptionCodes::INVOICE_HAS_ACTIVE_TRANSACTIONS => 'فاکتور :invoice_id دارای تراکنش های موفق می باشد.',
    ExceptionCodes::AT_LEAST_ONE_INVOICE_ITEM_MUST_REMAIN => 'برای جداسازی فاکتور حداقل یک آیتم باید باقی بماند.',
    ExceptionCodes::APPLY_CREDIT_TO_CREDIT_INVOICE_NOT_ALLOWED => 'امکان استفاده از کیف پول برای فاکتورهای افزودن اعتبار وجود ندارد.',
    ExceptionCodes::AMOUNT_IS_MORE_THAN_INVOICE_BALANCE => 'مبلغ وارد شده بیشتر از مبلغ فاکتور میباشد.',
    ExceptionCodes::OFFLINE_PAYMENT_APPLY => 'پرداخت افلاین شماره :offline_payment_id قبلا تایید شده است.',
    ExceptionCodes::INVOICE_CANCELLATION_FAILED => 'فاکتور شماره :invoice_id قابل کنسل کردن نمیباشد.',
];
