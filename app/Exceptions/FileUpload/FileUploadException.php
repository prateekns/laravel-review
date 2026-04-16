<?php

namespace App\Exceptions\FileUpload;

use RuntimeException;

class FileUploadException extends RuntimeException
{
    public function __construct(
        string $message = "File upload failed",
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?string $filePath = null,
        public readonly ?string $originalName = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
