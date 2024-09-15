<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class SplittingAllInvoiceItemsNotAllowedException
 * @package App\Exceptions\ApplicationException
 * @method static self make()
 */
class ItemAmountShouldNotBeZeroException extends BaseApplicationException
{
    // Error code 410097
    protected string $logRef = ExceptionCodes::ITEM_AMOUNT_MUST_NOT_BE_ZERO;

    protected int $errorCode = 400;

    protected int $logType = ExceptionTypes::TYPE_SERVICE;

}
