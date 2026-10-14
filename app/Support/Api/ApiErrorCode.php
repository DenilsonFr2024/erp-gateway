<?php

namespace App\Support\Api;

final class ApiErrorCode
{
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';

    public const AUTHENTICATION_ERROR = 'AUTHENTICATION_ERROR';

    public const AUTHORIZATION_ERROR = 'AUTHORIZATION_ERROR';

    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const METHOD_NOT_ALLOWED = 'METHOD_NOT_ALLOWED';

    public const UNSUPPORTED_MEDIA_TYPE = 'UNSUPPORTED_MEDIA_TYPE';

    public const CONFLICT = 'CONFLICT';

    public const BUSINESS_RULE_ERROR = 'BUSINESS_RULE_ERROR';

    public const RATE_LIMIT_EXCEEDED = 'RATE_LIMIT_EXCEEDED';

    public const SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';

    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
}
