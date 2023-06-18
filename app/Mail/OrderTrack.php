<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderTrack extends Mailable
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
    public function __construct($user, $orderVendor, $tracking)
    {
        $this->templateBase = EmailTemplate::findOrFail(1);
        $this->templateContent = EmailTemplate::findOrFail(15);
        $this->template['content'] = $this->templateContent->email_templates;
        if($user){
            $this->template['userFName'] = $user->first_name;
            $this->template['userLName'] = $user->last_name;
            $this->template['userEmail'] = $user->email;
        } else {
            $this->template['userFName'] = '';
            $this->template['userLName'] = '';
            $this->template['userEmail'] = '';
        }
        if($orderVendor){
            $this->template['orderId'] = $orderVendor->order_id;
            $this->template['orderVendorName'] = $orderVendor->vendor->name;
        } else {
            $this->template['orderId'] = '';
            $this->template['orderVendorName'] = '';
        }
        if($tracking){
            $this->template['orderTrackComment'] = $tracking->comment;
            $this->template['orderTrackLink'] = route('profile.orders.track', $tracking->order_vendor_id);
        } else {
            $this->template['orderTrackComment'] = '';
            $this->template['orderTrackLink'] = '#';
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
