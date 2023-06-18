<?php

namespace App\Http\Controllers;

use Image;
use Carbon\Carbon;
use App\Models\Ads;
use App\Models\User;
use App\Models\Order;
use App\Models\Pages;
use App\Models\Story;
use App\Mail\NewOrder;
use App\Mail\ContactUs;
use App\Models\Product;
use App\Models\Category;
use App\Models\UserRole;
use App\Mail\UserRegister;
use App\Models\VendorRole;
use App\Models\ChatMessage;
use App\Models\HomeFeature;
use App\Models\OrderVendor;
use App\Mail\VendorRegister;
use Illuminate\Http\Request;
use App\Models\VendorRequest;
use App\Mail\VendorUserMember;
use App\Mail\UserRegisterOrder;
use App\Models\UserRoleRequest;
use App\Models\HomeFeaturedProduct;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HomeController extends Controller
{

    public function generateProductImage()
    {
        $path = public_path('photos/products/');
        $files = \File::files($path);
        $sizes = array('230', '70');
        foreach($files as $file) {
            $name = $file->getBasename();
            $imgFile = Image::make($file);
            foreach($sizes as $size){
                $thumbnail = $imgFile->fit($size, $size, function ($constraint) {
                    $constraint->aspectRatio();
                });
                // dd($thumbnail);
                $pathss = public_path('photos/products/'.$size)."/".$name;
                $thumbnail->save($pathss);
            }
        }
    }

    public function testmail()
    {
        $request = UserRoleRequest::find(2);
            // (new VendorRegister(current_user(), $vendor, 1))->render();
        return (new VendorUserMember($request, $request->vendor, 1))->render();
        // $products = Product::all();
        // foreach($products as $product){
        //     $product->slug = null;
        //     $product->save();
        // }
        // return 1;

        // QrCode::generate('http://www.simplesoftware.io', 'photos/qrcodes/qrcode.svg');
        // $qrCode = QrCode::size(100)->style('round')->generate('http://www.simplesoftware.io');
        // // $qrCode = QrCode::format('png')->size(399)->color(40,40,40)->generate('Make me a QrCode!');
        // return $qrCode;
        // Mail::to('e.dalipi@codeit.al')->send(new OrderTrack());
        // $user = User::find(153);
        // $order = Order::find(19);
        // $vorder = OrderVendor::find(24);
        // return (new UserRegister(current_user(), '#', 'test'))->render();
        // return (new NewOrder($user, $order, false))->render();
        // return (new UserRegisterOrder($user, '123'))->render();
        // return (new NewOrder($user, $order, $vorder))->render();
        // Mail::to($vendOrder->vendor->email)->send(new NewOrder($user, $order, $vendOrder));
        // return (new UserRegister(current_user(), '#', 'test'))->render();
        // Mail::to(current_user()->email)->send(new VendorRegister(current_user(), $vendor, 1));
        // $request = UserRoleRequest::find(3);
            // (new VendorRegister(current_user(), $vendor, 1))->render();
        // return (new VendorUserMember($request, $request->vendor, 2))->render();
        // return (new VendorRegister($request, $request->vendor, 1))->render().(new VendorRegister(current_user(), $vendor, 2))->render();

        // $message = ChatMessage::findOrFail(503);
		// event(new \App\Events\MessageSent('035ed2f5-1cb7-4315-8311-613e7caaa8bd-1629881713', 1, $message));
        return '';
    }

    public function homeRedirect()
    {
        return redirect()->route('home');
    }

    public function home()
    {
        $nowTime = Carbon::now();
        $stories = Story::where('cactive', 1)->whereHas('items', function ($query) use ($nowTime) {
            $query->where('cactive', '=', '1')->where('end_story', '>', $nowTime)->where('start_story', '<', $nowTime);
            // $query->where('cactive', '=', '1')->where('start_story', '>', $nowTime);
        })->get();
        if (Cache::has('pcat')) {
            $categories = Cache::get('pcat');
        } else {
            $categories = Cache::rememberForever('pcat', function () {
                return Category::where('parent', '0')->get();
            });
        }
        $features = HomeFeature::orderBy('corder', 'asc')->limit(4)->get();
        $homeMAds = Ads::find(1);
        $homeAds = false;
        $featuredProductsNum = 3;
        if($homeMAds){
            if(!$homeMAds->ads->where('astatus')->isEmpty()){
                $homeAdsS = $homeMAds->ads->where('astatus')->random(1)->first();
                if($homeAdsS){
                    $featuredProductsNum = 2;
                    $homeAds = $homeAdsS;
                }
            }
        }
        $featuredProducts = HomeFeaturedProduct::orderBy('corder', 'asc')->limit($featuredProductsNum)->get();
        $homeCategories = $categories->where('home', '=', '1');
        $products = Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->orderBy('updated_at', 'DESC')->paginate(15);
        // $products = Product::paginate(15);
        // $products = Product::with('owner')->where('vendor.vstatus', '=', 1)->paginate(15);
        // $products = Product::select("*",
        //     DB::raw('(CASE 
        //         WHEN vendors.country_id = "1" THEN 0 
        //         WHEN vendors.country_id = "2" THEN 1 
        //         ELSE 3
        //         END) AS active_lable'))
        // ->get();
        // $products = Product::select("products.*", DB::raw('(CASE 
        //         WHEN vendors.country_id = 1 THEN 0 
        //         WHEN vendors.country_id = 2 THEN 1 
        //         ELSE 3
        //         END) AS corder'))
        //     ->leftJoin('vendors', 'vendors.id', '=', 'products.vendor_id')
        //     ->orderBy('corder')
        //     ->paginate(15);
        // ray()->stopShowingQueries();
        return view('home', compact('stories', 'categories', 'features', 'featuredProducts', 'homeAds', 'homeCategories', 'products', 'nowTime'));
    }

    public function register(Request $request)
    {
        if(current_user()){
            return redirect()->route('home');
        }
        $emailReq = '';
        if($request->stoken){
            $userReq = UserRoleRequest::where('token', '=', $request->stoken)->first();
            if($userReq){
                $emailReq = $userReq->user_email;
            }
        }
        return view('user.register',compact('emailReq'));
    }

    public function registerStore(Request $request)
    {
        $validatedDate = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'password_confirm' => 'required|same:password',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'country' => 'required|min:1',
            'city' => 'required|min:1',
        ], [
            'email.required' => 'Email është i detyrueshëm',
            'email.email' => 'Email nuk është shkruar saktë',
            'email.unique' => 'Email është regjistruar më parë. Provoni të hyni ose shkoni tek kam harruar fjalkalimin nëse nuk ju kujtohen te dhënat',
            'password.required' => 'Fjalkalimi është i detyrueshëm',
            'password.min' => 'Fjalkalimi duhet të jetë më shumë se 6 karaktere',
            'password_confirm.required' => 'Konfirmimi i fjalkalimit është i detyrueshëm',
            'password_confirm.same' => 'Konfirmimi i fjalkalimit nuk është i njëjtë me Fjalkalimin',
            'first_name.required' => 'Emri është i detyrueshëm',
            'last_name.required' => 'Mbiemri është i detyrueshëm',
            'address.required' => 'Adresa është i detyrueshëm',
            'phone.required' => 'Telefoni është i detyrueshëm',
            'country.required' => 'Shteti është i detyrueshëm',
            'country.min' => 'Shteti është i detyrueshëm',
            'city.required' => 'Qyteti është i detyrueshëm',
            'city.min' => 'Qyteti është i detyrueshëm',
        ]);
        // dd($request);
        $password = Hash::make($request->password);
        $user = new User();
        $user->email = $request->email;
        $user->password = $password;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->zipcode = $request->zipcode;
        $user->country_id = $request->country;
        $user->city = $request->city;
        // $user->vendortype = $request->vendortype;
        $user->save();
        $user->sendEmailVerificationNotification();
        if($request->stoken){
            $userReq = UserRoleRequest::where('token', '=', $request->stoken)->first();
            if($userReq){
                if($userReq->user_email == $request->email){
                    $userRole = new UserRole();
                    $userRole->user_id = $user->id;
                    $userRole->role_id = $userReq->role_id;
                    $userRole->save();
                    $vendorRole = new VendorRole();
                    $vendorRole->user_id = $user->id;
                    $vendorRole->vendor_id = $userReq->vendor_id;
                    $vendorRole->save();
                    Mail::to($userReq->vendor->email)->send(new VendorUserMember($userReq, $userReq->vendor, 2));
                    $userReq->delete();
                }
            }
        }
        return redirect()->route('home');
    }

    public function vendor()
    {
        return view('pages.vendor-register');
    }

    public function vendorRequest(Request $request)
    {
        // $validatedDate = $request->validate([
        //     'name' => 'required',
        //     'email' => 'required|email',
        //     'address' => 'required',
        //     'phone' => 'required',
        //     'country' => 'required|min:1',
        //     'city' => 'required|min:1',
        //     'description' => 'required',
        //     'message' => 'required',
        // ], [
        //     'name.required' => 'Emri është i detyrueshëm',
        //     'email.required' => 'Email është i detyrueshëm',
        //     'email.email' => 'Email nuk është shkruar saktë',
        //     'address.required' => 'Adresa është i detyrueshëm',
        //     'phone.required' => 'Telefoni është i detyrueshëm',
        //     'country.required' => 'Shteti është i detyrueshëm',
        //     'country.min' => 'Shteti është i detyrueshëm',
        //     'city.required' => 'Qyteti është i detyrueshëm',
        //     'city.min' => 'Qyteti është i detyrueshëm',
        //     'description.required' => 'Përshkrimi është i detyrueshëm',
        //     'message.required' => 'Mesazhi është i detyrueshëm',
        // ]);
        $validatedDate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'phone' => 'required',
            'country' => 'required|min:1',
            'city' => 'required|min:1',
            'description' => 'required',
            'message' => 'required',
        ], [
            'name.required' => 'Emri është i detyrueshëm',
            'email.required' => 'Email është i detyrueshëm',
            'email.email' => 'Email nuk është shkruar saktë',
            'address.required' => 'Adresa është i detyrueshëm',
            'phone.required' => 'Telefoni është i detyrueshëm',
            'country.required' => 'Shteti është i detyrueshëm',
            'country.min' => 'Shteti është i detyrueshëm',
            'city.required' => 'Qyteti është i detyrueshëm',
            'city.min' => 'Qyteti është i detyrueshëm',
            'description.required' => 'Përshkrimi është i detyrueshëm',
            'message.required' => 'Mesazhi është i detyrueshëm',
        ]);
        if ($validatedDate->fails()) {
            return redirect('regjistrim-dyqani#register-vendor')
                        ->withErrors($validatedDate)
                        ->withInput();

        }
        if(current_user()){
            $vendor = new VendorRequest();
            $vendor->user_id = current_user()->id;
            $vendor->name = $request->name;
            $vendor->email = $request->email;
            $vendor->address = $request->address;
            $vendor->phone = $request->phone;
            $vendor->country_id = $request->country;
            $vendor->city = $request->city;
            $vendor->description = $request->description;
            $vendor->message = $request->message;
            $vendor->save();
            Mail::to(current_user()->email)->send(new VendorRegister(current_user(), $vendor, 1)); // vendor
            Mail::to('info@elefandi.com')->send(new VendorRegister(current_user(), $vendor, 2)); // TODO: admin mail
            return redirect()->route('home');
        } else {
            $hasErrors = false;
            if(!$request->fname){
                $validatedDate->errors()->add(
                    'fname', 'Emri është i detyrueshëm'
                );
                $hasErrors = true;
            }
            if(!$request->lname){
                $validatedDate->errors()->add(
                    'lname', 'Mbiemri është i detyrueshëm'
                );
                $hasErrors = true;
            }
            if(!$request->password){
                $validatedDate->errors()->add(
                    'password', 'Fjalkalimit është i detyrueshëm'
                );
                $hasErrors = true;
            }
            if(!$request->password_confirm){
                $validatedDate->errors()->add(
                    'password_confirm', 'Konfirmimi i fjalkalimit është i detyrueshëm'
                );
                $hasErrors = true;
            }
            if($request->password != $request->password_confirm){
                $validatedDate->errors()->add(
                    'password', 'Fjalkalimi i ri dhe konfirmimi i tij nuk janë të njëjtë'
                );
                $hasErrors = true;
            }
            if($hasErrors){
                return redirect('regjistrim-dyqani#register-vendor')->withErrors($validatedDate)->withInput();
            }
            $password = Hash::make($request->password);
            $user = new User();
            $user->email = $request->email;
            $user->password = $password;
            $user->first_name = $request->fname;
            $user->last_name = $request->lname;
            $user->address = $request->address;
            $user->phone = $request->phone;
            $user->country_id = $request->country;
            $user->city = $request->city;
            $user->save();
            $user->sendEmailVerificationNotification();
            $vendor = new VendorRequest();
            $vendor->user_id = $user->id;
            $vendor->name = $request->name;
            $vendor->email = $request->email;
            $vendor->address = $request->address;
            $vendor->phone = $request->phone;
            $vendor->country_id = $request->country;
            $vendor->city = $request->city;
            $vendor->description = $request->description;
            $vendor->message = $request->message;
            $vendor->save();
            Mail::to($user->email)->send(new VendorRegister($user, $vendor, 1)); // vendor
            Mail::to('info@elefandi.com')->send(new VendorRegister($user, $vendor, 2)); // TODO: admin mail
            return redirect()->route('home');
        }
    }

    public function wishlist()
    {
        return view('product.wishlist');
    }

    public function contactus()
    {
        $submitedC = false;
        $contactPage = Pages::findOrFail(14);
        return view('user.contactus', compact('submitedC', 'contactPage'));
    }

    public function contactusPost(Request $request)
    {
        $validatedDate = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        $cName = '<p>Përdoruesi "'.$request->name.'" me email : "'.$request->email.'" ka shkruar: </p>';
		
		Mail::to('info@elefandi.al')->send(new ContactUs($cName, $request->subject, $request->message));
        $submitedC = true;
        return view('user.contactus', compact('submitedC'));
    }
}
