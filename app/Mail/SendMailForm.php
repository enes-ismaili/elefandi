<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailForm extends Mailable
{
    use Queueable, SerializesModels;

    public $templateBase;
    public $templateContent;
    public $template = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content, $user, $vendor)
    {
        $this->templateBase = EmailTemplate::findOrFail(1);
        $this->templateContent = $content;
        $this->template['content'] = $content;
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
            $this->template['vendorLogo'] = '';
        } else {
            $this->template['vendorName'] = '';
            $this->template['vendorLogo'] = '';
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.base')
            ->with([
                'ptemplate' => $this->templateBase->email_templates,
                'content' => $this->template,
            ]);
    }
}
