<?php 
namespace App\Mail;

use Illuminate\Mail\Mailable;

class ArticleCreatedMail extends Mailable
{
    public $user;
    public $articleId;      
    public $articleTitle;   
    public $userName;
    public $userNickname;  
    public $userEmail;     

    public function __construct($user, $articleId, $articleTitle, $userName, $userNickname, $userEmail)
    {
        $this->user = $user;
        $this->articleId = $articleId;
        $this->articleTitle = $articleTitle;
        $this->userName = $userName;
        $this->userNickname = $userNickname;
        $this->userEmail = $userEmail;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject('New Article Has been Created')
            ->view('emails.article_create_mail')
            ->with([
                'user' => $this->user,
                'article_id' => $this->articleId, 
                'article_title' => $this->articleTitle,
                'user_name' => $this->userName,
                'user_nickname' => $this->userNickname,
                'user_email' => $this->userEmail
            ]);
    }
}