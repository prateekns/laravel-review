<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class AuthenticationException extends Exception
{
    /**
     * Create a new authentication exception instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = 'Unauthenticated.',
        int $code = Response::HTTP_UNAUTHORIZED,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error' => config('app.debug') ? $this->getPrevious()?->getMessage() : null,
        ], $this->getCode());
    }
}
