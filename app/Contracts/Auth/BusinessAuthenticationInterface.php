<?php

namespace App\Contracts\Auth;

use App\Http\Requests\Business\Auth\LoginRequest;
use App\Http\Requests\Business\Auth\RegisterRequest;
use App\Models\Business\BusinessUser;
use Illuminate\Http\Request;

interface BusinessAuthenticationInterface
{
    /**
     * Authenticate a user and create a session
     *
     * @param LoginRequest $request
     * @return BusinessUser
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(LoginRequest $request): BusinessUser;

    /**
     * Register a new business and user
     *
     * @param RegisterRequest $request
     * @return BusinessUser
     * @throws \Exception
     */
    public function register(RegisterRequest $request): BusinessUser;

    /**
     * Log out the current user
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void;

    /**
     * Auto login a business user (used for admin impersonation)
     *
     * @param BusinessUser $user
     * @return void
     */
    public function autoLogin(BusinessUser $user): void;
}
