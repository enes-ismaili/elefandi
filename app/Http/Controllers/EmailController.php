<?php

namespace App\Http\Controllers;

use App\Mail\SendMailForm;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function emails()
    {
        $emails = EmailTemplate::all();
        return view('admin.emails.index', compact('emails'));
    }

    public function template($id)
    {
        if(is_numeric($id)){
            $email = EmailTemplate::findOrFail($id);
            return view('admin.emails.edit', compact('email'));
        }
    }

    public function storeTemplate(Request $request, $id)
    {
        if(is_numeric($id)){
            $validatedDate = $request->validate([
                'subject' => 'required',
            ], [
                'subject.required' => 'Subjekti është i detyrueshëm',
            ]);
            $templateEmail = EmailTemplate::findOrFail($id);
            $templateEmail->subject = $request->subject;
            if(in_array($templateEmail->rights, [0, 1,4,6,7])) {
                $templateEmail->email_templates = $request->email_templates;
            }
            if(in_array($templateEmail->rights, [2,4,5,7])) {
                $templateEmail->vemail_templates = $request->vemail_templates;
            }
            if(in_array($templateEmail->rights, [3,5,6,7])) {
                $templateEmail->aemail_templates = $request->aemail_templates;
            }
            $templateEmail->save();
            session()->put('success','Email Template u ruajt me sukses.');
            return redirect()->route('admin.emails.index');
        }
    }

    public function send()
    {
        $countries = Country::where('shipping', 1)->get();
        return view('admin.emails.send', compact('countries'));
    }

    public function sendPost(Request $request)
    {
        $validatedDate = $request->validate([
            'subject' => 'required',
            'email_templates' => 'required',
        ], [
            'subject.required' => 'Subjekti është i detyrueshëm',
            'email_templates.required' => 'Forma Email është e detyrueshme',
        ]);
        $country = 0;
        $counts = 0;
        if($request->country){
            $country = $request->country;
        }
        
        if($request->type==1){
            if($country){
                $receivers = User::where('country_id', $country)->get();
            } else {
                $receivers = User::all();
            }
            foreach($receivers as $receiver){
                try {
                    $counts++;
                    // Mail::to($receiver->email)->send(new SendMailForm($request->email_templates, $receiver, false));
                    Mail::to($receiver->email)->send(new SendMailForm($request->email_templates, $receiver, false));
                    // if($receiver->email == 'e.dalipi@codeit.al'){
                    //     // Mail::send(new SendMailForm($request->email_templates, $receiver, false), [], function ($message) use ($receiver) {
                    //     //     $message->to($receiver->email);
                    //     // });
                    // }
                } catch(Swift_TransportException $e){
                    if($e->getMessage()) {
                       dd($e->getMessage());
                    }             
                 }
            }
        } else {
            if($country){
                $receivers = Vendor::where('country_id', $country)->get();
            } else {
                $receivers = Vendor::all();
            }
            foreach($receivers as $receiver){
                $counts++;
                Mail::to($receiver->email)->send(new SendMailForm($request->email_templates, false, $receiver));
            }
        }
        session()->put('success', $counts.' Email u dërguan me sukses');
        return redirect()->route('admin.emails.index');
    }
}
