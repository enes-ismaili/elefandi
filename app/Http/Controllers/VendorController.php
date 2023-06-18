<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Country;
use App\Models\WorkHour;
use App\Models\Transport;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Models\VendorNameRequest;

class VendorController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d 00:00:05');
        $thisMonth = date('Y-m-01 00:00:05');
        $ordersToday = current_vendor()->orders->where('created_at', '>', $today)->count();
        $ordersTodaySum = current_vendor()->orders->where('created_at', '>', $today)->sum('value');
        $ordersMonth = current_vendor()->orders->where('created_at', '>', $thisMonth)->count();
        $ordersMonthSum = current_vendor()->orders->where('created_at', '>', $thisMonth)->sum('value');
        $pOrders = current_vendor()->orders->where('status', '=', 0)->count();
        $cOrders = current_vendor()->orders->where('status', '=', 1)->count();
        $caOrders = current_vendor()->orders->where('status', '=', 2)->count();
        return view('admin.base.vhome', compact('pOrders', 'cOrders', 'caOrders', 'ordersToday', 'ordersMonth', 'ordersTodaySum', 'ordersMonthSum'));
    }

    public function editprofile()
    {
        if(check_permissions('manage_vendor')) {
            $vendor = current_vendor();
            $countries = Country::all();
            $shippingCountry = Country::where('shipping', '1')->get();
            return view('admin.vendors.edit-profile', compact('vendor', 'countries', 'shippingCountry'));
        }
        abort(404);
    }

    public function store(Request $request)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'email' => 'required|email',
                'description' => 'required',
                'address' => 'required',
                'city' => 'required|min:1',
                'country' => 'required|min:1',
                'phone' => 'required',
                'trans' => 'array|size:3',
                'time.*' => 'required',
                'logo_path' => 'required',
                'cover_path' => 'required',
            ], [
                'email.required' => 'Email është i detyrueshëm',
                'email.email' => 'Email është nuk është shkruar saktë',
                'description.required' => 'Përshkrimi është i detyrueshëm',
                'address.required' => 'Adresa është i detyrueshëm',
                'country.required' => 'Shteti është i detyrueshëm',
                'country.min' => 'Shteti është i detyrueshëm',
                'city.required' => 'Qyteti është i detyrueshëm',
                'city.min' => 'Qyteti është i detyrueshëm',
                'phone.required' => 'Telefoni është i detyrueshëm',
                'trans.required' => 'Transporti është i detyrueshëm',
                'time.*.required' => 'Oraret janë të detyrueshëm',
                'logo_path.required' => 'Logo e Dyqanit është e detyrueshme',
                'cover_path.required' => 'Cover i Dyqanit është i detyrueshëm',
            ]);
            // dd($request);
            $currVendor = current_vendor();
            $vendor = current_vendor();
            $vendor->slug = null;
            $vendor->description = $request->description;
            $vendor->address = $request->address;
            $vendor->city = $request->city;
            $vendor->country_id = $request->country;
            $vendor->zipcode = $request->zipcode;
            $vendor->phone = $request->phone;
            $vendor->email = $request->email;
            $vendor->logo_path = $request->logo_path;
            $vendor->cover_path = $request->cover_path;
            foreach ($request->trans as $key=>$transport) {
                if(count($transport) > 1){
                    $transportType = 4;
                } else {
                    $transportType = $transport[0];
                }
                if($request->transCost[$key][0]){
                    $transCost = $request->transCost[$key][0];
                } else {
                    $transCost = 0;
                }
                $shipping = Transport::updateOrCreate(
                    ['vendor_id' => $currVendor->id, 'country_id'=> $key],
                    ['transport' => $transportType, 'limit' => $request->transLimit[$key], 'cost'=> $request->transCost[$key][0], 'transtime' => $request->transTime[$key][0]]
                );
            }
            $vendor->socials()->delete();
            foreach ($request->socials as $key=>$social) {
                if($social){
                    if(in_array($key, ['facebook', 'twitter', 'instagram', 'youtube'])){
                        $socials = SocialLink::updateOrCreate(
                            ['vendor_id' => $currVendor->id, 'name'=> $key],
                            ['links' => $social]
                        );
                    }
                }
            }
            $workHours = $request->time;
            $workHour = WorkHour::updateOrCreate(
                ['vendor_id' => $currVendor->id],
                [
                    'monday' => $workHours['monday'], 'monday_start' => $workHours['monday_start'], 'monday_end'=> $workHours['monday_end'],
                    'tuesday' => $workHours['tuesday'], 'tuesday_start' => $workHours['tuesday_start'], 'tuesday_end'=> $workHours['tuesday_end'],
                    'wednesday' => $workHours['wednesday'], 'wednesday_start' => $workHours['wednesday_start'], 'wednesday_end'=> $workHours['wednesday_end'],
                    'thursday' => $workHours['thursday'], 'thursday_start' => $workHours['thursday_start'], 'thursday_end'=> $workHours['thursday_end'],
                    'friday' => $workHours['friday'], 'friday_start' => $workHours['friday_start'], 'friday_end'=> $workHours['friday_end'],
                    'saturday' => $workHours['saturday'], 'saturday_start' => $workHours['saturday_start'], 'saturday_end'=> $workHours['saturday_end'],
                    'sunday' => $workHours['sunday'], 'sunday_start' => $workHours['sunday_start'], 'sunday_end'=> $workHours['sunday_end'],
                ]
            );
            $vendor->save();
            session()->put('success','Profili i Dyqanit u ruajt me sukses.');
            return redirect()->route('vendor.edit.profile');
        }
        abort(404);
    }

    public function storenamechange(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'newname' => 'required_unless:removeRequest,1',
                'newdescription' => 'required_unless:removeRequest,1',
            ], [
                'newname.required_unless' => 'Emri i ri i Dyqanit është i detyrueshëm',
                'newdescription.required_unless' => 'Arsyeja e ndryshimit të emrit është e detyrueshme',
            ]);
            $currVendor = current_vendor();
            if($request->removeRequest){
                $nameRequest = VendorNameRequest::where([['vendor_id', '=', $id], ['udelete', '=', 0]])->first();
                if($nameRequest){
                    $nameRequest->udelete = 1;
                    $nameRequest->save();
                    session()->put('success','Kërkesa për ndryshim emri u tërhoq me sukses.');
                }
                return redirect()->route('vendor.edit.profile');
            } else {
                $nameRequest = new VendorNameRequest();
                $nameRequest->vendor_id = $currVendor->id;
                $nameRequest->name = $request->newname;
                $nameRequest->description = $request->newdescription;
                $nameRequest->save();
                session()->put('success','Kërkesa për ndryshim emri u dërgua me sukses.');
                return redirect()->route('vendor.edit.profile');
            }
        }
        abort(404);
    }

    public function tickets()
    {
        if(check_permissions('manage_supports')){
            $tickets = current_vendor()->tickets;
            return view('admin.tickets.vindex', compact('tickets'));
        }
        abort(404);
    }

    public function singletickets($id)
    {
        if(check_permissions('manage_supports') && is_numeric($id)){
            $ticket = current_vendor()->tickets->where('id', $id)->first();
            return view('admin.tickets.vsingle', compact('ticket'));
        }
        abort(404);
    }

    public function addsingletickets(Request $request, $id)
    {
        if(check_permissions('manage_supports') && is_numeric($id)){
            $validatedDate = $request->validate([
                'message' => 'required',
            ], [
                'message.required' => 'Mesazhi është i detyrueshëm',
            ]);
            if($request->message){
                $ticket = new TicketMessage();
                $ticket->ticket_id = $id;
                $ticket->user_id = current_vendor()->id;
                $ticket->way = 2;
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
            if($supportTicket->status < 3 && $request->close) {
                $supportTicket->status = 3;
                $supportTicket->save();
            }
            session()->put('success','Mesazhi u shtua me sukses.');
            return redirect()->route('vendor.ticket.single', $id);
        }
        abort(404);
    }

    public function membership()
    {
        if(check_permissions('manage_vendor')){
            $vendor = current_vendor();
            return view('admin.vendors.membership.vindex', compact('vendor'));
        }
        abort(404);
    }
}
