<?php

namespace App\Mail;

use App\Models\Shared\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonationThankYou extends Mailable
{
    use Queueable, SerializesModels;

    public Donation $donation;

    public function __construct(Donation $donation)
    {
        $this->donation = $donation;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name', 'PawMatch');

        return $this->from($fromAddress, $fromName)
            ->subject('Thank you for your donation to PawMatch')
            ->view('emails.donation-thank-you')
            ->text('emails.donation-thank-you-plain')
            ->with([
                'donation' => $this->donation,
            ]);
    }
}
