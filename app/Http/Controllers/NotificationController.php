<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index()
    {
        if(check_permissions('manage_vendor')){
            $notificationsNA = Notification::where('nactive', '=', 0)->orderBy('created_at', 'DESC')->get();
            $notifications = Notification::where('nactive', '!=', 0)->orderBy('created_at', 'DESC')->get();
            $limit = Setting::where('name', '=', 'notificationlimit')->first();
            return view('admin.notifications.index', compact('notifications', 'notificationsNA', 'limit'));
        }
        abort(404);
    }

    public function limitNotification()
    {
        if(check_permissions('manage_vendor')){
            $limit = Setting::where('name', '=', 'notificationlimit')->first();
            return view('admin.notifications.limit', compact('limit'));
        }
        abort(404);
    }

    public function limitNotificationUpdate(Request $request)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'limit' => 'required|numeric',
            ], [
                'limit.required' => 'Limiti është i detyrueshëm',
                'limit.numeric' => 'Limiti duhet të jetë në formatin numër',
            ]);
            Setting::upsert([
                ['name' => 'notificationlimit', 'value' => $request->limit]
            ], ['name'], ['value']);
            session()->put('success','Limiti u ruajt me sukses.');
            return redirect()->route('admin.notifications.index');
        }
        abort(404);
    }

    public function addNotification()
    {
        if(check_permissions('manage_vendor')){
            return view('admin.notifications.add-notification');
        }
        abort(404);
    }

    public function storeNotification(Request $request)
    {
        if(check_permissions('manage_vendor')){
            if($request->send_later == 1){
                $request->merge([
                    'send_later' => 1
                ]);
            } else {
                $request->merge([
                    'send_at' => date("Y-m-d H:i:s")
                ]);
            }
            $validatedDate = Validator::make($request->all(), [
                'title' => 'required',
                'message' => 'required',
                'ntype' => 'required',
                'vlink' => 'required_if:ntype,1,3',
                'plink' => 'required_if:ntype,2',
                'coupon' => 'required_if:ntype,3',
                'olink' => 'required_if:ntype,4',
                'send_at' => 'required_if:send_later,1|after:'.date('Y-m-d H:i'),
            ], [
                'title.required' => 'Titulli është i detyrueshëm',
                'message.required' => 'Mesazhi është i detyrueshëm',
                'ntype.required' => 'Lloji i njoftimit është i detyrueshëm',
                'vlink.required_unless' => 'Linku i dyqanit është i detyrueshëm',
                'plink.required_if' => 'Linku i produktit është i detyrueshëm',
                'coupon.required_if' => 'Kuponi është i detyrueshëm',
                'send_at.required_if' => 'Data e dërgimit është e detyrueshme',
                'send_at.after' => 'Data e dërgimit duhet të jetë jo më pak se data dhe ora e tanishme',
            ]);
            $publishLater = false;
            if($request->send_later){
                $publishLater = true;
            }
            if($request->ntype == 2){
                $prodInfo = $this->extractUrl($request->plink);
                if($prodInfo['status'] == 'error'){
                } else {
                    $prodLink = $prodInfo['link'];
                    $prodName = $prodInfo['name'];
                    $additionalField = array(
                        "linkType" => 'product',
                        "product" => $prodLink,
                        "productName" => $prodName,
                    );
                    $oLink = $request->plink;
                    $data = array(
                        "linkType" => 'product',
                        "product" => $prodLink,
                    );
                    $appUrl = 'elefandi://product';
                    $filters = array(
                        array(
                            "field" => "tag",
                            "key" => "notification",
                            "relation" => "=",
                            "value" => "yes"
                        ),
                        array("operator" => "AND"),
                        array(
                            "field" => "tag",
                            "key" => "products",
                            "relation" => "=",
                            "value" => "yes"
                        )
                    );
                }
            } else if ($request->ntype == 4) {
                $offerInfo = $this->extractUrl($request->olink);
                if($offerInfo['status'] == 'error'){
                } else {
                    $offerLink = $offerInfo['link'];
                    $offerName = $offerInfo['name'];
                    $additionalField = array(
                        "linkType" => 'offer',
                        "offer" => $offerLink,
                        "offerName" => $offerName,
                    );
                    $oLink = $request->plink;
                    $data = array(
                        "linkType" => 'offer',
                        "offer" => $offerLink,
                    );
                    $appUrl = 'elefandi://offer';
                    $filters = array(
                        array(
                            "field" => "tag",
                            "key" => "notification",
                            "relation" => "=",
                            "value" => "yes"
                        )
                    );
                }
            } else {
                $vendorInfo = $this->extractUrl($request->vlink);
                if($vendorInfo['status'] == 'error'){
                } else {
                    $vendLink = $vendorInfo['link'];
                    $vendName = $vendorInfo['name'];
                    $additionalField = array(
                        "linkType" => 'vendor',
                        "vendor" => $vendLink,
                        "vendorName" => $vendName,
                    );
                    $oLink = $request->vlink;
                    $data = array(
                        "linkType" => 'vendor',
                        "vendor" => $vendLink,
                    );
                }
                if($request->ntype == 1){
                    $appUrl = 'elefandi://vendor';
                    $filters = array(
                        array(
                            "field" => "tag",
                            "key" => "notification",
                            "relation" => "=",
                            "value" => "yes"
                        ),
                        array("operator" => "AND"),
                        array(
                            "field" => "tag",
                            "key" => "vendors",
                            "relation" => "=",
                            "value" => "yes"
                        )
                    );
                } else if($request->ntype == 3){
                    $data = array(
                        "linkType" => 'coupon',
                        "vendor" => $vendLink,
                        "coupon" => $request->coupon,
                        "vendorName" => $vendName,
                    );
                    $additionalField = $data;
                    $appUrl = 'elefandi://coupon';
                    $filters = array(
                        array(
                            "field" => "tag",
                            "key" => "notification",
                            "relation" => "=",
                            "value" => "yes"
                        ),
                        array("operator" => "AND"),
                        array(
                            "field" => "tag",
                            "key" => "coupons",
                            "relation" => "=",
                            "value" => "yes"
                        )
                    );
                }
            }
            $headings = array("en" => $request->title);
            $content = array("en" => $request->message);
            $fields = array(
                'app_id' => env('ONESIGNAL_APP_ID'),
                'filters' => $filters,
                'data' => $data,
                'app_url' => $appUrl,
                'headings' => $headings,
                'contents' => $content,
            );
            $sendStatus = 1;
            $send_at = date("Y-m-d H:i:s");
            if($publishLater){
                $carbon_date = Carbon::parse($request->send_at);
                $fields["send_after"] = $carbon_date;
                $send_at = $request->send_at;
                $sendStatus = 0;
            }
            $fields = json_encode($fields);
            $onesignalId = '';
            $onesignal = $this->onesignal(1, $fields);
            if(isset($onesignal->errors) && $onesignal->errors){
                // return redirect()->back()->withInput()->with('title', json_encode(array("status"=>"error", "message"=>$onesignal->errors)));
                $validatedDate->getMessageBag()->add('title', json_encode(array("status"=>"error", "message"=>$onesignal->errors)));
                return redirect()->back()->withInput()->withErrors($validatedDate);
            } else {
                $onesignalId = $onesignal->id;
            }
            $notification = new Notification();
            $notification->title = $request->title;
            $notification->message = $request->message;
            $notification->ntype = $request->ntype;
            $notification->nactive = 1;
            $notification->link = $oLink;
            $notification->fields = serialize($additionalField);
            $notification->oneid = $onesignalId;
            $notification->nsent = $sendStatus;
            $notification->send_at = $send_at;
            $notification->save();
            $notification->title = $request->title;
            // image
            session()->put('success','Njoftimi u shtua me sukses.');
            return redirect()->route('admin.notifications.index');
        }
        abort(404);
    }

    public function editNotification($id)
    {
        if(check_permissions('manage_vendor') && is_numeric($id)){
            $notification = Notification::findOrFail($id);
            $sendAtTime = Carbon::parse($notification->send_at);
            $sendAt = Carbon::now()->lt($sendAtTime);
            return view('admin.notifications.edit-notification', compact('notification', 'sendAtTime', 'sendAt'));
        }
        abort(404);
    }

    public function viewNotification($id)
    {
        if(check_permissions('manage_vendor') && is_numeric($id)){
            $notification = Notification::findOrFail($id);
            $sendAtTime = Carbon::parse($notification->send_at);
            $sendAt = Carbon::now()->lt($sendAtTime);
            return view('admin.notifications.view-notification', compact('notification', 'sendAtTime', 'sendAt'));
        }
        abort(404);
    }

    public function updateNotification(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            $notificationO = Notification::findOrFail($id);
            $request->merge([
                'ntype' => $notificationO->ntype
            ]);
            if($request->send_later == 1){
                $request->merge([
                    'send_later' => 1
                ]);
            } else {
                $request->merge([
                    'send_at' => date("Y-m-d H:i:s")
                ]);
            }
            $validatedDate = $request->validate([
                'title' => 'required',
                'message' => 'required',
                'ntype' => 'required',
                'vlink' => 'required_if:ntype,1,3',
                'plink' => 'required_if:ntype,2',
                'coupon' => 'required_if:ntype,3',
                'olink' => 'required_if:ntype,4',
                'send_at' => 'required_if:send_later,1|after:'.date('Y-m-d H:i'),
            ], [
                'title.required' => 'Titulli është i detyrueshëm',
                'message.required' => 'Mesazhi është i detyrueshëm',
                'ntype.required' => 'Lloji i njoftimit është i detyrueshëm',
                'vlink.required_if' => 'Linku i dyqanit është i detyrueshëm',
                'plink.required_if' => 'Linku i produktit është i detyrueshëm',
                'coupon.required_if' => 'Kuponi është i detyrueshëm',
                'olink.required_if' => 'Linku i Ofertës është i detyrueshëm',
                'send_at.required_if' => 'Data e dërgimit është e detyrueshme',
                'send_at.after' => 'Data e dërgimit duhet të jetë jo më pak se data dhe ora e tanishme',
            ]);

            $publishLater = false;
            if($request->send_later){
                $publishLater = true;
            }
            if($request->ntype == 2){
                $prodInfo = $this->extractUrl($request->plink);
                if($prodInfo['status'] == 'error'){
                } else {
                    $prodLink = $prodInfo['link'];
                    $prodName = $prodInfo['name'];
                    $additionalField = array(
                        "linkType" => 'product',
                        "product" => $prodLink,
                        "productName" => $prodName,
                    );
                    $oLink = $request->plink;
                    $data = array(
                        "linkType" => 'product',
                        "product" => $prodLink,
                    );
                    $appUrl = 'elefandi://product';
                    $filters = array(
                        array(
                            "field" => "tag",
                            "key" => "notification",
                            "relation" => "=",
                            "value" => "yes"
                        ),
                        array("operator" => "AND"),
                        array(
                            "field" => "tag",
                            "key" => "products",
                            "relation" => "=",
                            "value" => "yes"
                        )
                    );
                }
            } else if ($request->ntype == 4) {
                $offerInfo = $this->extractUrl($request->olink);
                if($offerInfo['status'] == 'error'){
                } else {
                    $offerLink = $offerInfo['link'];
                    $offerName = $offerInfo['name'];
                    $additionalField = array(
                        "linkType" => 'offer',
                        "offer" => $offerLink,
                        "offerName" => $offerName,
                    );
                    $oLink = $request->plink;
                    $data = array(
                        "linkType" => 'offer',
                        "offer" => $offerLink,
                    );
                    $appUrl = 'elefandi://offer';
                    $filters = array(
                        array(
                            "field" => "tag",
                            "key" => "notification",
                            "relation" => "=",
                            "value" => "yes"
                        )
                    );
                }
            } else {
                $vendorInfo = $this->extractUrl($request->vlink);
                if($vendorInfo['status'] == 'error'){
                } else {
                    $vendLink = $vendorInfo['link'];
                    $vendName = $vendorInfo['name'];
                    $additionalField = array(
                        "linkType" => 'vendor',
                        "vendor" => $vendLink,
                        "vendorName" => $vendName,
                    );
                    $oLink = $request->vlink;
                    $data = array(
                        "linkType" => 'vendor',
                        "vendor" => $vendLink,
                    );
                }
                if($request->ntype == 1 || $request->ntype == 5){
                    $appUrl = 'elefandi://vendor';
                    $filters = array(
                        array(
                            "field" => "tag",
                            "key" => "notification",
                            "relation" => "=",
                            "value" => "yes"
                        ),
                        array("operator" => "AND"),
                        array(
                            "field" => "tag",
                            "key" => "vendors",
                            "relation" => "=",
                            "value" => "yes"
                        )
                    );
                } else if($request->ntype == 3){
                    $data = array(
                        "linkType" => 'coupon',
                        "vendor" => $vendLink,
                        "coupon" => $request->coupon,
                        "vendorName" => $vendName,
                    );
                    $additionalField = $data;
                    $appUrl = 'elefandi://coupon';
                    $filters = array(
                        array(
                            "field" => "tag",
                            "key" => "notification",
                            "relation" => "=",
                            "value" => "yes"
                        ),
                        array("operator" => "AND"),
                        array(
                            "field" => "tag",
                            "key" => "coupons",
                            "relation" => "=",
                            "value" => "yes"
                        )
                    );
                }
            }
            $headings = array("en" => $request->title);
            $content = array("en" => $request->message);
            $fields = array(
                'app_id' => env('ONESIGNAL_APP_ID'),
                'filters' => $filters,
                'data' => $data,
                'headings' => $headings,
                'contents' => $content,
            );
            $sendStatus = 1;
            $send_at = date("Y-m-d H:i:s");
            if($publishLater){
                $carbon_date = Carbon::parse($request->send_at);
                $fields["send_after"] = $carbon_date;
                $send_at = $request->send_at;
                $sendStatus = 0;
            }
            $fields = json_encode($fields);
            $onesignalId = '';
            $onesignal = $this->onesignal(1, $fields);
            if(isset($onesignal->errors) && $onesignal->errors){
                ray($onesignal);
                echo json_encode(array("status"=>"error", "message"=>$onesignal->errors));
            } else {
                $onesignalId = $onesignal->id;
            }
            $notificationO->title = $request->title;
            $notificationO->message = $request->message;
            $notificationO->ntype = $request->ntype;
            $notificationO->nactive = 1;
            $notificationO->link = $oLink;
            $notificationO->fields = serialize($additionalField);
            $notificationO->oneid = $onesignalId;
            $notificationO->nsent = $sendStatus;
            $notificationO->send_at = $send_at;
            $notificationO->save();
            session()->put('success','Njoftimi u pranua me sukses.');
            return redirect()->route('admin.notifications.index');
        }
        abort(404);
    }

    public function rejectNotification(Request $request, $id)
    {
        if(check_permissions('manage_vendor') && is_numeric($id)){
            $notification = Notification::findOrFail($id);
            $notification->nactive = 2;
            $notification->save();
            session()->put('success','Njoftimi u refuzua me sukses.');
            return redirect()->route('admin.notifications.index');
        }
    }

    protected function extractUrl($url){
        $re = '/^(?:(?:www\.)?(?:.*?))\.(?:com|al)\/(.*)/m';
        preg_match($re,trim($url),$match);
        if($match && $match[1]){
            preg_match('/^(?:(?:https|http)?(?::\/\/))?(?:www.)?(elefandi.com)/m',trim($url),$matchUrl);
            if($matchUrl && $matchUrl[1] != 'elefandi.com'){
                return ['status'=>'error', 'message'=>'Link i gabuar1'];
            }
            $urlExplode = explode('/', $match[1]);
            if(count($urlExplode) == 2){
                if(is_numeric($urlExplode[1])){
                    $pId = $urlExplode[1];
                    if($urlExplode[0] == 'offer'){
                        $offer = Offer::find($pId);
                        if($offer){
                            return ['status'=>'success', 'link'=>'offer/'.$pId, 'name'=>$offer->name, 'id'=>$offer->id];
                        }
                    } else {
                        $product = Product::find($pId);
                        if($product){
                            return ['status'=>'success', 'link'=>'product/'.$pId, 'name'=>$product->name, 'id'=>$product->id];
                        }
                    }
                    return ['status'=>'error', 'message'=>'Link i gabuar1'];
                } else {
                    if($urlExplode[0] == 'category'){
                        $cSlug = $urlExplode[1];
                        $category = Category::where('slug', '=', $cSlug)->first();
                        if($category){
                            return ['status'=>'success', 'link'=>'category/'.$category->id];
                        }
                    }
                }
            } else {
                $vendor = Vendor::where('slug', '=', $urlExplode[0])->first();
                if($vendor){
                    return ['status'=>'success', 'link'=>'vendor/'.$vendor->id, 'name'=>$vendor->name];
                }
            }
        }
    }

    protected function onesignal($send, $fields){
		if($send == 1){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic '.env('ONESIGNAL_AUTHORIZATION')));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			return json_decode($response);
		} else if ($send == 2){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications/".$fields."?app_id=".env('ONESIGNAL_APP_ID'));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.env('ONESIGNAL_AUTHORIZATION')));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			return json_decode($response);
		} else if ($send == 3){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications/".$fields."?app_id=".env('ONESIGNAL_APP_ID'));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.env('ONESIGNAL_AUTHORIZATION')));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			return json_decode($response);
		}
	}

    public function vindex()
    {
        if(check_permissions('manage_vendor') && vendor_status()){
            $notificationsNA = current_vendor()->notifications->where('nactive', '=', 0);
            $notifications = current_vendor()->notifications->where('nactive', '!=', 0);
            $limit = current_vendor()->nlimit;
            if(!current_vendor()->nlimit){
                $limit = Setting::where('name', '=', 'notificationlimit')->first();
                $limit = $limit->value;
            }
            $thisMonth = date('Y-m-01 00:01:00');
            $notificationsC = current_vendor()->notifications()->where('nactive', 0)->orWhere([['created_at', '>', $thisMonth], ['nactive', 1]])->count();
            return view('admin.notifications.vindex', compact('notifications', 'notificationsNA', 'notificationsC', 'limit'));
        }
        abort(404);
    }

    public function vaddNotification()
    {
        if(check_permissions('manage_vendor') && vendor_status()){
            $limit = current_vendor()->nlimit;
            if(!current_vendor()->nlimit){
                $limit = Setting::where('name', '=', 'notificationlimit')->first();
                $limit = $limit->value;
            }
            $thisMonth = date('Y-m-01 00:01:00');
            $notificationsC = current_vendor()->notifications()->where('nactive', 0)->orWhere([['created_at', '>', $thisMonth], ['nactive', 1]])->count();
            if($notificationsC <= $limit){
                return view('admin.notifications.vadd-notification');
            }
            session()->put('success','Njoftimi kanë arritur limitin. Kontaktoni me administratorin për të shtuar limitin tuaj mujor');
            return redirect()->route('vendor.notifications.index');
        }
        abort(404);
    }

    public function vstoreNotification(Request $request)
    {
        if(check_permissions('manage_vendor') && vendor_status()){
            if($request->send_later == 1){
                $request->merge([
                    'send_later' => 1
                ]);
            } else {
                $request->merge([
                    'send_at' => date("Y-m-d H:i:s")
                ]);
            }
            $validatedDate = $request->validate([
                'title' => 'required',
                'message' => 'required',
                'ntype' => 'required',
                'plink' => 'required_if:ntype,2',
                'coupon' => 'required_if:ntype,3',
                'send_at' => 'required_if:send_later,1|after:'.date('Y-m-d H:i'),
            ], [
                'title.required' => 'Titulli është i detyrueshëm',
                'message.required' => 'Mesazhi është i detyrueshëm',
                'ntype.required' => 'Lloji i njoftimit është i detyrueshëm',
                'plink.required_if' => 'Linku i produktit është i detyrueshëm',
                'coupon.required_if' => 'Kuponi është i detyrueshëm',
                'send_at.required_if' => 'Data e dërgimit është e detyrueshme',
                'send_at.after' => 'Data e dërgimit duhet të jetë jo më pak se data dhe ora e tanishme',
            ]);

            $send_at = date("Y-m-d H:i:s");
            if($request->send_later){
                $send_at = $request->send_at;
            }
            $additionalField = '';
            if($request->ntype == 1){
                $oLink = route('single.vendor', current_vendor()->slug);
            } elseif ($request->ntype == 2){
                $productLink = $this->extractUrl($request->plink);
                if($productLink['status'] == 'error'){
                    return redirect()->back()->withInput()->with('cerror', 'Linku i produktit është i gabuar');
                }
                $oLink = $request->plink;

            } elseif ($request->ntype == 3){
                $oLink = route('single.vendor', current_vendor()->slug);
                $additionalField = $request->coupon;
            } elseif ($request->ntype == 4){
                $oLink = route('single.vendor', current_vendor()->slug);
            }
            $notification = new Notification();
            $notification->vendor_id = current_vendor()->id;
            $notification->title = $request->title;
            $notification->message = $request->message;
            $notification->ntype = $request->ntype;
            $notification->nactive = 1;
            $notification->link = $oLink;
            $notification->fields = $additionalField;
            $notification->oneid = '';
            $notification->nsent = 0;
            $notification->nactive = 0;
            $notification->send_at = $send_at;
            $notification->save();
            session()->put('success','Njoftimi u shtua me sukses.');
            return redirect()->route('vendor.notifications.index');
        }
        abort(404);
    }

    public function vdeleteNotification($id)
    {
        if(check_permissions('manage_vendor') && vendor_status() && check_permissions('delete_rights') && is_numeric($id)){
            $notification = Notification::findOrFail($id);
            if(($notification->vendor_id == current_vendor()->id) && ($notification->nactive == 0)){
                $notification->delete();
                session()->put('success','Njoftimi u fshi me sukses.');
                return redirect()->route('vendor.notifications.index');
            }
        }
        abort(404);
    }
}
