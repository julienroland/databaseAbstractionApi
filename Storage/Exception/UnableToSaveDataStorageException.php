<?php namespace Burger\Storage\Exception;

use UnexpectedValueException as BaseException;

class UnableToSaveDataStorageException extends BaseException
{
    public function __construct($query, $code = 0)
    {
        BaseException::__construct("Unable to save data using this query: {$query}", $code);
    }
}
