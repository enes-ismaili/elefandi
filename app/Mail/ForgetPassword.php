<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $templateBase;
    public $templateContent;
    public $mailSubject = '';
    public $template = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $user = '')
    {
        $this->templateBase = EmailTemplate::findOrFail(1);
        $templateContents = EmailTemplate::findOrFail(5);
        $this->templateContent = $templateContents;
        $this->template['content'] = $this->templateContent->email_templates;
        $this->mailSubject = $templateContents->subject;
        if($token){
            $this->template['userConfirmEmail'] = route('password.reset', $token).'?email='.$user->email;
        } else {
            $this->template['userConfirmEmail'] = '';
        }
        if($user){
            $this->template['userFName'] = $user->first_name;
            $this->template['userLName'] = $user->last_name;
        } else {
            $this->template['userFName'] = '';
            $this->template['userLName'] = '';
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
        return $this->subject($mailSubjects)->view('emails.base')
            ->with([
                'ptemplate' => $this->templateBase->email_templates,
                'content' => $this->template,
            ]);
    }
}
