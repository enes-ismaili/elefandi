<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Vendor;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProfileController extends Controller
{
    public function index()
    {
        return view('user.profile.index');
    }

    public function edit()
    {
        // TODO: Cache all Countries
        $countries = Country::all();
        return view('user.profile.editprofile', compact('countries'));
    }

    public function store(Request $request)
    {
        $validatedDate = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'country' => 'required|min:1',
            'city' => 'required|min:1',
        ], [
            'first_name.required' => 'Emri është i detyrueshëm',
            'last_name.required' => 'Mbiemri është i detyrueshëm',
            'email.required' => 'Email është i detyrueshëm',
            'email.email' => 'Email nuk është i saktë',
            'phone.required' => 'Telefoni është i detyrueshëm',
            'address.required' => 'Adresa është i detyrueshëm',
            'country.required' => 'Shteti është i detyrueshëm',
            'country.min' => 'Shteti është i detyrueshëm',
            'city.required' => 'Qyteti është i detyrueshëm',
            'city.min' => 'Qyteti është i detyrueshëm',
        ]);

        $user = User::find(current_user()->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->zipcode = $request->zipcode;
        $user->city = $request->city;
        $user->country_id = $request->country;
        $user->save();

        session()->put('success','Ndryshimet ne profilin tuaj u ruajtën me sukses.');
        return redirect()->route('profile.edit');
    }

    public function address()
    {
        return view('user.profile.address');
    }

    public function orders()
    {
        $orders = current_user()->orders;
        return view('user.orders.index', compact('orders'));
    }

    public function singleorder($id)
    {
        $order = current_user()->orders->where('id', $id)->first();
        if($order){
            return view('user.orders.single', compact('order'));
        } else {
            abort(404);
        }
    }

    public function singleordertrack($id)
    {
        $order = current_user()->orders->where('id', $id)->first();
        if($order){
            return view('user.orders.track', compact('order'));
        } else {
            abort(404);
        }
    }

    public function singleordersupport($id)
    {
        if(is_numeric($id)){
            $order = current_user()->orders->where('id', $id)->first();
            if($order){
                return view('user.orders.support', compact('order'));
            }
        }
        abort(404);
    }

    public function addordersupport(Request $request, $id)
    {
        if(is_numeric($id)){
            $order = current_user()->orders->where('id', $id)->first();
            if($order){
                $validatedDate = $request->validate([
                    'vendor' => 'required',
                    'subject_choose' => 'required',
                    'subject' => 'required_if:subject_choose,9',
                    'message' => 'required',
                ], [
                    'vendor.required' => 'Dyqani është i detyrueshëm',
                    'subject_choose.required' => 'Subjekti është i detyrueshëm',
                    'subject.required_if' => 'Subjekti është i detyrueshëm',
                    'message.required' => 'Mesazhi është i detyrueshëm',
                ]);
                $ticket = new Ticket();
                $ticket->vendor_id = $request->vendor;
                $ticket->order_id = $id;
                $ticket->user_id = current_user()->id;
                $ticket->type = $request->subject_choose;
                if($request->subject_choose == 9){
                    $ticket->subject = $request->subject;
                }
                $ticket->message = $request->message;
                $ticket->save();
                return redirect()->route('profile.ticket.index');
            }
        }
        abort(404);
    }
    
    public function tickets()
    {
        $tickets = current_user()->tickets;
        return view('user.ticket.index', compact('tickets'));
    }

    public function singletickets($id)
    {
        if(is_numeric($id)){
            $ticket = current_user()->tickets->where('id', $id)->first();
            return view('user.ticket.single', compact('ticket'));
        }
    }

    public function addsingletickets(Request $request, $id)
    {
        if(is_numeric($id)){
            $validatedDate = $request->validate([
                'message' => 'required',
            ], [
                'message.required' => 'Mesazhi është i detyrueshëm',
            ]);
            if($request->message){
                $ticket = new TicketMessage();
                $ticket->ticket_id = $id;
                $ticket->user_id = current_user()->id;
                $ticket->way = 1;
                $ticket->message = $request->message;
                $ticket->save();
                if($request->attachment && count($request->attachment)){
                    foreach($request->attachment as $key => $attachment){
                        $attachments = new TicketAttachment();
                        $attachments->ticket_id = $id;
                        $attachments->message_id = $ticket->id;
                        $attachments->file = $attachment;
                        $attachments->save();
                    }
                }
            }
            $supportTicket = Ticket::findorfail($id);
            if($supportTicket->status == 3){
                $supportTicket->status = 4;
                $supportTicket->save();
            }
            return redirect()->route('profile.ticket.single', $id);
        }
    }

    public function vendor($vendorSlug)
    {
        $vendor = Vendor::where('slug', '=', $vendorSlug)->firstOrFail();
        if($vendor->vstatus == 1){
            if(!$vendor->qrcode){
                $qrName = $vendor->id;
                $qrExtension = '.png';
                $exists = Storage::disk('local')->exists('photos/qrcodes/vendor/'.$qrName.$qrExtension);
                if ($exists) {
                    $increment = 0;
                    $this->name = $qrName.$qrExtension;
                    while(Storage::disk('local')->exists('photos/qrcodes/vendor/'.$qrName.$qrExtension)) {
                        $increment++;
                        $qrName = $qrName.'-'.$increment.$qrExtension;
                    }
                }
                $qrPath = 'photos/qrcodes/vendor/'.$qrName.$qrExtension;
                $vendorLink = route('single.vendor', [$vendor->slug]);
                // QrCode::size(150)->generate($vendorLink, $qrPath);
                QrCode::format('png')->merge('https://new57.elefandi.com/images/qr-icon.png', .2, true)->size(150)->generate($vendorLink, $qrPath);
                $vendor->qrcode = $qrName.$qrExtension;
                $vendor->save();
            }
            $vendorCategories = $vendor->products()->with('category')->get()->pluck('category')->unique('id');
            return view('user.vendor.index', compact('vendor', 'vendorCategories'));
        }
        abort(404);
    }
}
