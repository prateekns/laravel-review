<?php

namespace App\Exceptions;

use Exception;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\RateLimitException;
use Throwable;

class SubscriptionException extends Exception
{
    protected $original;

    public function __construct(Throwable $exception)
    {
        $this->original = $exception;
        parent::__construct($this->getFriendlyMessage($exception), $exception->getCode(), $exception);
    }

    public function render()
    {
        // Return a view or JSON
        return response()->view('errors.stripe', [
            'message' => $this->getMessage(),
        ], 500);
    }

    private function getFriendlyMessage(Throwable $e): string
    {
        // Customize the message based on the exception type
        return match (true) {
            $e instanceof ApiConnectionException => 'Unable to connect to Stripe. Please check your internet connection and try again.',
            $e instanceof AuthenticationException => 'Stripe authentication failed. Please contact support.',
            $e instanceof InvalidRequestException => 'Invalid request to Stripe. Please try again.',
            $e instanceof RateLimitException => 'Too many requests to Stripe. Please try again in a few minutes.',
            $e instanceof ApiErrorException => 'An error occurred with Stripe. Please try again later.',
            default => 'An unexpected error occurred. Please try again later.',
        };
    }

    public function getOriginal(): Throwable
    {
        return $this->original;
    }
}
