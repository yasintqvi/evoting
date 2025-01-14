<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\Auth\LoginDTO;
use App\Enums\UserStatus;
use App\Exceptions\Auth\UserHasBeenBlockedException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function loginWithPassword(LoginDTO $loginDTO)
    {
        $identifier_field = config('auth.identifier');

        $user = User::where($identifier_field, $loginDTO->identifier)->first();

        if (!$user || !$user->password) {
            return false;
        }

        $this->checkUserStatus($user);

        return Auth::attempt([
            $identifier_field => $loginDTO->identifier,
            'password' => $loginDTO->password
        ], $loginDTO->remember);
    }

    public function loginWithOtp(LoginDTO $loginDTO) {}

    protected function checkUserStatus(User $user)
    {
        if ($user->status === UserStatus::BLOCK) {
            throw new UserHasBeenBlockedException;
        }
    }
}
