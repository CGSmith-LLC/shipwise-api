<?php

namespace common\exceptions;

use yii\base\ErrorException;

class OrderCancelException extends ErrorException
{
    public function __construct($message = 'Order cannot be cancelled.', $code = 0, $severity = 1, $filename = __FILE__, $lineno = __LINE__, $previous = null)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
}