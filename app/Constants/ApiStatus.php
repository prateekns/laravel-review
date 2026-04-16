<?php

namespace App\Constants;

class ApiStatus
{
    public const OK = 200;
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const INTERNAL_SERVER_ERROR = 500;

    // Success
    public const SUCCESS = 'success';

    // Signature related errors
    public const MISSING_SIGNATURE_HEADERS = 'missing_signature_headers';
    public const SIGNATURE_EXPIRED = 'signature_expired';
    public const INVALID_SIGNATURE = 'invalid_signature';
    public const INVALID_TIMESTAMP = 'invalid_timestamp';
    public const INVALID_CREDENTIALS = 'invalid_credentials';

    // Auth
    public const TOKEN_EXPIRED = 'token_expired';
    public const TOKEN_INVALID = 'token_invalid';

    // Others
    public const VALIDATION_ERROR = 'validation_error';
    public const SERVER_ERROR = 'server_error';
    public const TWILIO_ERROR = 'twilio_error';
    public const OTP_EXPIRED = 'otp_expired';
    public const DEVICE_CHECK_FAILED = 'device_check_failed';
    public const LOGOUT_FAILED = 'logout_failed';
    public const INVALID_REFRESH_TOKEN = 'invalid_refresh_token';
    public const DEVICE_ID_REQUIRED = 'device_id_required';
    public const TOKEN_REFRESH_FAILED = 'token_refresh_failed';
    public const UNAUTHORIZED_DEVICE = 'unauthorized_device';
    public const MISSING_DEVICE_ID = 'missing_device_id';

    //API
    public const JOBS_FETCH_ERROR = 'jobs_fetch_error';
    public const JOB_ALREADY_COMPLETED = 'job_already_completed';
    public const TECHNICIAN_CHANGED = 'technician_changed';
    public const JOB_DETAILS_CHANGED = 'job_details_changed';
}
