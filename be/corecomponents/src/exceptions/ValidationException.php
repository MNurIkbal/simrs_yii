<?php

namespace Doco\exceptions;

use yii\base\UserException;
use Doco\components\DocoHelpers;

class ValidationException extends UserException
{

    private $_options;
    private $_statusCode;
    private $_message;
    /**
     * Constructor.
     * @param int $status HTTP status code, such as 404, 500, etc.
     * @param string $message error message
     * @param array $options error message options
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($status, $message = null, $options = [], \Exception $previous = null)
    {
        $this->_message = $message;
        $this->_options = $options;
        $this->_statusCode = $status;
        parent::__construct($message, $status, $previous);
    }

    public function getOptions()
    {
        return DocoHelpers::callBack($this->_message, $this->_options, $this->_statusCode); 
    }
}