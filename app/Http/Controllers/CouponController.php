<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Category;
use App\Rules\UniqueCoupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function coupons()
    {
        if(check_permissions('manage_offers')){
            $coupons = Coupon::orderBy('updated_at', 'DESC')->get();
            return view('admin.coupons.index', compact('coupons'));
        }
        abort(404);
    }

    public function vcoupons()
    {
        if(check_permissions('manage_offers') && vendor_status()){
            $coupons = current_vendor()->coupons()->orderBy('updated_at', 'DESC')->get();
            return view('admin.coupons.vindex', compact('coupons'));
        }
        abort(404);
    }

    public function vnewcoupons()
    {
        if(check_permissions('manage_offers') && vendor_status()){
            $categories = Category::where('parent', '0')->get();
            return view('admin.coupons.vnew', compact('categories'));
        }
        abort(404);
    }

    public function vsavecoupons(Request $request)
    {
        if(check_permissions('manage_offers') && vendor_status()){
            $validatedDate = $request->validate([
                'code' => ['required', new UniqueCoupon()],
                'action' => 'required',
                'discount' => 'required|gt:0',
                'start_date' => 'required|date|after:yesterday',
                'expire_date' => 'required|date|after_or_equal:start_date',
            ], [
                'code.required' => 'Kodi i Kuponit është i detyrueshëm',
                'action.required' => 'Zgjidhni tipin e kuponit',
                'discount.required' => 'Ulja është e detyrueshme',
                'discount.gt' => 'Ulja është e detyrueshme',
                'start_date.required' => 'Data e Fillimit është e detyrueshëm',
                'start_date.date' => 'Data e Fillimit duhet të jetë në formatin datë',
                'start_date.after' => 'Data e Fillimit duhet të jetë me e madhe se data e djeshme',
                'expire_date.required' => 'Data e Mbarimit është e detyrueshëm',
                'expire_date.date' => 'Data e Mbarimit duhet të jetë në formatin datë',
                'expire_date.after_or_equal' => 'Data e Mbarimit duhet të jetë me e madhe se data e fillimit',
            ]);
            $coupon = new Coupon();
            $coupon->vendor_id = current_vendor()->id;
            $coupon->code = 'v'.current_vendor()->id.'-'.$request->code;
            $coupon->ucode = $request->code;
            $coupon->description = $request->description;
            if($request->coupontype == 2){
                $coupon->action = 1;
                $couponCategories = [];
                foreach($request->categories as $ccategory){
                    $couponCategories[] = (int) $ccategory;
                }
                $coupon->categories = json_encode($couponCategories);
                $coupon->products = NULL;
                $coupon->type = 2;
            } elseif($request->coupontype == 3){
                $coupon->action = $request->action;
                $coupon->categories = NULL;
                $coupon->products = json_encode($request->products);
                $coupon->type = 3;
            } else {
                $coupon->action = 1;
                $coupon->categories = NULL;
                $coupon->products = NULL;
            }
            $coupon->discount = $request->discount;
            if($coupon->action == 1){
                if($request->discount >= 99){
                    $coupon->discount = 99;
                }
            }
            $coupon->withoffer = $request->withoffer;
            $coupon->start_date = $request->start_date.' 00:00:05';
            $coupon->expire_date = $request->expire_date.' 23:59:58';
            $coupon->save();
            session()->put('success','Kuponi u shtua me sukses.');
            return redirect()->route('vendor.coupons.index');
        }
        abort(404);
    }

    public function editcoupons($id)
    {
        if(check_permissions('manage_offers')){
            $coupon = Coupon::findorfail($id);
            $categories = Category::where('parent', '0')->get();
            $selectedVendors = collect(json_decode($coupon->vendors))->toArray();
            $selectedCategories = collect(json_decode($coupon->categories))->toArray();
            $selectedProducts = collect(json_decode($coupon->products))->toArray();
            return view('admin.coupons.edit', compact('coupon', 'categories', 'selectedCategories', 'selectedVendors', 'selectedProducts'));
        }
        abort(404);
    }

    public function veditcoupons($id)
    {
        if(check_permissions('manage_offers') && vendor_status()){
            $coupon = Coupon::findorfail($id);
            if(current_vendor()->id == $coupon->vendor_id){
                $categories = Category::where('parent', '0')->get();
                $selectedVendors = $coupon->vendor_id;
                $selectedCategories = collect(json_decode($coupon->categories))->toArray();
                $selectedProducts = collect(json_decode($coupon->products))->toArray();
                return view('admin.coupons.vedit', compact('coupon', 'categories', 'selectedCategories', 'selectedVendors', 'selectedProducts'));
            }
        }
        abort(404);
    }

    public function storecoupons(Request $request, $id)
    {
        if(check_permissions('manage_offers')){
            $minDate = '2020-10-01';
            $request->merge([
                'before_date' => $minDate
            ]);
            $validatedDate = $request->validate([
                'code' => ['required', new UniqueCoupon($id)],
                'discount' => 'required|gt:0',
                'start_date' => 'required|date|after:before_date',
                'expire_date' => 'required|date|after_or_equal:start_date',
            ], [
                'code.required' => 'Kodi i Kuponit është i detyrueshëm',
                'discount.required' => 'Ulja është e detyrueshme',
                'discount.gt' => 'Ulja është e detyrueshme',
                'start_date.required' => 'Data e Fillimit është e detyrueshëm',
                'start_date.date' => 'Data e Fillimit duhet të jetë në formatin datë',
                'start_date.after' => 'Data e Fillimit duhet të jetë me e madhe se data 1 Janar 2020',
                'expire_date.required' => 'Data e Mbarimit është e detyrueshëm',
                'expire_date.date' => 'Data e Mbarimit duhet të jetë në formatin datë',
                'expire_date.after_or_equal' => 'Data e Mbarimit duhet të jetë me e madhe se data e fillimit',
            ]);
            $coupon = Coupon::findorfail($id);
            $coupon->code = 'v'.current_vendor()->id.'-'.$request->code;
            $coupon->ucode = $request->code;
            $coupon->description = $request->description;
            if($coupon->type == 2){
                $coupon->action = 1;
                $couponCategories = [];
                foreach($request->categories as $ccategory){
                    $couponCategories[] = (int) $ccategory;
                }
                $coupon->categories = json_encode($couponCategories);
            } elseif($coupon->type == 3){
                $coupon->action = $request->action;
                $coupon->products = json_encode($request->products);
            } else {
                $coupon->action = 1;
            }
            $coupon->discount = $request->discount;
            if($coupon->action == 1){
                if($request->discount >= 99){
                    $coupon->discount = 99;
                }
            }
            $coupon->withoffer = $request->withoffer;
            $coupon->start_date = $request->start_date.' 00:00:05';
            $coupon->expire_date = $request->expire_date.' 23:59:58';
            $coupon->save();
            session()->put('success','Kuponi u ruajt me sukses.');
            return redirect()->route('admin.coupons.index');
        }
        abort(404);
    }

    public function vstorecoupons(Request $request, $id)
    {
        if(check_permissions('manage_offers') && vendor_status()){
            $minDate = '2020-10-01';
            $request->merge([
                'before_date' => $minDate
            ]);
            $validatedDate = $request->validate([
                'code' => ['required', new UniqueCoupon($id)],
                'discount' => 'required|gt:0',
                'start_date' => 'required|date|after:before_date',
                'expire_date' => 'required|date|after_or_equal:start_date',
            ], [
                'code.required' => 'Kodi i Kuponit është i detyrueshëm',
                'discount.required' => 'Ulja është e detyrueshme',
                'discount.gt' => 'Ulja është e detyrueshme',
                'start_date.required' => 'Data e Fillimit është e detyrueshëm',
                'start_date.date' => 'Data e Fillimit duhet të jetë në formatin datë',
                'start_date.after' => 'Data e Fillimit duhet të jetë me e madhe se data 1 Janar 2020',
                'expire_date.required' => 'Data e Mbarimit është e detyrueshëm',
                'expire_date.date' => 'Data e Mbarimit duhet të jetë në formatin datë',
                'expire_date.after_or_equal' => 'Data e Mbarimit duhet të jetë me e madhe se data e fillimit',
            ]);
            $coupon = Coupon::findorfail($id);
            $coupon->code = 'v'.current_vendor()->id.'-'.$request->code;
            $coupon->ucode = $request->code;
            $coupon->description = $request->description;
            if($coupon->type == 2){
                $coupon->action = 1;
                $couponCategories = [];
                foreach($request->categories as $ccategory){
                    $couponCategories[] = (int) $ccategory;
                }
                $coupon->categories = json_encode($couponCategories);
            } elseif($coupon->type == 3){
                $coupon->action = $request->action;
                $coupon->products = json_encode($request->products);
            } else {
                $coupon->action = 1;
            }
            $coupon->discount = $request->discount;
            if($coupon->action == 1){
                if($request->discount >= 99){
                    $coupon->discount = 99;
                }
            }
            $coupon->withoffer = $request->withoffer;
            $coupon->start_date = $request->start_date.' 00:00:05';
            $coupon->expire_date = $request->expire_date.' 23:59:58';
            $coupon->save();
            session()->put('success','Kuponi u ruajt me sukses.');
            return redirect()->route('vendor.coupons.index');
        }
        abort(404);
    }

    public function deleteCoupon($id)
    {
        if(check_permissions('manage_offers') && check_permissions('delete_rights')){
            $coupon = Coupon::findorfail($id);
            if($coupon){
                $coupon->delete();
                session()->put('success','Kuponi u fshi me sukses.');
                return redirect()->route('admin.coupons.index');
            }
        }
        abort(404);
    }

    public function vdeleteCoupon($id)
    {
        $coupon = Coupon::findorfail($id);
        if(check_permissions('manage_offers') && check_permissions('delete_rights') && vendor_status() && $coupon && current_vendor()->id == $coupon->vendor_id){
            $coupon->delete();
            session()->put('success','Kuponi u fshi me sukses.');
            return redirect()->route('vendor.coupons.index');
        }
        return redirect()->route('vendor.coupons.index');
    }
}
