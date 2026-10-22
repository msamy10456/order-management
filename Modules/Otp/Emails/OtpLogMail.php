<?php

namespace Modules\Otp\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpLogMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function envelope(): Envelope
    {
        return new Envelope;
    }

    public function content(): Content
    {
        return new Content(
            'otp::otp-log',
            with: [
                'value' => $this->value,
            ]
        );
    }
}
