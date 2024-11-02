<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param  array  $data
     * @return User
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Login an existing user and return an auth token.
     *
     * @param  array  $data
     * @return string
     * @throws AuthenticationException
     */
    public function login(array $data): string
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken('auth_token')->plainTextToken;
    }

    public function sendResetLink(array $data): string
    {
        $status = Password::sendResetLink([
            'email' => $data['email']
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception(__($status));
        }

        return $status;
    }

    public function resetPassword(array $data): void
    {
        Password::reset($data, function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });
    }

    /**
     * Handle user logout by revoking the current token.
     *
     * @param Request $request
     * @return bool
     */
    public function logout(Request $request): bool
    {
        if ($request->user() && !$request->user()->currentAccessToken() instanceof \Laravel\Sanctum\TransientToken) {
            $request->user()->currentAccessToken()->delete();
        }

        return true;
    }
}
