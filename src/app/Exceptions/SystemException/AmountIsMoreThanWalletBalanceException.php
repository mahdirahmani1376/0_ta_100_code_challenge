<?php

namespace App\Exceptions\SystemException;

use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class AmountIsMoreThanWalletBalance
 * @package App\Exceptions\SystemException
 * @method static self make()
 * @method self params()
 */
class AmountIsMoreThanWalletBalanceException extends BaseApplicationException
{
    protected string $logRef = ExceptionCodes::AMOUNT_IS_MORE_THAN_WALLET_BALANCE;

    protected int $errorCode = 403;

    protected int $logType = ExceptionTypes::TYPE_ACTION;

    protected array $messageParams = [];
}
