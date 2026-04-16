<?php

namespace App\Constants;

/**
 * Constants for storage paths used throughout the application
 */
final class StoragePaths
{
    /**
     * Base path for customer related files
     */
    public const CUSTOMER_BASE = 'customers/';

    /**
     * Path for customer equipment images
     */
    public const CUSTOMER_EQUIPMENT_IMAGES = self::CUSTOMER_BASE . 'equipment/';

    private const THUMBNAIL_PATH = 'thumbnail/';

    /**
     * Path for customer equipment thumbnail images
     */
    public const CUSTOMER_EQUIPMENT_THUMBNAIL_IMAGES = self::CUSTOMER_EQUIPMENT_IMAGES . self::THUMBNAIL_PATH;

    /**
     * Path for work order images
     */
    public const WORK_ORDER_IMAGES = 'work-orders/';

    /**
     * Path for work order thumbnail images
     */
    public const WORK_ORDER_THUMBNAIL_IMAGES = self::WORK_ORDER_IMAGES . self::THUMBNAIL_PATH;

    /**
     * Path for maintenance images
     */
    public const MAINTENANCE_IMAGES = 'maintenance-orders/';

    /**
     * Path for maintenance thumbnail images
     */
    public const MAINTENANCE_THUMBNAIL_IMAGES = self::MAINTENANCE_IMAGES . self::THUMBNAIL_PATH;
}
