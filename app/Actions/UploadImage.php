<?php

namespace App\Actions;

use App\Exceptions\UploadException;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Log;
use App\Exceptions\FileUpload\FileUploadException;

class UploadImage
{
    /**
     * Handle image upload.
     *
     * @throws \Exception
     */
    public function handle(?string $image, string $uploadPath): ?string
    {
        if (! $image) {
            return null;
        }

        try {
            return FileHelper::s3ImgUpload($uploadPath, $image);
        } catch (FileUploadException $e) {
            Log::error('File Upload Failed: ' . $e->getMessage());
            throw new UploadException($e->getMessage());
        }
    }
}
