<?php

namespace App\Mail;

use App\Models\WalkInCustomer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WalkInRegistrationInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WalkInCustomer $walkInCustomer, public string $registrationUrl) {}

    public function build()
    {
        return $this->subject('Create Your Padayon Massage Center Account')->view('emails.walk-in-registration-invitation');
    }
}