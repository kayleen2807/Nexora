<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\EmailVerificationCodeNotification;
use Illuminate\Support\Facades\Hash;

class EmailVerificationService
{
    public function send(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'email_verification_code' => Hash::make($code),
            'email_verification_expires_at' => now()->addMinutes(10),
            'email_verified_at' => null,
        ])->save();

        $user->notify(new EmailVerificationCodeNotification($code));
    }
}