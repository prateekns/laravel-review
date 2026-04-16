<?php

namespace App\Models\Business\WorkOrder\Concerns;

use App\Constants\StoragePaths;
use App\Helpers\FileHelper;

trait WorkOrderImageTrait
{
    /**
     * Handle work order image management during model saving
     */
    protected function handleImageManagement(): void
    {
        $paths = [
            'main' => $this->type == 'WO' ? StoragePaths::WORK_ORDER_IMAGES : StoragePaths::MAINTENANCE_IMAGES,
            'thumb' => $this->type == 'WO' ? StoragePaths::WORK_ORDER_THUMBNAIL_IMAGES : StoragePaths::MAINTENANCE_THUMBNAIL_IMAGES
        ];

        if ($this->isDirty('photo') && ($oldImage = $this->getRawOriginal('photo'))) {
            FileHelper::deleteFile($paths['main'] . $oldImage, 's3');
            FileHelper::deleteFile($paths['thumb'] . $oldImage, 's3');
        }

        if (request()->has('delete_photo') && request()->boolean('delete_photo')) {
            if ($oldImage = $this->attributes['photo'] ?? null) {
                FileHelper::deleteFile($paths['main'] . $oldImage, 's3');
                FileHelper::deleteFile($paths['thumb'] . $oldImage, 's3');
            }
            $this->photo = null;
        }
    }

    /**
     * Get the photo URL attribute
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }
        $path = $this->type == 'WO' ? StoragePaths::WORK_ORDER_IMAGES : StoragePaths::MAINTENANCE_IMAGES;
        $path .= $this->photo;
        return FileHelper::getS3ImageUrl($path);
    }

    /**
     * Get the thumbnail photo URL attribute
     */
    public function getPhotoThumbUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        $path = $this->type == 'WO' ? StoragePaths::WORK_ORDER_THUMBNAIL_IMAGES : StoragePaths::MAINTENANCE_THUMBNAIL_IMAGES;
        $path = $path . $this->photo;
        return FileHelper::getS3ImageUrl($path);
    }
}
