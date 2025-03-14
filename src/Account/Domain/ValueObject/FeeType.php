<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\ValueObject;

enum FeeType: string
{
    case TransactionCosts = 'TransactionCosts';
    case OtherFeeType = 'OtherFeeType';
}
