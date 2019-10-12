<?php


namespace App\Exceptions;


use Exception;

class ErrorControlledException extends Exception
{
    private $httpCode;
    private $exceptionClass;
    private $messageShort;

    /**
     * ErrorControlledException constructor.
     * @param int $httpCode
     * @param $messageShort
     * @param Exception $exception
     */
    public function __construct($httpCode, $exception, $messageShort = null)
    {
        parent::__construct($exception->getMessage());

        $this->httpCode = $httpCode;
        $this->exceptionClass = last(explode('\\', get_class($exception)));
        $this->messageShort = $messageShort;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getExceptionClass()
    {
        return $this->exceptionClass;
    }

    public function hasMessageShort()
    {
        return !is_null($this->messageShort);
    }

    public function getMessageShort()
    {
        return $this->messageShort;
    }
}