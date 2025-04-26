<?php 

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordResetMail extends Mailable
{
    public $user;
    public $newPassword;

    public function __construct($user, $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject('Your Password Has Been Reset')
            ->view('emails.password_reset')
            ->with([
                'user_name' => $this->user->user_name,
                'new_password' => $this->newPassword
            ]);
    }
}
