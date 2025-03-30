<?php 

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AccountCreateMail extends Mailable
{
    public $user_name;
    public $user_email;
    public $user_password;

    public function __construct($data)
    {
        $this->user_name = $data['user_name'];
        $this->user_email = $data['user_email'];
        $this->user_password = $data['user_password'];
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject('Your Password Has Been Reset')
            ->view('emails.account_created_mail')
            ->with([
                'user_name' => $this->user_name,
                'user_email' => $this->user_email,
                'new_password' => $this->user_password
            ]);
    }
}
