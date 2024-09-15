<?php

return [
    'rahkaran_baseurl'             => env('RAHKARAN_SERVICE_URL'),
    'rahkaran_username'            => env('RAHKARAN_SERVICE_USERNAME'),
    'rahkaran_password'            => env('RAHKARAN_SERVICE_PASSWORD'),
    'rahkaran_host'                => env('RAHKARAN_SERVICE_HOST'),
    'test_mode'                    => env('RAHKARAN_TEST_MODE', true),
    'legalPartyDlCode'             => env('RAHKARAN_LEGAL_PARTY_DL_CODE', 10001000),
    'personalPartyDlCode'          => env('RAHKARAN_PERSONAL_PARTY_DL_CODE', 20001000),
    'legalPartyDlType'             => env('RAHKARAN_LEGAL_PARTY_DL_TYPE', 2),
    'personalPartyDlType'          => env('RAHKARAN_PERSONAL_PARTY_DL_TYPE', 1),
    'productDlCode'                => env('RAHKARAN_PRODUCT_DL_CODE', 50001000),
    'productGroupDlCode'           => env('RAHKARAN_PRODUCT_GROUP_DL_CODE', 90000100),
    'level4DlType'                 => env('RAHKARAN_LEVEL4DL_TYPE', 12),
    'level5DlType'                 => env('RAHKARAN_LEVEL5DL_TYPE', 15),
    'level6DlType'                 => env('RAHKARAN_LEVEL5DL_TYPE', 15), // mahsool
    'paymentSl'                    => env('RAHKARAN_PAYMENT_SL', 1212705),
    'collectionSl'                 => env('RAHKARAN_COLLECTION_SL', 1212705),
    'taxSl'                        => env('RAHKARAN_TAX_SL', 1212221),
    'tollSl'                       => env('RAHKARAN_TOLL_SL', 1212222),
    'newTaxSl'                     => env('RAHKARAN_TOLL_SL', 1212223),
    'discountSl'                   => env('RAHKARAN_DISCOUNT_SL', 2111512),
    'creditSl'                     => env('RAHKARAN_CREDIT_SL', 1212705),
    'roundingSl'                   => env('RAHKARAN_ROUNDING_SL', 4113130),
    'roundingDl4Code'              => env('RAHKARAN_ROUNDING_DL4_CODE', 90000000),
    'roundingDl5Code'              => env('RAHKARAN_ROUNDING_DL5_CODE', 80000007),
    'saleSl'                       => env('RAHKARAN_SALE_SL', 2111311),
    'refundSl'                     => env('RAHKARAN_REFUND_SL', 2111312),
    'generalDl4Code'               => env('RAHKARAN_GENERAL_DL4_CODE', 90000001),
    'generalDl5Code'               => env('RAHKARAN_GENERAL_DL5_CODE', 50001000),
    'generalDl6Code'               => env('RAHKARAN_GENERAL_DL5_CODE', 60001000),
    'refundDl4Code'                => env('RAHKARAN_REFUND_DL4_CODE', 50000999),
    'domainDl4Code'                => env('RAHKARAN_DOMAIN_DL4_CODE', 90000003),
    'domainIrDl5Code'              => env('RAHKARAN_DOMAIN_IR_DL5_CODE', 50002002),
    'domainIntDl5Code'             => env('RAHKARAN_DOMAIN_INT_DL5_CODE', 50002003),
    'zibalBankId'                  => env('RAHKARAN_ZIBAL_BANK_ID', 16),
    'asanpardakhtBankId'           => env('RAHKARAN_ASANPARDAKHT_BANK_ID', 23),
    'zarinpalBankId'               => env('RAHKARAN_ZARINPAL_BANK_ID', 14),
    'zarinpalSmsBankId'            => env('RAHKARAN_ZARINPAL_SMS_BANK_ID', 17),
    'samanBankId'                  => env('RAHKARAN_SAMAN_BANK_ID', 28),
    'sadadBankId'                  => env('RAHKARAN_SADAD_BANK_ID', 15),
    'defaultBankId'                => env('RAHKARAN_DEFAULT_BANK_ID', 15),
    'iranKishBankId'               => env('RAHKARAN_IRANKISH_BANK_ID', 15),
    'mellatBankId'                 => env('RAHKARAN_MELLAT_BANK_ID', 1),
    'parsianBankId'                => env('RAHKARAN_PARSIAN_BANK_ID', 8),
    'creditBankId'                 => env('RAHKARAN_CREDIT_BANK_ID', 3),
    'roundingBankId'               => env('RAHKARAN_ROUNDING_BANK_ID', 15),
    'bankBranchId'                 => env('RAHKARAN_BANK_BRANCH_ID', 12), // according to the excel
    'fiscalYearRef'                => env('RAHKARAN_FISCAL_YEAR_REF', 3), // according to the example ارجاع به سال مالی: با توجه به دوره مالی که سند در آن ثبت میشود باید مشخص شود تیبل select fiscalyearid from gnr3.FiscalYear
    'voucherBranchRef'             => env('RAHKARAN_VOUCHER_BRANCH_REF', 1), // according to the excel
    'voucherCreatorId'             => env('RAHKARAN_VOUCHER_CREATOR_ID', 1),// according to the excel
    'voucherLedgerRef'             => env('RAHKARAN_VOUCHER_LEDGER_REF', 1), // according to the excel ارجاع به دفترکل در اینجا برابر با مقدار 1 می باشد
    'voucherState'                 => env('RAHKARAN_VOUCHER_STATE', 1),// according to the excel
    'voucherVoucherTypeRef'        => env('RAHKARAN_VOUCHER_VOUCHER_TYPE_REF', 1),// according to the excel ارجاع به نوع سند که در اینجا باید 1 باشد
    'voucherIsCurrencyBased'       => env('RAHKARAN_VOUCHER_IS_CURRENCY_BASED', 0),// according to the excel
    'receiptAccountingOperationID' => env('RAHKARAN_RECEIPT_ACCOUNTING_OPERATION_ID', 11),
    'receiptCashFlowFactorID'      => env('RAHKARAN_RECEIPT_CASH_FLOW_FACTOR_ID', 75),
    'paymentAccountingOperationID' => env('RAHKARAN_PAYMENT_ACCOUNTING_OPERATION_ID', 1),
    'paymentCashFlowFactorID'      => env('RAHKARAN_PAYMENT_CASH_FLOW_FACTOR_ID', 75),
    'defaultRegionalDivisionID'    => env('RAHKARAN_DEFAULT_REGIONAL_DIVISION_ID', 3),
    'salesDl5Code'                 => env('RAHKARAN_SALES_DL5_CODE', 60000000),
    'discountDl4Code'              => env('RAHKARAN_DISCOUNT_DL4_CODE', 90000001),
    'discountDl5Code'              => env('RAHKARAN_DISCOUNT_DL5_CODE', 60000000),
    'discountDl6Code'              => env('RAHKARAN_DISCOUNT_DL6_CODE', 50000000),
    "adminTimeDl4Code"             => env("RAHKARAN_ADMIN_TIME_DL4", 90000000),
    "adminTimeDl5Code"             => env("RAHKARAN_ADMIN_TIME_DL5", 50002004),
    "cloudDl4Code"                 => env("RAHKARAN_CLOUD_DL4", 90000105),
    "cloudDl5Code"                 => env("RAHKARAN_CLOUD_DL5", 50001063),
    "paymentFeeSL"                 => env("RAHKARAN_PAYMENT_FEE_SL", 5111411),
    "paymentFeeDL4"                => env("RAHKARAN_PAYMENT_FEE_DL4", 90000000),
    "paymentFeeDL5"                => env("RAHKARAN_PAYMENT_FEE_DL5", 80000001),
    "zarinpalDL4"                  => env("RAHKARAN_ZARINPAL_DL4", 30000012),
    "bankBaseSL"                   => env("RAHKARAN_BANK_BASE_SL", 1111111),
    "insuranceSl"                   => env("RAHKARAN_BANK_BASE_SL", 1118111)
];
