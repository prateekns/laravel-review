<?php

namespace App\Exceptions\FileUpload;

use RuntimeException;

class S3ConfigurationException extends RuntimeException
{
    public function __construct(
        string $message = "Invalid S3 configuration",
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?array $config = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
