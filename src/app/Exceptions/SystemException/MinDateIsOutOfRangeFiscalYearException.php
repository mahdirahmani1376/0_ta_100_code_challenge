<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseSystemException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class MinDateIsOutOfRangeFiscalYearException
 * @package App\Exceptions\SystemException
 * @method static self make($from_date, $fiscal_year_start_day)
 * @method self params($from_date, $fiscal_year_start_day)
 */
class MinDateIsOutOfRangeFiscalYearException extends BaseSystemException
{
    // Error code 570058
    protected string $logRef = ExceptionCodes::MIN_DATE_OUT_OF_RANGE_FISCAL_YEAR;

    protected int $logType = ExceptionTypes::TYPE_COMMAND;

    protected int $errorCode = 500;

    protected array $messageParams = [
        'from_date'             => '',
        'fiscal_year_start_day' => ''
    ];
}
