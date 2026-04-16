# Laravel Sanctum Setup Guide

This guide will help you set up Laravel Sanctum for API token authentication in your Laravel project.

---

## 1. Install Sanctum

```
composer require laravel/sanctum
```

---

## 2. Publish Sanctum Configuration

```
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```
This will create a `config/sanctum.php` file.

---

## 3. Run Sanctum Migrations

```
php artisan migrate
```
This creates the `personal_access_tokens` table.

---

## 4. Add Sanctum Middleware

### Laravel 11+ (`bootstrap/app.php`):
Add inside the `->withMiddleware` closure:

```php
$middleware->api([
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
]);
```

### Laravel 10 and below (`app/Http/Kernel.php`):
Add to the `api` middleware group:

```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

---

## 5. Use HasApiTokens in Your Model

Add the trait to your authenticatable model (e.g., `User` or `Technician`):

```php
use Laravel\Sanctum\HasApiTokens;

class Technician extends Model
{
    use HasApiTokens, ...;
}
```

---

## 6. Protect Routes with Sanctum Middleware

In `routes/api.php`:

```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

---

## 7. Issue Tokens on Login

In your login controller:

```php
$technician = Technician::where('staff_id', $request->staff_id)->first();
if ($technician && Hash::check($request->password, $technician->password)) {
    $token = $technician->createToken('technician-token')->plainTextToken;
    // return token in response
}
```

---

## 8. Use the Token in API Requests

Add this header to authenticated requests:

```
Authorization: Bearer <token>
```

---

## 9. Revoke Tokens on Logout

```php
$request->user()->currentAccessToken()->delete();
```

---

## 10. (Optional) Configure Sanctum

Adjust settings in `config/sanctum.php` as needed (expiration, stateful domains, etc).

---

## Summary of Commands

```
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

---

**You are now ready to use Sanctum for API authentication in Laravel!** 