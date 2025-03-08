<?php 
namespace App\Mail;

use Illuminate\Mail\Mailable;

class UserCreatedMail extends Mailable
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user; // Ensure this contains user_name and user_email
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject('Welcome to Our System!')
            ->view('emails.user_created') // Blade email template
            ->with([
                'user' => $this->user,
            ]);
    }
}
