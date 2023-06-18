<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUs extends Mailable
{
	use Queueable, SerializesModels;
	
	public $templateBase;
    public $templateContent;
	public $template = [];
    public $mailSubject;
	
	public function __construct($cName, $subject, $message)
    {
		$this->templateBase = EmailTemplate::findOrFail(1);
		$this->template['content'] = $cName.$message;
		$this->mailSubject = $subject;
	}
	
	public function build()
    {
        return $this->subject($this->mailSubject)->view('emails.base')
            ->with([
                'ptemplate' => $this->templateBase->email_templates,
                'content' => $this->template,
            ]);
    }
}