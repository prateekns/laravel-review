<?php

namespace App\Exceptions;

class EmptyFileException extends \Exception
{
    public function __construct(string $message = "The uploaded file is empty. Please provide valid customer data.")
    {
        parent::__construct($message);
    }
}
