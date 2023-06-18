<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\OrderVendor;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderTrack as OrderTrackMail;

class OrderController extends Controller
{
    public function orders()
    {
        if(check_permissions('manage_orders')){
            $title = 'Të gjitha Porositë';
            $orders = Order::orderBy('id', 'DESC')->get();
            return view('admin.orders.index', compact('title', 'orders'));
        }
        abort(404);
    }

    public function porders()
    {
        if(check_permissions('manage_orders')){
            $title = 'Porositë në Pritje';
            $orders = Order::where('status', '=', 0)->orderBy('id', 'DESC')->get();
            return view('admin.orders.index', compact('title', 'orders'));
        }
        abort(404);
    }

    public function corders()
    {
        if(check_permissions('manage_orders')){
            $title = 'Porositë e Dërguara';
            $orders = Order::where('status', '=', 1)->orderBy('id', 'DESC')->get();
            return view('admin.orders.index', compact('title', 'orders'));
        }
        abort(404);
    }

    public function dorders()
    {
        if(check_permissions('manage_orders')){
            $title = 'Porositë e Anulluara';
            $orders = Order::where('status', '=', 2)->orderBy('id', 'DESC')->get();
            return view('admin.orders.index', compact('title', 'orders'));
        }
        abort(404);
    }

    public function singleorder($id)
    {
        if(check_permissions('manage_orders')){
            $order = Order::findorfail($id);
            if($order){
                return view('admin.orders.single', compact('order'));
            }
        }
        abort(404);
    }
    
    public function singleordertrack($id)
    {
        if(check_permissions('manage_orders')){
            $order = Order::findorfail($id);
            if($order){
                return view('admin.orders.track', compact('order'));
            }
        }
        abort(404);
    }

    public function addsingleordertrack(Request $request, $id)
    {
        if(check_permissions('manage_orders')){
            $validatedDate = $request->validate([
                'comment' => 'required'
            ], [
                'comment.required' => 'Informacioni rreth gjurmimit është i detyrueshëm'
            ]);
            $orderVendor = OrderVendor::findOrFail($id);
            $tracking = new OrderTrack();
            $tracking->order_vendor_id = $id;
            $tracking->comment = $request->comment;
            $tracking->save();
            $userNotification = new UserNotification();
            $userNotification->user_id = $orderVendor->order->user_id;
            $userNotification->title = 'Informacion rreth Gjurmimit';
            $userNotification->message = 'Informacion rreth gjurmimit të porosisë tuaj është shtuar nga '.$orderVendor->vendor->name;
            $userNotification->fields = serialize(['order'=>$orderVendor->order_id]);
            $userNotification->save();
            session()->put('success','Informacioni rreth gjurmimit u ruajt.');
            return redirect()->back();
        }
        abort(404);
    }
    
    public function vorders()
    {
        if(check_permissions('manage_orders') && vendor_status()){
            $title = 'Të gjitha Porositë';
            $orders = current_vendor()->orders()->orderBy('id', 'DESC')->get();
            return view('admin.orders.vindex', compact('title', 'orders'));
        }
        abort(404);
    }

    public function vporders()
    {
        if(check_permissions('manage_orders') && vendor_status()){
            $title = 'Porositë në Pritje';
            $orders = current_vendor()->orders()->where('status', '=', 0)->orderBy('id', 'DESC')->get();
            return view('admin.orders.vindex', compact('title', 'orders'));
        }
        abort(404);
    }

    public function vcorders()
    {
        if(check_permissions('manage_orders') && vendor_status()){
            $title = 'Porositë e Dërguara';
            $orders = current_vendor()->orders()->where('status', '=', 1)->orderBy('id', 'DESC')->get();
            return view('admin.orders.vindex', compact('title', 'orders'));
        }
        abort(404);
    }

    public function vdorders()
    {
        if(check_permissions('manage_orders') && vendor_status()){
            $title = 'Porositë e Anulluara';
            $orders = current_vendor()->orders()->where('status', '=', 2)->orderBy('id', 'DESC')->get();
            return view('admin.orders.vindex', compact('title', 'orders'));
        }
        abort(404);
    }

    public function vsingleorder($id)
    {
        if(check_permissions('manage_orders') && vendor_status()){
            $order = current_vendor()->orders()->where('id', $id)->first();
            if($order){
                return view('admin.orders.vsingle', compact('order'));
            }
        }
        abort(404);
    }

    public function vsingleordertrack($id)
    {
        if(check_permissions('manage_orders') && vendor_status()){
            $order = current_vendor()->orders()->where('id', $id)->first();
            if($order){
                return view('admin.orders.vtrack', compact('order'));
            }
        }
        abort(404);
    }

    public function vaddsingleordertrack(Request $request, $id)
    {
        if(check_permissions('manage_orders') && vendor_status()){
            $validatedDate = $request->validate([
                'comment' => 'required'
            ], [
                'comment.required' => 'Informacioni rreth gjurmimit është i detyrueshëm'
            ]);
            $tracking = new OrderTrack();
            $orderId = OrderVendor::find($id);
            $tracking->order_id = $orderId->order_id;
            $tracking->order_vendor_id = $id;
            $tracking->comment = $request->comment;
            $tracking->save();
            $order = $orderId->order;
            $user = $order->user;
            $userNotification = new UserNotification();
            $userNotification->user_id = $order->user_id;
            $userNotification->title = 'Informacion rreth Gjurmimit';
            $userNotification->message = 'Informacion rreth gjurmimit të porosisë tuaj është shtuar nga '.$orderId->vendor->name;
            $userNotification->fields = serialize(['order'=>$orderId->order_id]);
            $userNotification->save();
            Mail::to($user->email)->send(new OrderTrackMail($user, $orderId, $tracking));
            session()->put('success','Informacioni rreth gjurmimit u shtua me sukses.');
            return redirect()->back();
        }
        abort(404);
    }
}
