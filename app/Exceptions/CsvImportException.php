<?php

namespace App\Exceptions;

use Exception;

class CsvImportException extends Exception
{
    public function __construct(string $message = 'CSV import error occurred', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
