<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VendorRegister extends Mailable
{
    use Queueable, SerializesModels;

    public $templateBase;
    public $templateContent;
    public $mailSubject;
    public $template = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $vendor, $receiver)
    {
        $this->templateBase = EmailTemplate::findOrFail(1);
        $templateContents = EmailTemplate::findOrFail(4);
        $this->templateContent = $templateContents;
        $this->mailSubject = $templateContents->subject;
        if($receiver == 2){
            $this->template['content'] = $templateContents->aemail_templates;
        } else {
            $this->template['content'] = $templateContents->vemail_templates;
        }
        if($user){
            $this->template['userFName'] = $user->first_name;
            $this->template['userLName'] = $user->last_name;
            $this->template['userEmail'] = $user->email;
            $this->template['userConfirmEmail'] = '#';
        } else {
            $this->template['userFName'] = '';
            $this->template['userLName'] = '';
            $this->template['userEmail'] = '';
            $this->template['userEmail'] = '';
        }
        if($vendor){
            $this->template['vendorName'] = $vendor->name;
        } else {
            $this->template['vendorName'] = '';
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
        return $this->view('emails.base')
            ->with([
                'ptemplate' => $this->templateBase->email_templates,
                'content' => $this->template,
            ]);
    }
}
