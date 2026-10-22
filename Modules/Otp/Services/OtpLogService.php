<?php

namespace Modules\Otp\Services;

use Illuminate\Support\Facades\Mail;
use Modules\Otp\Contracts\OtpContract;
use Modules\Otp\Emails\OtpLogMail;

class OtpLogService implements OtpContract
{
    public function send(string $phoneNumber, string $message): bool
    {
        Mail::to('test@example.com')->send(new OtpLogMail($message));

        return true;
    }
}
