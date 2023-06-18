<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegisterOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $templateBase;
    public $templateContent;
    public $mailSubject;
    public $template = [];
    public $recipients;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password, $url, $recipients)
    {
        $this->templateBase = EmailTemplate::findOrFail(1);
        $templateContents = EmailTemplate::findOrFail(16);
        $this->templateContent = $templateContents;
        $this->mailSubject = $templateContents->subject;
        $this->recipients = $recipients;
        $this->template['content'] = $templateContents->email_templates;
        if($user){
            $this->template['userFName'] = $user->first_name;
            $this->template['userLName'] = $user->last_name;
            $this->template['userEmail'] = $user->email;
            $this->template['userConfirmEmail'] = '#';
        } else {
            $this->template['userFName'] = '';
            $this->template['userLName'] = '';
            $this->template['userEmail'] = '';
        }
        if($password){
            $this->template['userPassword'] = $password;
        } else {
            $this->template['userPassword'] = '';
        }
        if($url){
            $this->template['userConfirmEmail'] = $url;
        } else {
            $this->template['userConfirmEmail'] = '';
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mailSubjects = $this->mailSubject;
        $recipients = $this->recipients;
        return $this->to($recipients)->subject($mailSubjects)->view('emails.base')
            ->with([
                'ptemplate' => $this->templateBase->email_templates,
                'content' => $this->template,
            ]);
    }
}
