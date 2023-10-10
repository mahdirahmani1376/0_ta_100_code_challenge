<?php

return [
    'error'        => [
        'RESPONSE_ERROR'                        => 'خطا در دریافت اطلاعات',
        'RESPONSE_PARSING_ERROR'                => 'خطا در تشخیص اطلاعات دریافت شده',
        'NOT_FOUND_PARTY'                       => 'پارتی با کد :party_id یافت نشد.',
        'NOT_FOUND_DL'                          => 'کد تفصیلی :code یافت نشد',
        'CONNECTION_ERROR'                      => 'خطا در اتصال به راهکاران',
        'SERVER_ERROR'                          => 'خطا در اتصال به راهکاران',
        'INVOICE_ERROR'                         => 'امکان صدور سند برای این فاکتور وجود ندارد',
        'TRANSACTION_ERROR'                     => 'امکان صدور اعلامیه واریز برای این تراکنش وجود ندارد',
        'DELETE_INVOICE_ERROR'                  => 'امکان حذف فاکتور وجود ندارد.',
        'NOT_FOUND_TRANSACTION_BANK_ACCOUNT_ID' => 'کد بانک راهکاران برای تراکنش :transaction_id یافت نشد',
    ],
    'party'        => [
        'MAIN_ADDRESS'    => 'آدرس اصلی',
        'COMPANY_ADDRESS' => 'آدرس شرکت',
    ],
    'dl'           => [
        'DOMAIN_REGISTRATION'  => 'ثبت دامنه',
        'INTERNATIONAL_DOMAIN' => 'دامنه بین المللی',
        'IR_DOMAIN'            => 'دامنه ir',
        'MANAGE_SERVICE'       => 'مدیریت سرویس',
        'ADMIN_TIME'           => "هزینه خدمات کارشناس",
        "CLOUD"                => 'خدمات ابری هاست ایران'
    ],
    'tax'          => [
        'tax'  => 'مالیات بر ارزش افزوده-6%',
        'toll' => 'مالیات بر ارزش افزوده-3%',
        'newTax' => 'مالیات و عوارض - 9%',
    ],
    'voucher'      => [
        'PAID_INVOICE'     => 'صورتحساب فروش :invoice_id',
        'REFUNDED_INVOICE' => 'صورتحساب بازگشت از فروش :invoice_id'
    ],
    'voucher_item' => [
        'collection' => 'درآمد وصول نشده',
        'credit'     => 'استفاده از اعتبار با استفاده از تراکنش :transaction_id',
        'payment'    => 'پرداخت با استفاده از تراکنش :transaction_id',
        'refund'     => 'بازگشت از فروش',
        'rounding'   => 'بابت رند شدن',
    ],
    'receipt'      => [
        'ADD_CREDIT'                       => 'افزایش اعتبار کاربری',
        'ADD_BASE_CREDIT'                  => 'اعتبار اولیه کاربر',
        'TRANSACTION_DESCRIPTION'          => 'تراکنش با کد پیگیری :code',
        'ROUNDING_TRANSACTION_DESCRIPTION' => 'تراکنش بابت رند شدن با کد پیگیری :code',
    ],
    'payment'      => [
        'REMOVE_CREDIT'           => 'کاهش اعتبار کاربری',
        'TRANSACTION_DESCRIPTION' => 'تراکنش با کد پیگیری :code'
    ]
];
