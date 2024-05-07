<?php

use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Helpers\JalaliCalender;
use App\Models\AdminLog;
use App\Models\FinanceLog;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Support\Str;

if (!function_exists('get_paginate_params')) {
    function get_paginate_params(): array
    {
        $perPage = request()->get('perPage');
        $page = request()->get('page');

        if (empty($perPage) || is_array($perPage) || is_object($perPage) || (int)$perPage < 0 || (int)$perPage > 200) {
            $perPage = 10;
        }
        if (empty($page) || is_array($page) || is_object($page) || ((int)$page < 0)) {
            $page = 1;
        }

        return ['per_page' => (int)$perPage, 'page' => (int)$page];
    }
}

if (!function_exists("get_sortable_items")) {
    /**
     * @param array $items
     * @return array
     */
    function get_sortable_items(array $items): array
    {
        return array_unique(array_merge($items, ['created_at', 'updated_at', 'id']));
    }
}

if (!function_exists("check_rahkaran")) {
    /**
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    function check_rahkaran(Invoice $invoice): void
    {
        if (
            $invoice->rahkaran_id
            ||
            in_array($invoice->status, [Invoice::STATUS_REFUNDED, Invoice::STATUS_PAID,])
        ) {
            throw InvoiceLockedAndAlreadyImportedToRahkaranException::make($invoice->getKey());
        }
    }
}

if (!function_exists('fa2en')) {
    function fa2en($string)
    {
        return str_replace(
            ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
            , $string);
    }
}

if (!function_exists('clean_ir_mobile')) {
    function clean_ir_mobile($input)
    {
        $number = fa2en($input);
        $number = str_replace('+', '00', $number);
        $number = preg_replace('/[\D]/', '', $number);

        if (strpos($number, '00989') === 0 && strlen($number) === 14) {
            return 0 . substr($number, 4, strlen($number));
        }

        if (strpos($number, '989') === 0 && strlen($number) === 12) {
            return 0 . substr($number, 2, strlen($number));
        }

        if (strpos($number, '9') === 0 && strlen($number) === 10) {
            return 0 . $number;
        }

        if (strpos($number, '09') === 0 && strlen($number) === 11) {
            return $number;
        }

        return false;
    }
}

if (!function_exists('is_json')) {
    function is_json($string)
    {
        json_decode($string, true);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}

if (!function_exists('admin_log')) {
    function admin_log(string $action, $model = null, $changes = null, $oldState = null, $validatedData = null, $adminId = null)
    {

        if (is_null($adminId) && is_null(request('admin_id'))) {
            return;
        }

        $adminLog = '';

        try {
            if (!is_array($oldState)) {
                $oldState = $oldState?->toArray();
            }
            $adminLog = AdminLog::query()->create([
                'admin_user_id' => $adminId ?? request('admin_id'),
                'action'        => $action,
                'logable_id'    => $model?->id,
                'logable_type'  => $model ? get_class($model) : null,
                'after'         => $changes,
                'before'        => $oldState,
                'request'       => $validatedData,
            ]);
        } catch (Exception $exception) {
            // TODO
        }

        return $adminLog;
    }
}

if (!function_exists('finance_log')) {
    function finance_log(string $action, $model = null, $changes = null, $oldState = null, $validatedData = null)
    {
        $financeLog = '';

        try {
            if (!is_array($oldState)) {
                $oldState = $oldState?->toArray();
            }
            $financeLog = FinanceLog::query()->create([
                'action'       => $action,
                'logable_id'   => $model?->id,
                'logable_type' => $model ? get_class($model) : null,
                'after'        => $changes,
                'before'       => $oldState,
                'request'      => $validatedData,
            ]);
        } catch (Exception $exception) {
        }

        return $financeLog;
    }
}

if (!function_exists('math_subtract')) {
    /**
     * @param float|int|string $minuend
     * @param float|int|string $subtrahend
     * @param int $decimal_places
     * @return float|int
     */
    function math_subtract($minuend, $subtrahend, int $decimal_places)
    {
        $decimal = pow(10, $decimal_places);

        $minuend = round($minuend * $decimal);

        $subtrahend = round($subtrahend * $decimal);

        return ($minuend - $subtrahend) / $decimal;
    }
}

if (!function_exists('finance_report_dates')) {
    function finance_report_dates($from = null, $to = null): array
    {
        if ($from && $to) {
            return [$from, $to];
        }

        [$j_year, $j_month, $j_day] = explode('/', JalaliCalender::getJalaliString(now()));

        $startOfCurrentMonth = JalaliCalender::makeCarbonByJalali($j_year, $j_month, 1);
        $startOfCurrentYear = JalaliCalender::makeCarbonByJalali($j_year, 1, 1);

        [$startOfLastMonth, $to] = JalaliCalender::getRange($j_year, $j_month, $j_day, 'monthly', true);
        [$to_j_year, $to_j_month] = explode('/', JalaliCalender::getJalaliString($to));

        $lastMonthTo = JalaliCalender::makeCarbonByJalali(
            $to_j_year,
            $to_j_month,
            $j_day > JalaliCalender::jalaaliMonthLength($to_j_year, $to_j_month) ? JalaliCalender::jalaaliMonthLength($to_j_year, $to_j_month) : $j_day
        );

        return [$startOfCurrentMonth->toDateString(), now()->toDateString()];
    }
}

if (!function_exists('parse_string')) {
    function parse_string($string, $array): string
    {
        foreach ($array as $key => $value) {
            $string = str_replace(":$key", $value, $string);
        }

        return $string;
    }
}

if (!function_exists('callback_result_redirect_url')) {
    function callback_result_redirect_url($url, int $invoiceId, string $transactionStatus = null, string $invoiceStatus = null): string
    {
        if (is_null($transactionStatus)) {
            $status = match ($invoiceStatus) {
                Invoice::STATUS_CANCELED, Invoice::STATUS_DRAFT, Invoice::STATUS_DELETED => Transaction::STATUS_FAIL,
                Invoice::STATUS_PAID, Invoice::STATUS_REFUNDED => Transaction::STATUS_SUCCESS,
            };
        } else {
            $status = $transactionStatus;
        }

        return Str::swap([
            '{invoice}' => $invoiceId,
            '{status}'  => $status,
        ], $url);
    }
}

if (!function_exists('normalise_sheba_number')) {
    function normalise_sheba_number(string $shebaNumber): string
    {
        return Str::of($shebaNumber)
            ->replace('I', '', caseSensitive: false)
            ->replace('R', '', caseSensitive: false)
            ->start('IR');
    }
}
