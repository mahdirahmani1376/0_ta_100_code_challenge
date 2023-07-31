<?php

return [
    'error' => [
        'RecalculationError' => 'خطایی در محاسبه بالانس فاکتور :invoice_id رخ داده است.',
        'RemoveCredit' => 'خطایی در برداشت اعتبار از فاکتور :invoice_id رخ داده است.',
        'RefundPayment' => 'خطایی در برداشت مبلغ پرداختی از فاکتور :invoice_id رخ داده است.',
        'InvoiceCreationError' => 'خطایی در ایجاد فاکتور رخ داده است.',
        'RefundInvoiceCreationError' => 'خطایی در بازگشت وجه توسط فاکتور :invoice_id رخ داده است.',
        'DeletePaymentError' => 'خطایی هنگام حذف تراکنش رخ داده است.',
        'OnlyUnpaidInvoiceAllowed' => 'این امکان فقط برای فاکتورهای پرداخت نشده وجود دارد.',
        'OnlyPendingOfflinePaymentAllowed' => 'این امکان فقط برای پرداخت آفلاین درحال انتظار وجود دارد.',
        'CannotMakeMassPaymentInvoice' => 'هیچ یک از فاکتور های ارسال شده امکان پرداخت گروهی ندارد.',
    ],
    'invoice' => [
        'AmountExceedsInvoiceBalance' => 'مقدار انتخاب شده از مبلغ فاکتور بیشتر می باشد.',
        'NegativeBalance' => 'بالانس فاکتور منفی می باشد.',
        'OfficialInvoiceStatusError' => 'امکان صدور فاکتور فقط برای فاکتور های پرداخت شده بازگشت وجه و پرداخت نشده وجود دارد',
        'LessThan1400' => 'برای صدور فاکتور های صادر شده پیش از سال ۱۴۰۰ لطفا درخواست خود را از طریق تیکت به واحد فروش ارسال فرمایید. ',
        'ClientActive' => 'حساب کابری شما فعال نشده است',
        'InvoiceNumberNotFound' => 'شماره فاکتور برای فاکتور شما ایجاد نشده است لطفا درخواست خود را از طریق تیکت به واحد فروش ارسال فرمایید.',
        'InvoiceNumberFileNotFound' => 'فایل فاکتور رسمی پیدا نشد.',
        'NotCorrectStatus' => 'این فاکتور در وضعیت درست قرار ندارد',
        'MassPaymentInvoice' => 'پرداخت فاکتور شماره :id',
        'ClientCreditInvoiceItem' => 'افزایش اعتبار کیف پول',
        'PaidByPartner' => 'پرداخت توسط :partner',
        'PartnerPayment' => 'پرداخت سرویس :service و دامنه :domain برای کاربر :client',
        'PartnerCommission' => 'تخفیف به دلیل همکاری در فروش سفارش :order_id',
        'unpaid_invoice_description' => 'شماره حساب نزد بانک :title با شماره حساب :account_number و شماره شبا :sheba_number و شماره کارت :card_number',
        'RefundCancelledDomain' => 'بازگشت هزینه به دلیل کنسل شدن دامین :domain',
        "GraceDomainInvoiceItemDescription" => "بابت پرداخت جریمه تمدید دامنه :domain در سررسید :expire_date",
        "MassPaymentInvoiceItemFailed" => "خطای پرداخت گروهی فاکتور :mass_invoice_id",
        "MassPaymentInvoiceItemFailedDescription" => "پرداخت صورت حساب شماره :invoice_id ناموفق بود. صورت حساب :",
        "ChangeServiceInvoiceItemDescription" => "هزینه تبدیل سرویس :domain به محصول :product تا تاریخ :expire_date",

    ],
    'credit' => [
        'WalletAdjustment' => 'اصلاحیه اعتبار کیف پول',
        'RefundInvoiceCreditOverpayment' => 'افزایش اعتبار به دلیل پرداخت بیش از مقدار فاکتور :invoice_id',
        'RefundInvoicePayment' => 'افزایش اعتبار به دلیل بازگشت مبلغ پرداخت شده از فاکتور :invoice_id',
        'RemoveInvoiceCredit' => 'افزایش اعتبار به دلیل بازگشت اعتبار از فاکتور :invoice_id',
        'RefundCancelledInvoice' => 'افزایش اعتبار به دلیل بازگشت وجه از فاکتور کنسل شده :invoice_id',
        'RefundTotalCredit' => 'افزایش اعتبار به دلیل حذف اعتبار از فاکتور :invoice_id',
        'RefundDeletedInvoiceCredit' => 'حذف شده افزایش اعتبار به دلیل بازگشت وجه از فاکتور :invoice_id',
        'RefundRefundedInvoiceCredit' => 'افزایش اعتبار به دلیل بازگشت وجه از فاکتور بازگشت از فروش :invoice_id',
        'RefundInvoiceCredit' => 'بازگشت از فاکتور :invoice_id',
        'AddCreditInvoice' => 'افزایش اعتبار توسط فاکتور :invoice_id',
        'AddMassPaymentInvoice' => 'افزایش اعتبار توسط فاکتور تجمیعی :invoice_id',
        'ApplyCreditToInvoice' => 'کاهش اعتبار به دلیل پرداخت فاکتور :invoice_id',
        'ApplyCreditToInvoiceWithCloud' => 'کاهش اعتبار توسط زیر ساخت ابری به دلیل پرداخت فاکتور :invoice_id',
        'RefundCancelledOrder' => 'افزایش اعتبار به دلیل بازگشت وجه از سفارش کنسل شده :invoice_id',
        'RefundDeletedInvoice' => 'افزایش اعتبار به دلیل بازگشت وجه از فاکتور حذف شده :invoice_id',
        'RefundForApplyCloudExpenses' => 'افزایش اعتبار به دلیل محاسبه‌‌ی فاکتور سرویس ابری به شماره :invoice_id',
        'AddCommissionForOrder' => 'افزایش اعتبار به دلیل همکاری در فروش سفارش :order_id',
        'InvoiceOnlyAppliedToUnPaid' => 'وضعیت فاکتور باید پرداخت نشده باشد.',
        'ApplyCreditToCreditInvoiceNotAllowed' => 'امکان استفاده از کیف پول برای فاکتورهای افزودن اعتبار وجود ندارد.',
        'TransferCreditFromClient' => 'انتقال اعتبار از کاربر :client_id',
        'TransferCreditToClient' => 'انتقال اعتبار به کاربر :client_id',
        'NotEnoughBalance' => 'موجودی شما کافی نیست.',
        'PartnerCreditCorrection' => 'اصلاحیه اعتبار کیف پول برای همکار فروش برای پرداخت فاکتور :invoice_id',
        'RefundedCreditCorrection' => 'اصلاحیه اعتبار کیف پول برای افزودن تراکنش  بازگشت از فروش فاکتور :invoice_id',
        'InvoiceCreditCorrection' => 'اصلاحیه اعتبار کیف پول برای پرداخت فاکتور :invoice_id',
    ],
    'transaction' => [
        'RefundTransaction' => 'تراکنش افزایش اعتبار فاکتور بازگشت از فروش',
        'RoundingTransaction' => 'تراکنش بابت رند شدن',
    ],
    'ipg' => [
        'OnlyUnknownAndReadyToPayStatusAreAllowed' => 'فقط تراکنش هایی با وضعیت نامشخص می توانند توسط درگاه اینترنتی تایید شوند.'
    ],
    "domain" => [
        "item_description" => ":type دامنه :domain به مدت :period سال از تاریخ :from تا تاریخ :to",
        "types" => [
            "register" => "ثبت",
            "transfer" => "انتقال",
            "renew" => "تمدید",
            "backorder" => "پیش سفارش"
        ],
        'id_protection' => "هزینه Id protection"

    ],
    'service' => [
        'item_description' => ':type سرویس :domain به مدت :period سال از تاریخ :from تا تاریخ :to',
        'upgrade' => [
            'quantity' => ':config_name (:quantity -> :new_quantity)',
            'option' => ':config_name (:value_name -> :new_value_name)',
        ],
        'create' => [
            'item_description' => 'خرید سرویس :domain ',
            'quantity' => ':config_name (:quantity -> :new_quantity)',
            'option' => ':config_name (:value_name -> :new_value_name)',
        ],
        'types' => [
            'register' => 'ثبت',
            'upgrade' => 'ارتقا',
            'downgrade' => 'تنزیل',
            'renew' => 'تمدید',
        ]
    ],
    'affiliation' => [
        'item_desc' => 'بابت تخفیف استفاده از کد :code'
    ]
];
