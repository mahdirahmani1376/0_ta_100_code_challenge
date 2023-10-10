<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseSystemException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class MaxDateIsOutOfRangeFiscalYearException
 * @package App\Exceptions\SystemException
 * @method static self make($to_date, $fiscal_year_end_day)
 * @method self params($to_date, $fiscal_year_end_day)
 */
class MaxDateIsOutOfRangeFiscalYearException extends BaseSystemException
{
    // Error code 570059
    protected string $logRef = ExceptionCodes::MAX_DATE_OUT_OF_RANGE_FISCAL_YEAR;

    protected int $logType = ExceptionTypes::TYPE_COMMAND;

    protected int $errorCode = 500;

    protected array $messageParams = [
        'to_date'               => '',
        'fiscal_year_end_day'   => '',
    ];
}
