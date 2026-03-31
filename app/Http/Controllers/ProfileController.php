<?php

namespace App\Http\Controllers;

use App\Enums\TwoFactorType;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\Image\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Log;
use PragmaRX\Google2FAQRCode\Google2FA;

class ProfileController extends Controller
{
    public function __construct(protected ImageService $imageService)
    {
    }

    public function show(): View
    {
        return view('app.profile.show');
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = user();

        try {
            $validated = $request->validated();

            if ($request->hasFile('avatar')) {
                $validated['avatar'] = $this->imageService
                    ->setImage($request->file('avatar'))
                    ->setExclusiveDirectory('images/profiles')
                    ->save();
            }

            $user->update($validated);

            Log::info('User profile updated', [
                'user_id' => $user->id,
                'performed_by' => $user->id,
            ]);

            return back()->with('success', __('messages.profile_updated'));

        } catch (\Throwable $th) {
            Log::error('Error updating user profile', [
                'user_id' => $user->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.profile_update_error'));
        }
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        try {
            $hashedPassword = user()->password;

            if (!password_verify($request->validated('current_password'), $hashedPassword)) {
                return back()->withErrors([
                    'current_password' => __('messages.current_password_invalid'),
                ]);
            }

            return back()->with('password_changed', __('messages.password_changed'));
        } catch (\Throwable $th) {
            Log::error('Error changing user password', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.password_change_error'));
        }
    }

    public function enableGoogle2FA(Request $request)
    {
        try {
            $google2fa = new Google2FA;

            $secretKey = $google2fa->generateSecretKey();

            $user = user();
            $user->google2fa_secret = $secretKey;
            $user->save();

            $QRImage = $google2fa->getQRCodeInline(
                config('app.name'),
                $user->phone,
                $secretKey
            );

            return back()->with('google_2fa_verify', [
                'qr_image' => $QRImage,
                'secret_key' => $secretKey,
            ]);
        } catch (\Throwable $th) {
            Log::error('Error enabling Google 2FA', [
                'user_id' => user()->id ?? null,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.google2fa.enable_error'));
        }
    }

    public function verifyGoogle2FA(Request $request)
    {
        try {
            $google2fa = new Google2FA;

            $user = user();

            $valid = $google2fa->verifyKey($user->google2fa_secret, $request->input('otp'));

            if (!$valid) {
                return back()->withErrors(['otp' => __('messages.invalid_otp')]);
            }

            $user->update([
                'two_factor_type' => TwoFactorType::GOOGLE_AUTHENTICATOR,
            ]);

            return back()->with('google2fa_verified', __('messages.google2fa_verified'));
        } catch (\Throwable $th) {
            Log::error('Error verifying Google 2FA', [
                'user_id' => user()->id ?? null,
                'otp' => $request->input('otp'),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.google2fa.verify_error'));
        }
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return to_route('login');
    }
}
