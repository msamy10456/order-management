<?php

namespace Modules\Otp\Contracts;

interface OtpContract
{
    public function send(string $phoneNumber, string $message): bool;
}
