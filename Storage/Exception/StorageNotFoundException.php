<?php namespace Burger\Storage\Exception;

use UnexpectedValueException as BaseException;

class StorageNotFoundException extends BaseException
{
    public function __construct($reference, $code = 0)
    {
        BaseException::__construct("Storage item not found with this reference: {$reference}", $code);
    }
}
