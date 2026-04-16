<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FileHelper;
use App\Constants\StoragePaths;

class CustomerEquipmentImageThumbUrl implements CastsAttributes
{
    /**
     * Cast the given value when retrieving.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value  Filename stored in DB
     * @param  array<string, mixed>  $attributes
     * @return string|null Temporary signed URL to the image
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        // Extract the base field name by removing '_thumb' suffix
        $baseField = str_replace('_thumb', '', $key);

        // Get the filename from the base image field
        $filename = $model->getRawOriginal($baseField);

        if (!$filename) {
            return null;
        }

        $path = StoragePaths::CUSTOMER_EQUIPMENT_THUMBNAIL_IMAGES . $filename;
        return FileHelper::getS3ImageUrl($path);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return mixed
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
