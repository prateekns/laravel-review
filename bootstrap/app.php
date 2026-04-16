<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\EnsureTechnicianUser;
use App\Http\Middleware\EnsureOnboarding;
use App\Http\Middleware\CanSubscribe;
use App\Http\Middleware\VerifyAppSignature;
use App\Http\Middleware\CanCreate;
use App\Http\Middleware\EnsureBusinessAccess;
use App\Http\Middleware\CheckDevice;
use App\Http\Middleware\RequestLogger;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Bind our custom authentication middleware to replace the built-in one
        $middleware->alias([
            'pool.auth'      => Authenticate::class,
            'pool.guest'     => RedirectIfAuthenticated::class,
            'subscription.active' => EnsureSubscriptionActive::class,
            'technician' => EnsureTechnicianUser::class,
            'checkDevice' => CheckDevice::class,
            'verifyAppSignature' => VerifyAppSignature::class,
            'onboarding' => EnsureOnboarding::class,
            'canSubscribe' => CanSubscribe::class,
            'can.create' => CanCreate::class,
            'ensure.business.access' => EnsureBusinessAccess::class,
        ]);

        // $middleware->use([RequestLogger::class]);

        $middleware->validateCsrfTokens(except: [
            'stripe/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle AuthenticationException
        $exceptions->render(function (AuthenticationException $e, $request) {
            if (isApiRequest($request)) {
                return handleApiAuthError($request);
            }
            return handleWebAuthError($e);
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($e instanceof HttpException && $e->getStatusCode() === 419) {
                return redirect()->route('login', ['expired' => 1]);
            }
            return null;
        });

        // Handle general exceptions
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            if (isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => __('api.too_many_login_attempts'),
                ], 429);
            }
        });

        // Handle general exceptions
        $exceptions->render(function (\Exception $e, $request) {
            if (isApiRequest($request)) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error' => app()->environment('local') ? $e->getTrace() : null
                ], 500);
            }
        });

        // Handle file upload exceptions
        $exceptions->render(function (\Symfony\Component\HttpFoundation\File\Exception\UploadException $e, $request) {
            if (isApiRequest($request)) {
                Log::error('Upload size exceeds limit: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Upload size exceeds limit.',
                ], 413);
            }
        });
    })->create();
/**
 * Check if the request is an API request
 */
function isApiRequest(Request $request): bool
{
    return str_starts_with($request->path(), 'api/') || $request->expectsJson();
}

/**
 * Handle API authentication errors
 */
function handleApiAuthError(Request $request): \Illuminate\Http\JsonResponse
{
    $message = $request->header('Authorization')
        ? __('messages.auth.token_invalid')
        : __('messages.auth.token_missing');

    return response()->json([
        'success' => false,
        'code' => 401,
        'message' => $message,
    ], 401);
}

/**
 * Handle web authentication errors
 */
function handleWebAuthError(AuthenticationException $e): \Illuminate\Http\RedirectResponse
{
    $route = in_array('business', $e->guards())
        ? 'login'
        : 'admin.login';

    return redirect()
        ->route($route)
        ->with('message', __('messages.session_timeout'));
}
