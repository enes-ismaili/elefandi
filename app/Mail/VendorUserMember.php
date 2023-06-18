<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VendorUserMember extends Mailable
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
    public function __construct($request, $vendor, $receiver)
    {
        $this->templateBase = EmailTemplate::findOrFail(1);
        $templateContents = EmailTemplate::findOrFail(10);
        $this->templateContent = $templateContents;
        $this->mailSubject = $templateContents->subject;
        if($receiver == 2){
            $this->template['content'] = $templateContents->vemail_templates;
        } else {
            $this->template['content'] = $templateContents->email_templates;
        }
        if($vendor){
            $this->template['vendorName'] = $vendor->name;
        } else {
            $this->template['vendorName'] = '';
        }
        if($request){
            $this->template['requestRole'] = $request->roles->name;
            $this->template['requestConfirm'] = route('view.register').'?stoken='.$request->token;
            $this->template['requestManageStaff'] = route('vendor.staff.index');
        } else {
            $this->template['requestRole'] = '';
            $this->template['requestConfirm'] = '#';
            $this->template['requestManageStaff'] = '#';
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
