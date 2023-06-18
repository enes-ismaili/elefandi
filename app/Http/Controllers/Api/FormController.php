<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUs;
use App\Models\ProductReports;

class FormController extends Controller
{
    function userProfile(Request $request)
    {
        ray($request);
        $validatedDate = $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'country' => 'required',
        ]);
    }
	
	public function contactUs(Request $request)
	{
		$validatedDate = $request->validate([
            'subject' => 'required',
            'message' => 'required',
            'userid' => 'required',
        ]);
		if($request->type == 2){
			$cName = '<p>Dyqani "'.current_vendor()->name.'" ka shkruar: </p>';
		} else {
			$cName = '<p>Përdoruesi "'.current_user()->first_name.' '.current_user()->last_name.'" ka shkruar: </p>';
		}
		Mail::to('info@elefandi.com')->send(new ContactUs($cName, $request->subject, $request->message));
		return ['status'=>'success'];
	}

    public function reportProduct(Request $request)
    {
        $validatedDate = $request->validate([
            'reason' => 'required',
            'product_id' => 'required',
        ]);
        if(current_user()){
            $validatedDate = $request->validate([
                'reason' => 'required',
                'product_id' => 'required',
            ]);
            $name = current_user()->first_name .' '. current_user()->last_name;
            $email = current_user()->email;
            $userId = current_user()->id;
        } else {
            $validatedDate = $request->validate([
                'name' => 'required',
                'email' => 'required',
                'reason' => 'required',
                'user_id' => 'required',
                'product_id' => 'required',
            ]);
            $name = $request->name;
            $email = $request->email;
            $userId = $request->user_id;
        }
        if($request->user_id == 'undefined' || $request->product_id == 'undefined'){
            return ['status'=>'error'];
        }
        
        $report = new ProductReports();
        $report->product_id = $request->product_id;
        $report->name = $name;
        $report->email = $email;
        $report->reason = $request->reason;
        $report->user_id = $userId;
        $report->save();
        return ['status'=>'success'];
    }
}
