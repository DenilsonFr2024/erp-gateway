<?php

namespace App\Exceptions;

use App\Support\Api\ApiError;
use App\Support\Api\ApiErrorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiExceptionHandler
{
    public static function render(
        Request $request,
        Throwable $exception
    ): ?JsonResponse {
        if (! $request->is('api/*')) {
            return null;
        }

        /*
         * 422 - Validation
         */
        if ($exception instanceof ValidationException) {
            return ApiError::make(
                code: ApiErrorCode::VALIDATION_ERROR,
                message: 'The given data was invalid.',
                details: $exception->errors(),
                status: 422,
            );
        }

        /*
         * HTTP exceptions
         */
        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();

            return ApiError::make(
                code: self::httpExceptionCode($status),
                message: self::httpExceptionMessage($status),
                status: $status,
            );
        }

        /*
         * Unexpected errors
         */
        return ApiError::make(
            code: ApiErrorCode::INTERNAL_ERROR,
            message: 'An unexpected error occurred.',
            status: 500,
        );
    }

    private static function httpExceptionCode(int $status): string
    {
        return match ($status) {
            400 => ApiErrorCode::BUSINESS_RULE_ERROR,
            401 => ApiErrorCode::AUTHENTICATION_ERROR,
            403 => ApiErrorCode::AUTHORIZATION_ERROR,
            404 => ApiErrorCode::RESOURCE_NOT_FOUND,
            405 => ApiErrorCode::METHOD_NOT_ALLOWED,
            415 => ApiErrorCode::UNSUPPORTED_MEDIA_TYPE,
            409 => ApiErrorCode::CONFLICT,
            429 => ApiErrorCode::RATE_LIMIT_EXCEEDED,
            503 => ApiErrorCode::SERVICE_UNAVAILABLE,
            default => ApiErrorCode::INTERNAL_ERROR,
        };
    }

    private static function httpExceptionMessage(int $status): string
    {
        return match ($status) {
            400 => 'The request could not be processed.',
            401 => 'Authentication is required.',
            403 => 'You are not authorized to perform this action.',
            404 => 'The requested resource was not found.',
            409 => 'The request conflicts with the current state of the resource.',
            405 => 'The HTTP method is not allowed for this resource.',
            415 => 'The request content type is not supported.',
            429 => 'Too many requests. Please try again later.',
            503 => 'The service is temporarily unavailable.',
            default => 'An unexpected error occurred.',
        };
    }
}
