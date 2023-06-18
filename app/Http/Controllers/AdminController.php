<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Vendor;
use App\Models\Country;
use App\Models\MembershipInvoice;
use App\Models\Product;
use App\Models\UserRole;
use App\Models\WorkHour;
use App\Models\Transport;
use App\Models\SocialLink;
use App\Models\VendorRole;
use App\Models\OrderVendor;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use App\Models\VendorRequest;
use App\Models\TicketAttachment;
use App\Models\VendorMembership;
use App\Models\VendorNameRequest;

class AdminController extends Controller
{
    public function home()
    {
        $today = date('Y-m-d 00:00:05');
        $thisMonth = date('Y-m-01 00:00:05');
        $ordersToday = Order::where('created_at', '>', $today)->count();
        $ordersTodaySum = Order::where('created_at', '>', $today)->sum('value');
        $ordersMonth = Order::where('created_at', '>', $thisMonth)->count();
        $userNewMonth = User::where('created_at', '>', $thisMonth)->count();
        $pOrders = Order::where('status', '=', 0)->count();
        $cOrders = Order::where('status', '=', 1)->count();
        $caOrders = Order::where('status', '=', 2)->count();
        $productsC = Product::count();
        $vendorC = Vendor::count();
        $userC = User::count();
        return view('admin.base.home', compact('pOrders', 'cOrders', 'caOrders', 'productsC', 'vendorC', 'userC', 'ordersToday', 'ordersTodaySum', 'ordersMonth', 'userNewMonth'));
    }

    public function users()
    {
        if(check_permissions('manage_users')){
            $users = User::where('status', '=', 1)->orderBy('created_at', 'DESC')->get();
            return view('admin.users.index', compact('users'));
        }
        abort(404);
    }

    public function singleUser($id)
    {
        if(check_permissions('manage_users') && is_numeric($id)){
            $user = User::findorfail($id);
            return view('admin.users.single', compact('user'));
        }
        abort(404);
    }

    public function singleUserDelete($id)
    {
        if(check_permissions('manage_users') && is_numeric($id) && check_permissions('delete_rights')){
            $user = User::findorfail($id);
            $user->status = 0;
            $user->email = 'old '.$user->email;
            $user->save();
            session()->put('success','Përdoruesi me emër '.$user->first_name.' '.$user->last_name.' u fshi me sukses.');
            return redirect()->route('admin.users.index');
        }
        abort(404);
    }

    public function vendors()
    {
        if(check_permissions('manage_vendors')){
            $vendors = Vendor::where('dshow', '=', 1)->orderBy('created_at', 'DESC')->get();
            return view('admin.vendors.index', compact('vendors'));
        }
        abort(404);
    }

    public function membership($id)
    {
        if(check_permissions('manage_vendors') && is_numeric($id)){
            $memberships = VendorMembership::where('vendor_id', $id)->orderBy('created_at', 'DESC')->get();
            $membershipsInvoice = MembershipInvoice::where('vendor_id', $id)->orderBy('created_at', 'DESC')->get();
            $vid = $id;
            return view('admin.vendors.membership.index', compact('memberships', 'membershipsInvoice', 'vid'));
        }
        abort(404);
    }

    public function membershipadd($id)
    {
        if(check_permissions('manage_vendors') && is_numeric($id)){
            $vid = $id;
            return view('admin.vendors.membership.add', compact('vid'));
        }
        abort(404);
    }

    public function membershipstore(Request $request, $id)
    {
        if(check_permissions('manage_vendors') && is_numeric($id)){
            $minDate = '2020-01-01';
            $request->merge([
                'before_date' => $minDate
            ]);
            $validatedDate = $request->validate([
                'start_date' => 'required|date|after:before_date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'start_date.required' => 'Data e Fillimit është e detyrueshme',
                'start_date.date' => 'Data e Fillimit duhet të jetë në formatin datë',
                'start_date.after' => 'Data e Fillimit duhet të jetë me e madhe se data 1 Janar 2020',
                'end_date.required' => 'Data e Mbarimit është e detyrueshme',
                'end_date.date' => 'Data e Mbarimit duhet të jetë në formatin datë',
                'end_date.after' => 'Data e Mbarimit duhet të jetë me e madhe se data e fillimit',
            ]);
            $membership = new VendorMembership();
            $membership->vendor_id = $id;
            if($request->amount){
                $membership->amount = $request->amount;
            } else {
                $membership->amount = 0;
            }
            if($request->type == 2){
                $membership->type = 2;
                $membership->end_date = '2035-01-01 23:59:58';
                $membership->paid = 1;
            } else {
                $membership->type = 1;
                $membership->end_date = $request->end_date.' 23:59:58';
                $membership->paid = 0;
            }
            $membership->start_date = $request->start_date.' 00:00:05';
            $membership->description = $request->description;
            if($request->active == 1){
                $membership->active = $request->active;
            } else {
                $membership->active = 0;
            }
            $membership->save();
            $vendor = Vendor::findOrFail($id);
            if($vendor->amembership->count()){
                if($vendor->vstatus == 2){
                    $vendor->vstatus = 1;
                    $vendor->save();
                    $vendor->products()->where('vstatus', '!=', 1)->update(['vstatus'=>1]);
                    session()->put('info','Statusi i dyqanit kaloi në Aktiv.');
                }
            } else {
                if($vendor->vstatus == 1){
                    $vendor->vstatus = 2;
                    $vendor->save();
                    $vendor->products()->where('vstatus', '=', 1)->update(['vstatus'=>2]);
                    session()->put('info','Statusi i dyqanit kaloi në Papaguar.');
                }
            }
            session()->put('success','Membership për dyqanin u shtua me sukses.');
            return redirect()->route('admin.vendors.membership.index', $id);
        }
        abort(404);
    }

    public function membershipedit($id, $mid)
    {
        if(check_permissions('manage_vendors') && is_numeric($id)&& is_numeric($mid)){
            $membership = VendorMembership::findOrFail($mid);
            if($membership->vendor_id == $id){
                $vid = $id;
                return view('admin.vendors.membership.edit', compact('membership', 'vid', 'mid'));
            }
        }
        abort(404);
    }

    public function membershipupdate(Request $request, $id, $mid)
    {
        $minDate = '2020-01-01';
        $request->merge([
            'before_date' => $minDate
        ]);
        if(check_permissions('manage_vendors') && is_numeric($id) && is_numeric($mid)){
            $validatedDate = $request->validate([
                'start_date' => 'required|date|after:before_date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'start_date.required' => 'Data e Fillimit është e detyrueshëm',
                'start_date.date' => 'Data e Fillimit duhet të jetë në formatin datë',
                'start_date.after' => 'Data e Fillimit duhet të jetë me e madhe se data 1 Janar 2020',
                'end_date.required' => 'Data e Mbarimit është e detyrueshëm',
                'end_date.date' => 'Data e Mbarimit duhet të jetë në formatin datë',
                'end_date.after_or_equal' => 'Data e Mbarimit duhet të jetë me e madhe se data e fillimit',
            ]);
            $membership = VendorMembership::findOrFail($mid);
            if($membership->vendor_id == $id){
                if($request->amount){
                    $membership->amount = $request->amount;
                } else {
                    $membership->amount = 0;
                }
                if($request->type == 2){
                    $membership->type = 2;
                    $membership->end_date = '2035-01-01 23:59:58';
                    $membership->paid = 1;
                } else {
                    $membership->type = 1;
                    $membership->end_date = $request->end_date.' 23:59:58';
                    $membership->paid = 0;
                }
                $membership->start_date = $request->start_date.' 00:00:05';
                $membership->description = $request->description;
                if($request->active == 1){
                    $membership->active = $request->active;
                } else {
                    $membership->active = 0;
                }
                $membership->save();
                $vendor = Vendor::findOrFail($id);
                if($vendor->amembership->count()){
                    if($vendor->vstatus == 2){
                        $vendor->vstatus = 1;
                        $vendor->save();
                        $vendor->products()->where('vstatus', '!=', 1)->update(['vstatus'=>1]);
                        session()->put('info','Statusi i dyqanit kaloi në Aktiv.');
                    }
                } else {
                    if($vendor->vstatus == 1){
                        $vendor->vstatus = 2;
                        $vendor->save();
                        $vendor->products()->where('vstatus', '=', 1)->update(['vstatus'=>2]);
                        session()->put('info','Statusi i dyqanit kaloi në Papaguar.');
                    }
                }
                session()->put('success','Membership për dyqanin u ndryshua me sukses.');
                return redirect()->route('admin.vendors.membership.index', $id);
            }
        }
        abort(404);
    }

    public function membershipdelete(Request $request, $id, $mid)
    {
        if(check_permissions('manage_vendors') && check_permissions('delete_rights') && is_numeric($id) && is_numeric($mid)){
            $membership = VendorMembership::findOrFail($mid);
            if($membership->vendor_id == $id){
                $membership->delete();
                $vendor = Vendor::findOrFail($id);
                if($vendor->amembership->count()){
                    if($vendor->vstatus == 2){
                        $vendor->vstatus = 1;
                        $vendor->save();
                        $vendor->products()->where('vstatus', '!=', 1)->update(['vstatus'=>1]);
                        session()->put('info','Statusi i dyqanit kaloi në Aktiv.');
                    }
                } else {
                    if($vendor->vstatus == 1){
                        $vendor->vstatus = 2;
                        $vendor->save();
                        $vendor->products()->where('vstatus', '=', 1)->update(['vstatus'=>2]);
                        session()->put('info','Statusi i dyqanit kaloi në Papaguar.');
                    }
                }
                session()->put('success','Membership për dyqanin u fshi me sukses.');
                return redirect()->route('admin.vendors.membership.index', $id);
            }
        }
        abort(404);
    }

    public function membershipinvoice()
    {
        if(check_permissions('manage_vendors')){
            $invoicesUnpaid = MembershipInvoice::where('paid', '=', 0)->orderBy('id', 'DESC')->get();
            $invoicesPaid = MembershipInvoice::where('paid', '=', 1)->orderBy('id', 'DESC')->get();
            return view('admin.vendors.membership.invoices', compact('invoicesUnpaid', 'invoicesPaid'));
        }
        abort(404);
    }

    public function membershipinvoiceedit($id)
    {
        if(check_permissions('manage_vendors') && is_numeric($id)){
            $invoice = MembershipInvoice::findOrFail($id);
            return view('admin.vendors.membership.invoice-edit', compact('invoice'));
        }
        abort(404);
    }

    public function membershipinvoiceupdate(Request $request, $id)
    {
        if(check_permissions('manage_vendors') && is_numeric($id)){
            $invoice = MembershipInvoice::findOrFail($id);
            $invoice->paid = $request->paid;
            $invoice->save();
            session()->put('success','Fatura u ndryshua me sukses.');
            return redirect()->route('admin.vendors.membership.invoice');
        }
        abort(404);
    }

    public function vendorsRequest()
    {
        if(check_permissions('manage_vendors')){
            $vendors = VendorRequest::where('status', '=', 1)->orderBy('created_at', 'DESC')->get();
            return view('admin.vendors.requests.index', compact('vendors'));
        }
        abort(404);
    }

    public function vendorsRequestedit($id)
    {
        if(check_permissions('manage_vendors')){
            $vendor = VendorRequest::findorfail($id);
            return view('admin.vendors.requests.edit', compact('vendor'));
        }
        abort(404);
    }

    public function vendorsRequestupdate(Request $request, $id)
    {
        if(check_permissions('manage_vendors')){
            $vendorRequest = VendorRequest::findorfail($id);
            if($request->action == 1){
                $vendorRequest->status = 0;
                $vendorRequest->save();
                $vendor = new Vendor();
                $vendor->name = $vendorRequest->name;
                $vendor->slug = '';
                $vendor->description = $vendorRequest->description;
                $vendor->address = $vendorRequest->address;
                $vendor->city = $vendorRequest->city;
                $vendor->country_id = $vendorRequest->country_id;
                $vendor->zipcode = $vendorRequest->zipcode;
                $vendor->phone = $vendorRequest->phone;
                $vendor->email = $vendorRequest->email;
                $vendor->vstatus = 2;
                $vendor->save();
                $vendorRole = new VendorRole();
                $vendorRole->user_id = $vendorRequest->user_id;
                $vendorRole->vendor_id = $vendor->id;
                $vendorRole->save();
                $userRole = new UserRole();
                $userRole->user_id = $vendorRequest->user_id;
                $userRole->role_id = 2;
                $userRole->save();
                $workHour = WorkHour::updateOrCreate(
                    ['vendor_id' => $vendor->id],
                    [
                        'monday' => 1, 'monday_start' => '08:00:00', 'monday_end'=> '17:00:00',
                        'tuesday' => 1, 'tuesday_start' => '08:00:00', 'tuesday_end'=> '17:00:00',
                        'wednesday' => 1, 'wednesday_start' => '08:00:00', 'wednesday_end'=> '17:00:00',
                        'thursday' => 1, 'thursday_start' => '08:00:00', 'thursday_end'=> '17:00:00',
                        'friday' => 1, 'friday_start' => '08:00:00', 'friday_end'=> '17:00:00',
                        'saturday' => 0, 'saturday_start' => '08:00:00', 'saturday_end'=> '17:00:00',
                        'sunday' => 0, 'sunday_start' => '08:00:00', 'sunday_end'=> '17:00:00',
                    ]
                );
                session()->put('success','Dyqani u pranua me sukses.');
                return redirect()->route('admin.vendors.requests');
            } else {
                $vendorRequest->status = 0;
                $vendorRequest->save();
                session()->put('success','Dyqani u refuzua.');
                return redirect()->route('admin.vendors.requests');
            }
        }
    }

    public function vendorsRequestdelete($id)
    {
        if(check_permissions('manage_vendors')){
            $vendorRequest = VendorRequest::findorfail($id);
            if($vendorRequest){
                $vendorRequest->delete();
                session()->put('success','Dyqani u fshi me sukses.');
                return redirect()->route('admin.vendors.requests');
            }
        }
        abort(404);
    }

    public function vendorsname()
    {
        if(check_permissions('manage_vendors')){
            $vendorsActive = VendorNameRequest::where([['udelete', '=', 0], ['status', '=', 0]])->orderBy('created_at', 'DESC')->get();
            $vendors = VendorNameRequest::where([['udelete', '=', 0],['status', '!=', 0]])->orderBy('created_at', 'DESC')->get();
            return view('admin.vendors.name-request', compact('vendorsActive', 'vendors'));
        }
        abort(404);
    }

    public function editvendorname($id)
    {
        if(check_permissions('manage_vendors')){
            $vendor = VendorNameRequest::findorfail($id);
            return view('admin.vendors.name-request-edit', compact('vendor'));
        }
        abort(404);
    }

    public function storevendorname(Request $request, $id)
    {
        if(check_permissions('manage_vendors')){
            $validatedDate = $request->validate([
                'acceptRequest' => 'required',
            ]);
            $vendorName = VendorNameRequest::findorfail($id);
            $vendor = $vendorName->vendor;
            $vendorName->status = $request->acceptRequest;
            $vendorName->save();
            $vendor->name = $vendorName->name;
            $vendor->slug = '';
            $vendor->save();
            // TODO: MAIL
            if($request->acceptRequest == 2){
                session()->put('success','Ndryshimi i emrit të Dyqanit u refuzua.');
            } else {
                session()->put('success','Ndryshimi i emrit të Dyqanit u aprovua.');
            }
            return redirect()->route('admin.vendors.namechange');
        }
        abort(404);
    }

    public function loginvendor($id)
    {
        if(check_permissions('manage_vendors') && is_numeric($id)){
            session()->put('logAsVendor', $id);
            session()->put('info','Ju jeni futur si dyqan.');
            return redirect()->route('vendor.home');
        }
    }

    public function cloginvendor()
    {
        if(check_permissions('manage_vendors')){
            session()->forget('logAsVendor');
            session()->put('info','Ju jeni futur si administrator.');
            return redirect()->route('admin.home');
        }
    }

    public function editvendor($id)
    {
        if(check_permissions('manage_vendors')){
            $vendor = Vendor::findorfail($id);
            $countries = Country::all();
            $shippingCountry = Country::where('shipping', '1')->get();
            return view('admin.vendors.edit-vendor', compact('vendor', 'countries', 'shippingCountry'));
        }
        abort(404);
    }

    public function storevendor(Request $request)
    {
        if(check_permissions('manage_vendors')){
            $validatedDate = $request->validate([
                'email' => 'required|email',
                'name' => 'required',
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
                'name.required' => 'Emri është i detyrueshëm',
                'description.required' => 'Përshkrimi është i detyrueshëm',
                'address.required' => 'Adresa është i detyrueshëm',
                'city.required' => 'Qyteti është i detyrueshëm',
                'city.min' => 'Qyteti është i detyrueshëm',
                'country.required' => 'Shteti është i detyrueshëm',
                'country.min' => 'Shteti është i detyrueshëm',
                'phone.required' => 'Telefoni është i detyrueshëm',
                'trans.required' => 'Transporti është i detyrueshëm',
                'time.*.required' => 'Oraret janë të detyrueshëm',
                'logo_path.required' => 'Logo e Dyqanit është e detyrueshme',
                'cover_path.required' => 'Cover i Dyqanit është i detyrueshëm',
            ]);
            $currVendor = $request->vendor_id;
            $vendor = Vendor::findorfail($currVendor);
            $vendor->name = $request->name;
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
            if($request->nlimit && is_numeric($request->nlimit)){
                $vendor->nlimit = $request->nlimit;
            } else {
                $vendor->nlimit = 0;
            }
            if($request->slimit && is_numeric($request->slimit)){
                $vendor->slimit = $request->slimit;
            } else {
                $vendor->slimit = 0;
            }
            if($request->silimit && is_numeric($request->silimit)){
                $vendor->silimit = $request->silimit;
            } else {
                $vendor->silimit = 0;
            }
            if($request->verified == 1){
                $vendor->verified = 1;
            } else {
                $vendor->verified = 0;
            }
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
                    ['vendor_id' => $currVendor, 'country_id'=> $key],
                    ['transport' => $transportType, 'limit' => $request->transLimit[$key], 'cost'=> $request->transCost[$key][0], 'transtime' => $request->transTime[$key][0]]
                );
            }
            $vendor->socials()->delete();
            foreach ($request->socials as $key=>$social) {
                if($social){
                    $socials = SocialLink::updateOrCreate(
                        ['vendor_id' => $currVendor, 'name'=> $key],
                        ['links' => $social]
                    );
                }
            }
            $workHours = $request->time;
            $workHour = WorkHour::updateOrCreate(
                ['vendor_id' => $currVendor],
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
            session()->put('success','Ndryshimet për Dyqanin u ruajtën.');
            return redirect()->route('admin.vendors.index');
        }
        abort(404);
    }

    public function deleteVendor($id)
    {
        if(check_permissions('manage_vendors') && check_permissions('delete_rights')){
            $vendor = Vendor::findorfail($id);
            $vendor->dshow = 0;
            $vendor->vstatus = 0;
            $vendor->save();
            $vendorRoles = VendorRole::where('vendor_id', '=', $vendor->id)->get();
            foreach($vendorRoles as $role){
                $userRole = UserRole::where('user_id', '=', $role->user_id)->get();
                ray($userRole);
                foreach($userRole as $uRole){
                    if($uRole->roleD->type == 0){
                        UserRole::where([['user_id', '=', $uRole->user_id],['role_id', '=', $uRole->role_id]])->delete();
                    }
                }
            }
            VendorRole::where([['vendor_id', '=', $vendor->id]])->delete();
            Product::where('vendor_id', '=', $vendor->id)->update(['vstatus' => 0]);
            session()->put('success','Dyqani u fshi me sukses.');
            return redirect()->route('admin.vendors.index');
        }
    }

    public function tickets()
    {
        if(check_permissions('manage_supports')){
            $tickets = Ticket::where('status', '<>', 4)->orderBy('created_at', 'DESC')->get();
            $atickets = Ticket::where('status', '=', 4)->orderBy('created_at', 'DESC')->get();
            return view('admin.tickets.index', compact('atickets', 'tickets'));
        }
        abort(404);
    }

    public function singletickets($id)
    {
        if(check_permissions('manage_supports')){
            if(is_numeric($id)){
                $ticket = Ticket::where('id', $id)->first();
                return view('admin.tickets.single', compact('ticket'));
            }
        }
        abort(404);
    }

    public function addsingletickets(Request $request, $id)
    {
        if(check_permissions('manage_supports')){
            if(is_numeric($id)){
                $validatedDate = $request->validate([
                    'message' => 'required',
                ], [
                    'message.required' => 'Mezazhi është i detyrueshëm'
                ]);
                if($request->message){
                    $ticket = new TicketMessage();
                    $ticket->ticket_id = $id;
                    $ticket->user_id = 0;
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
                return redirect()->route('admin.ticket.single', $id);
            }
        }
        abort(404);
    }

    public function closesingletickets(Request $request, $id)
    {
        if(check_permissions('manage_supports')){
            if(is_numeric($id)){
                $supportTicket = Ticket::findorfail($id);
                $supportTicket->status = 6;
                $supportTicket->save();
                session()->put('warning','Kërkesa për suport u mbyll.');
                return redirect()->route('admin.ticket.single', $id);
            }
        }
        abort(404);
    }

    public function refundsingletickets(Request $request, $id)
    {
        if(check_permissions('manage_supports')){
            if(is_numeric($id)){
                $supportTicket = Ticket::findorfail($id);
                $supportTicket->status = 7;
                $supportTicket->save();
                $order = OrderVendor::where([
                    ['order_id', '=', $supportTicket->order_id],
                    ['vendor_id', '=', $supportTicket->vendor_id],
                ])->first();
                $order->status = 3;
                $order->save();
                session()->put('success','Kërkesa për suport u kalua në statusin Rikthim Lekësh.');
                return redirect()->route('admin.ticket.single', $id);
            }
        }
        abort(404);
    }
}
