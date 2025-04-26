<?php 

namespace App\Mail;

use Illuminate\Mail\Mailable;

class UserCreatedMail extends Mailable
{
    public $user_name;
    public $user_email;
    public $password;
    
    public function __construct($data)
    {
        $this->user_name = $data['user_name'];
        $this->user_email = $data['user_email'];
        $this->password = $data['user_password'];
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject('Your Account Has Been Create')
            ->view('emails.user_created_mail')
            ->with([
                'user_name' => $this->user_name,
                'user_email' => $this->user_email,
                'password' => $this->password
            ]);
    }
}
