<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class UserWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Benvenuto su ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        $verifyUrl = $this->user->hasVerifiedEmail()
            ? null
            : URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes((int) config('auth.verification.expire', 60)),
                ['id' => $this->user->getKey(), 'hash' => sha1($this->user->getEmailForVerification())]
            );

        return new Content(
            view: 'emails.users.welcome',
            with: [
                'user' => $this->user,
                'verifyUrl' => $verifyUrl,
            ],
        );
    }
}
