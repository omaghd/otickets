<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordRequest;
use App\Http\Requests\Auth\ProfileRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Notifications\PasswordChanged;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected UserService $service)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $this->service->login($request->validated());

            return $this->successResponse('Login successful', $credentials);
        } catch (AuthenticationException $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->service->register($request->validated());

            return $this->successResponse('Registration successful', $user);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 409);
        }
    }

    public function updateProfile(ProfileRequest $request): JsonResponse
    {
        $user = $this->service->updateProfile($request->safe()->except('picture'));

        if ($request->hasFile('picture')) {
            $this->service->saveProfilePicture($user, $request->file('picture'));
        }

        return $this->successResponse('Profile updated successfully', $user);
    }

    public function updatePassword(PasswordRequest $request): JsonResponse
    {
        try {
            $this->service->updatePassword($request->validated());

            return $this->successResponse('Password updated successfully');
        } catch (AuthenticationException $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    public function user(): JsonResponse
    {
        return response()->json(auth()->user()->load(['department']));
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink(
            $request->only('email')
        );

        return response()->json(['message' => 'If your email is registered, you will receive a password reset link']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password'       => $request->password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                $user->notify(new PasswordChanged);
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'message' => 'Password reset failed'
            ])->status(409);
        }

        return response()->json(['message' => 'Password reset successfully']);
    }

    public function logout(): JsonResponse
    {
        $this->service->logout();

        return $this->successResponse('Logout successful');
    }
}
