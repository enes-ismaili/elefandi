<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewOrder extends Mailable
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
    public function __construct($user, $order, $vendOrder)
    {
        $this->templateBase = EmailTemplate::findOrFail(1);
        $this->templateContent = EmailTemplate::findOrFail(14);
        if($vendOrder){
            $this->template['content'] = $this->templateContent->vemail_templates;
        } else {
            $this->template['content'] = $this->templateContent->email_templates;
        }
        if($user){
            $this->template['userFName'] = $user->first_name;
            $this->template['userLName'] = $user->last_name;
            $this->template['userEmail'] = $user->email;
        } else {
            $this->template['userFName'] = '';
            $this->template['userLName'] = '';
            $this->template['userEmail'] = '';
        }
        if($order){
            $this->template['orderId'] = $order->id;
            $this->template['orderVendorName'] = ($vendOrder) ? $vendOrder->vendor->name : '';
            $orderHtml = '';
            if(!$vendOrder){
                $orderHtml .= '<h4>Detajet e porosisë</h4>';
                foreach($order->ordervendor as $vendor){
                    $orderHtml .= '<div style="font-size: 14px;padding: 14px;box-shadow: 1px 2px 10px #22222221;border-radius: 5px;margin-bottom: 15px;">';
                        $orderHtml .= '<div style="padding-bottom: 10px;border-bottom: 1px solid #ddd;display: inline-block;width: 100%;">';
                            $orderHtml .= '<div style="float: left;padding-left: 10px;font-weight: 600;">'.$vendor->vendor->name.'</div>';
                            $orderHtml .= '<div style="float: right;font-size: 14px;padding-right: 10px;"><div style="display: inline-block;">Transport: <span>'.($vendor->transport*1).'€</div><div style="display: inline-block;margin-left: 25px;">Gjithsej: <span>'.($vendor->value*1).'€</span></div></div>';
                        $orderHtml .= '</div>';
                        $orderHtml .= '<table style="width: 100%;font-size: 14px;">';
                            $orderHtml .= '<thead>';
                                $orderHtml .= '<tr>';
                                    $orderHtml .= '<th style="font-weight: 300;text-align: left;padding: 10px;">Produkte</th>';
                                    $orderHtml .= '<th style="font-weight: 300;padding: 10px;width: 100px;">Sasia</th>';
                                    $orderHtml .= '<th style="font-weight: 300;padding: 10px;width: 100px;">Shuma</th>';
                                $orderHtml .= '</tr>';
                            $orderHtml .= '</thead>';
                            $orderHtml .= '<tbody>';
                                foreach($vendor->details as $product) {
                                    $orderHtml .= '<tr>';
                                        $orderHtml .= '<td style="text-align: left;padding: 10px;border-top: 1px solid #dee2e6;">'.$product->products->name.'</td>';
                                        $orderHtml .= '<td style="text-align: center;padding: 10px;width: 100px;border-top: 1px solid #dee2e6;">'.$product->qty.'</td>';
                                        $orderHtml .= '<td style="text-align: center;padding: 10px;width: 100px;border-top: 1px solid #dee2e6;">'.($product->price * 1).'</td>';
                                    $orderHtml .= '</tr>';
                                }
                            $orderHtml .= '</tbody>';
                        $orderHtml .= '</table>';
                    $orderHtml .= '</div>';
                }
                $orderHtml .= '<div style="margin-bottom: 30px;background-color: #f1f1f1;border: 1px solid #bfbfbf;font-size: 16px;width: 100%;box-shadow: 1px 2px 10px #22222221;border-radius: 5px;">';
                    $orderHtml .= '<div style="padding: 10px 15px;">';
                        $orderHtml .= '<div style="display: inline-block;width: 100%;margin-bottom: 8px;">';
                            $orderHtml .= '<div style="width: 50%;float: left;">Nëntotal:</div>';
                            $orderHtml .= '<div style="width: 50%;float: left;text-align: right;">'.($order->value * 1).'€</div>';
                        $orderHtml .= '</div>';
                        $orderHtml .= '<div style="display: inline-block;width: 100%;margin-bottom: 8px;">';
                            $orderHtml .= '<div style="width: 50%;float: left;">Shpenzimet e dërgesës:</div>';
                            $orderHtml .= '<div style="width: 50%;float: left;text-align: right;">'.($order->transport * 1).'€</div>';
                        $orderHtml .= '</div>';
                        $orderHtml .= '<div style="display: inline-block;width: 100%;margin-bottom: 8px;">';
                            $orderHtml .= '<div style="width: 50%;float: left;">Metoda e pagesës:</div>';
                            $orderHtml .= '<div style="width: 50%;float: left;text-align: right;">Para në dorë</div>';
                        $orderHtml .= '</div>';
                        $orderHtml .= '<div style="display: inline-block;width: 100%;font-weight:700;">';
                            $orderHtml .= '<div style="width: 50%;float: left;">Gjithsej:</div>';
                            $orderHtml .= '<div style="width: 50%;float: left;text-align: right;color: #ff0000;">'.(($order->value + $order->transport)*1).'€</div>';
                        $orderHtml .= '</div>';
                    $orderHtml .= '</div>';
                $orderHtml .= '</div>';
                
                $orderHtml .= '<div style="font-size: 14px;padding: 14px;box-shadow: 1px 2px 10px #22222221;border-radius: 5px;margin-bottom: 15px;">';
                    $orderHtml .= '<div style="display:inline-block;width:100%;">';
                            $orderHtml .= '<div style="width:50%;float:left;">';
                                $orderHtml .= '<h3>Adresa e porosisë</h3>';
                                $orderHtml .= '<div>'.$order->user->first_name.' '.$order->user->last_name.'</div>';
                                $orderHtml .= '<div>'.$order->user->address.'</div>';
                                $orderHtml .= '<div>'.$order->user->zipcode.', '.((is_numeric($order->user->city) && $order->user->country_id < 4)?$order->user->cities->name : $order->user->city).'</div>';
                                $orderHtml .= '<div>'.$order->user->country()->name.'</div>';
                                $orderHtml .= '<div>'.$order->user->phone.'</div>';
                                $orderHtml .= '<div>'.$order->user->email.'</div>';
                            $orderHtml .= '</div>';
                            
                            $orderHtml .= '<div style="width:50%;float:left;">';
                                $orderHtml .= '<h3>Adresa e dërgesës</h3>';
                                $orderHtml .= '<div>'.$order->address->name.'</div>';
                                $orderHtml .= '<div>'.$order->address->address.'</div>';
                                if($order->address->address2){$orderHtml .= '<div>'.$order->address->address2.'</div>';}
                                $orderHtml .= '<div>'.$order->address->zipcode.', '.((is_numeric($order->address->city) && $order->address->country_id < 4) ? $order->address->cityF->name : $order->address->city).'</div>';
                                $orderHtml .= '<div>'.$order->address->country->name.'</div>';
                                $orderHtml .= '<div>'.$order->address->phone.'</div>';
                            $orderHtml .= '</div>';

                    $orderHtml .= '</div>';
                $orderHtml .= '</div>';
            }
            $this->template['orderDetails'] = $orderHtml;

            $orderVHtml = '';
            if($vendOrder){
                $orderVHtml .= '<h4>Detajet e porosisë</h4>';
                $orderVHtml .= '<div style="font-size: 14px;box-shadow: 1px 2px 10px #22222221;border-radius: 5px;margin-bottom: 15px;">';
                    $orderVHtml .= '<table style="width: 100%;font-size: 14px;">';
                        $orderVHtml .= '<thead>';
                            $orderVHtml .= '<tr>';
                                $orderVHtml .= '<th style="font-weight: 300;text-align: left;padding: 10px;">Produkte</th>';
                                $orderVHtml .= '<th style="font-weight: 300;padding: 10px;width: 100px;">Sasia</th>';
                                $orderVHtml .= '<th style="font-weight: 300;padding: 10px;width: 100px;">Shuma</th>';
                            $orderVHtml .= '</tr>';
                        $orderVHtml .= '</thead>';
                        $orderVHtml .= '<tbody>';
                            foreach($vendOrder->details as $product) {
                                $orderVHtml .= '<tr>';
                                    $orderVHtml .= '<td style="text-align: left;padding: 10px;border-top: 1px solid #dee2e6;">'.$product->products->name.'</td>';
                                    $orderVHtml .= '<td style="text-align: center;padding: 10px;width: 100px;border-top: 1px solid #dee2e6;">'.$product->qty.'</td>';
                                    $orderVHtml .= '<td style="text-align: center;padding: 10px;width: 100px;border-top: 1px solid #dee2e6;">'.($product->price * 1).'</td>';
                                $orderVHtml .= '</tr>';
                            }
                        $orderVHtml .= '</tbody>';
                    $orderVHtml .= '</table>';
                $orderVHtml .= '</div>';
                $orderVHtml .= '<div style="margin-bottom: 30px;background-color: #f1f1f1;border: 1px solid #bfbfbf;font-size: 16px;width: 100%;box-shadow: 1px 2px 10px #22222221;border-radius: 5px;">';
                    $orderVHtml .= '<div style="padding: 10px 15px;">';
                        $orderVHtml .= '<div style="display: inline-block;width: 100%;margin-bottom: 8px;">';
                            $orderVHtml .= '<div style="width: 50%;float: left;">Nëntotal:</div>';
                            $orderVHtml .= '<div style="width: 50%;float: left;text-align: right;">'.($vendOrder->value * 1).'€</div>';
                        $orderVHtml .= '</div>';
                        $orderVHtml .= '<div style="display: inline-block;width: 100%;margin-bottom: 8px;">';
                            $orderVHtml .= '<div style="width: 50%;float: left;">Shpenzimet e dërgesës:</div>';
                            $orderVHtml .= '<div style="width: 50%;float: left;text-align: right;">'.($vendOrder->transport * 1).'€</div>';
                        $orderVHtml .= '</div>';
                        $orderVHtml .= '<div style="display: inline-block;width: 100%;margin-bottom: 8px;">';
                            $orderVHtml .= '<div style="width: 50%;float: left;">Metoda e pagesës:</div>';
                            $orderVHtml .= '<div style="width: 50%;float: left;text-align: right;">Para në dorë</div>';
                        $orderVHtml .= '</div>';
                        $orderVHtml .= '<div style="display: inline-block;width: 100%;font-weight:700;">';
                            $orderVHtml .= '<div style="width: 50%;float: left;">Gjithsej:</div>';
                            $orderVHtml .= '<div style="width: 50%;float: left;text-align: right;color: #ff0000;">'.(($vendOrder->value + $vendOrder->transport)*1).'€</div>';
                        $orderVHtml .= '</div>';
                    $orderVHtml .= '</div>';
                $orderVHtml .= '</div>';
                
                $orderVHtml .= '<div style="font-size: 14px;padding: 14px;box-shadow: 1px 2px 10px #22222221;border-radius: 5px;margin-bottom: 15px;">';
                    $orderVHtml .= '<div style="display:inline-block;width:100%;">';
                        $orderVHtml .= '<div style="width:50%;float:left;">';
                            $orderVHtml .= '<h3>Adresa e porosisë</h3>';
                            $orderVHtml .= '<div>'.$order->user->first_name.' '.$order->user->last_name.'</div>';
                            $orderVHtml .= '<div>'.$order->user->address.'</div>';
                            $orderVHtml .= '<div>'.$order->user->zipcode.', '.((is_numeric($order->user->city) && $order->user->country_id < 4)?$order->user->cities->name : $order->user->city).'</div>';
                            $orderVHtml .= '<div>'.$order->user->country()->name.'</div>';
                            $orderVHtml .= '<div>'.$order->user->phone.'</div>';
                            $orderVHtml .= '<div>'.$order->user->email.'</div>';
                        $orderVHtml .= '</div>';
                        
                        $orderVHtml .= '<div style="width:50%;float:left;">';
                            $orderVHtml .= '<h3>Adresa e dërgesës</h3>';
                            $orderVHtml .= '<div>'.$order->address->name.'</div>';
                            $orderVHtml .= '<div>'.$order->address->address.'</div>';
                            if($order->address->address2){$orderVHtml .= '<div>'.$order->address->address2.'</div>';}
                            $orderVHtml .= '<div>'.$order->address->zipcode.', '.((is_numeric($order->address->city) && $order->address->country_id < 4) ? $order->address->cityF->name : $order->address->city).'</div>';
                            $orderVHtml .= '<div>'.$order->address->country->name.'</div>';
                            $orderVHtml .= '<div>'.$order->address->phone.'</div>';
                        $orderVHtml .= '</div>';
                    $orderVHtml .= '</div>';
                $orderVHtml .= '</div>';
            }
            $this->template['orderVDetails'] = $orderVHtml;
        } else {
            $this->template['orderId'] = '';
            $this->template['orderVendorName'] = '';
            $this->template['orderDetails'] = '';
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
