<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Arr;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Models\ShoppingCart;
use App\Models\WishList;
use Illuminate\Http\Request;
use App\Http\Resources\AddressResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderFResource;
use App\Http\Resources\TicketsResource;
use App\Http\Resources\TicketSingleResource;
use App\Http\Resources\OrderVendorsResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use App\Rules\MatchOldPassword;
use App\Http\Resources\UserResource;

class UserController extends BaseController
{
	public function syncUser(Request $request){
		if(current_user()){
			$user = current_user();
			$token = ($user->token)?$user->token->token:'';
			$response = [
				'user' => new UserResource($user),
				'token' => $token
			];
			if($user->vendor() && $user->vendor()->count()){
				$response['vendor_name'] = $user->vendor()->name;
				$response['vendor'] = $user->vendor()->id;
				$response['uvid'] = $user->vendor()->uvid;
				$vendPermission = $user->vroles->first();
				if($vendPermission){
					$response['vpermission'] = [];
					$response['vpermission']['manage_vendor'] = (isset($vendPermission['manage_vendor']) && $vendPermission['manage_vendor'])?1:0;
					$response['vpermission']['manage_products'] = (isset($vendPermission['manage_products']) && $vendPermission['manage_products'])?1:0;
					$response['vpermission']['manage_orders'] = (isset($vendPermission['manage_orders']) && $vendPermission['manage_orders'])?1:0;
					$response['vpermission']['manage_chat'] = (isset($vendPermission['manage_chat']) && $vendPermission['manage_chat'])?1:0;
					$response['vpermission']['manage_supports'] = (isset($vendPermission['manage_supports']) && $vendPermission['manage_supports'])?1:0;
					$response['vpermission']['manage_offers'] = (isset($vendPermission['manage_offers']) && $vendPermission['manage_offers'])?1:0;
					$response['vpermission']['manage_stories'] = (isset($vendPermission['manage_stories']) && $vendPermission['manage_stories'])?1:0;
					$response['vpermission']['manage_ads'] = (isset($vendPermission['manage_ads']) && $vendPermission['manage_ads'])?1:0;
					$response['vpermission']['manage_notifications'] = (isset($vendPermission['manage_notifications']) && $vendPermission['manage_notifications'])?1:0;
					$response['vpermission']['delete_rights'] = (isset($vendPermission['delete_rights']) && $vendPermission['delete_rights'])?1:0;
					$response['vpermission']['can_edit'] = (isset($vendPermission['can_edit']) && $vendPermission['can_edit'])?1:0;
				}
			}
			return response($response, 201);
		}
	}

	public function syncData(Request $request)
	{
		if(current_user()){
			$allCarts = [];
			$cardCount = 0;
			if($request->cart){
				$cart = (array) json_decode($request->cart, true);
				if(count($cart)){
					$allCarts = $cart;
					// $cardCount = count($cart);
					foreach($cart as $vcart){
						foreach($vcart as $scart){
							foreach($scart as $vcart){
								$cardCount++;
							}
						}
					}
				}
				if(current_user()->cart->count()){
					foreach(current_user()->cart as $ucart){
						$productInfo = $ucart->product;
						if($productInfo->status == 1 && $productInfo->vstatus == 1){
							if(!isset($allCarts['v'.$productInfo->vendor_id])){
								$allCarts['v'.$productInfo->vendor_id] = [];
							}
							if(!isset($allCarts['v'.$productInfo->vendor_id]['p'.$ucart->product_id])){
								$allCarts['v'.$productInfo->vendor_id]['p'.$ucart->product_id] = [];
							}
							if(!isset($allCarts['v'.$productInfo->vendor_id]['p'.$ucart->product_id]['v'.$ucart->variant_id])){
								$cardCount++;
							}
							$allCarts['v'.$productInfo->vendor_id]['p'.$ucart->product_id]['v'.$ucart->variant_id] = [
								'id' => $ucart->product_id,
								'variant' => $ucart->variant_id,
								"qty" => $ucart->qty,
								"personalize" => isset($ucart->personalize) ? $ucart->personalize : '',
							];
						}
					}
				}
				if(count($cart)){
					foreach($cart as $vcart){
						foreach($vcart as $scart){
							foreach($scart as $vcart){
								$prodPersonalize = isset($vcart['personalize']) ? $vcart['personalize'] : '';
								ShoppingCart::updateOrCreate(
									['product_id' => $vcart['id'], 'variant_id' => $vcart['variant'], 'user_id'=> current_user()->id],
									['qty' => $vcart['qty'], 'personalize' => $prodPersonalize]
								);
							}
						}
					}
				}
			}
			$allWishlist = [];
			if($request->wishlist){
				$wishlist = (array) json_decode($request->wishlist, true);
				if(count($wishlist)){
					$allWishlist = $wishlist;
				}
				if(current_user()->wishlist->count()){
					foreach(current_user()->wishlist as $uwishlist){
						$allWishlist['p'.$uwishlist->product_id] = $uwishlist->product_id;
					}
				}
				if(count($wishlist)){
					foreach($wishlist as $swishlist){
						WishList::updateOrCreate(
							['product_id' => $swishlist, 'variant_id' => 0, 'user_id'=> current_user()->id],
						);
					}
				}
			}
			return [
				'status'=>'success',
				'cart' => $allCarts,
				'cartCount' => $cardCount,
				'wishlist' => $allWishlist,
			];
		}
		return ['status'=>'error'];
	}

	public function syncCart(Request $request)
	{
		if(current_user()){
			// return $request;
			if(is_numeric($request->id) && is_numeric($request->var_id)){
				if($request->action == 'true'){
					ShoppingCart::updateOrCreate(
						['product_id' => $request->id, 'variant_id' => $request->var_id, 'user_id'=> current_user()->id],
						['qty' => $request->qty, 'personalize' => $request->personalize]
					);
				} else {
					$thisCart = current_user()->cart()->where([['product_id', $request->id],['variant_id', $request->var_id]])->first();
					if($thisCart){
						$thisCart->delete();
					}
				}
				return ['status'=>'success'];
			}
		}
		return ['status'=>'error'];
	}

	public function syncWishlist(Request $request)
	{
		if(current_user()){
			if(is_numeric($request->id)){
				if($request->action == 'true'){
					WishList::updateOrCreate(
						['product_id' => $request->id, 'variant_id' => 0, 'user_id'=> current_user()->id],
					);
				} else {
					$thisWish = current_user()->wishlist()->where('product_id', $request->id)->first();
					if($thisWish){
						$thisWish->delete();
					}
				}
				return ['status'=>'success'];
			}
		}
		return ['status'=>'error'];
	}

	public function changePassword(Request $request)
	{
		$validatedDate = $request->validate([
            'oldpassword' => ['required', new MatchOldPassword],
            'newpassword' => 'required',
            'confirmpassword' => 'required|same:newpassword',
        ]);
		User::find(auth()->user()->id)->update(['password'=> Hash::make($request->newpassword)]);
		return ['status'=>'success'];
	}
	
	public function forgetPassword(Request $request)
	{
		$fields = $request->validate([
			'email' => 'required|email',
		]);
		$response = Password::sendResetLink(['email' => $request->email]);
		if($response == 'passwords.sent'){
			return ['status'=>'success'];
		}
		return ['status'=>'error'];
	}
	
	public function registerUser(Request $request)
	{
		$fields = $request->validate([
			'email' => 'required|email',
			'password' => 'required',
			'confirmpassword' => 'required|same:password',
			'firstName' => 'required',
			'lastName' => 'required',
			'phone' => 'required',
			'address' => 'required',
			'country' => 'required',
			'city' => 'required',
		]);
		$checkUser = User::where('email', '=', $request->email)->count();
		if(!$checkUser){
			$zipcode = ($request->zipcode) ? $request->zipcode : '';
			$password = Hash::make($request->password);
			$user = new User();
			$user->email = $request->email;
			$user->password = $password;
			$user->first_name = $request->firstName;
			$user->last_name = $request->lastName;
			$user->address = $request->address;
			$user->phone = $request->phone;
			$user->zipcode = $zipcode;
			$user->country_id = $request->country;
			$user->city = $request->city;
			$user->save();
			$user->sendEmailVerificationNotification();
			return ['status'=>'success'];
		}
		return ['status'=>'error'];
	}
	
	public function getUserInfo()
	{
		return [
			'first_name' => current_user()->first_name,
			'last_name' => current_user()->last_name,
			'email' => current_user()->email,
			'phone' => current_user()->phone,
			'address' => current_user()->address,
			'zipcode' => current_user()->zipcode,
			'country' => current_user()->country_id,
			'city' => current_user()->city,
		];
	}
	
	public function setUserInfo(Request $request)
	{
		$fields = $request->validate([
			'firstName' => 'required',
			'lastName' => 'required',
			'email' => 'required|email',
			'phone' => 'required',
			'address' => 'required',
			'country' => 'required',
			'city' => 'required',
		]);
		$zipcode = ($request->zipcode) ? $request->zipcode : '';
		$user = current_user();
		$user->first_name = $request->firstName;
		$user->last_name = $request->lastName;
		$user->email = $request->email;
		$user->phone = $request->phone;
		$user->address = $request->address;
		$user->zipcode = $zipcode;
		$user->country_id = $request->country;
		$user->city = $request->city;
		$user->save();
		
		return [
			'status'=>'success',
			'first_name' => current_user()->first_name,
			'last_name' => current_user()->last_name,
			'email' => current_user()->email,
			'phone' => current_user()->phone,
			'address' => current_user()->address,
			'zipcode' => current_user()->zipcode,
			'country' => current_user()->country()->name,
			'city' => (is_numeric(current_user()->city))?current_user()->cities->name :current_user()->city,
		];
	}

    public function getOrders(Request $request)
    {
        $orders = current_user()->orders;
        return OrderResource::collection($orders);
    }

    public function getOrderDetail(Request $request, $id)
    {
        if(is_numeric($id)) {
            $order = current_user()->orders->where('id', $id)->first();
            $orderV = [];
            /*foreach($order->ordervendor as $vendor){
                $orderVendor = [
                    'name' => $vendor->vendor->name,
                    'transport' => $vendor->transport * 1,
                    'value' => $vendor->value * 1,
                    'total' => ($vendor->value + $vendor->transport),
                    'products' => $vendor->details->toArray(),
                ];
                array_push($orderV, $orderVendor);
            }*/
            if($order){
                return new OrderFResource($order);
            }
        }
        abort(404);
    }

    public function getTickets(Request $request)
    {
        $tickets = current_user()->tickets;
        return TicketsResource::collection($tickets);
    }

    public function getOrderTicketDetail(Request $request, $id)
    {
        if(is_numeric($id)) {
            $order = current_user()->orders->where('id', $id)->first();
			$reasons = [
				["id" =>"1", "name" => 'Porosia nuk ka mbërritur'],
				["id" =>"2", "name" => 'Probleme me Produktin'],
				["id" =>"3", "name" => 'Kërkesë për Rimbursim'],
				["id" =>"9", "name" => 'Tjetër'],
			];
			return [
				'id' => $order->id,
				'vendor' => OrderVendorsResource::collection($order->ordervendor),
				'reasons' => $reasons
			];
        }
        abort(404);
    }

    public function getTicketDetail(Request $request, $id)
    {
        if(is_numeric($id)) {
            $ticket = current_user()->tickets->where('id', $id)->first();
			return new TicketSingleResource($ticket);
        }
        abort(404);
    }

	public function newTicket(Request $request, $id)
	{
		if(is_numeric($id)){
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
			$fileName = $request->filename;
			$fileNameE = explode('\\', $fileName);
			$fileNameLength = count($fileNameE);
			if(!$fileNameLength){
				$fileNameE = explode('/', $fileName);
				$fileNameLength = count($fileNameE);
			}
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
			if($request->file && $request->file != 'null'){
				$image_64 = $request->file;
				$extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
				$replace = substr($image_64, 0, strpos($image_64, ',')+1); 
				$image = str_replace($replace, '', $image_64); 
				$image = str_replace(' ', '+', $image); 
				if($fileNameLength){
					$imageName = $fileNameE[$fileNameLength-1];
				} else {
					$imageName = Str::random(10).'.'.$extension;
				}
				$exists = Storage::disk('local')->exists('photos/ticket/'.$imageName);
				if ($exists) {
					$increment = 0;
					if (preg_match('/(^.*?)+(?:\((\d+)\))?(\.(?:\w){0,3}$)/si', $imageName, $regs)){
						$filename = $regs[1];
						$fileext = $regs[3];
						$this->name = $filename.$fileext;
						while(Storage::disk('local')->exists('photos/ticket/'.$imageName)) {
							$increment++;
							$imageName = $filename.$increment.$fileext;
						}
					}
				}
				Storage::disk('local')->put('photos/ticket/'.$imageName, base64_decode($image));
				
				$attachments = new TicketAttachment();
				$attachments->ticket_id = $ticket->id;
				$attachments->message_id = 0;
				$attachments->file = $imageName;
				$attachments->save();
			}
			return ['status'=>'success'];
		}
		return ['status'=>'error'];
	}
	
	public function addTicketDetail(Request $request, $id)
    {
        if(is_numeric($id)) {
			$validatedDate = $request->validate([
				'message' => 'required',
			]);
			$ticket = new TicketMessage();
			$ticket->ticket_id = $id;
			$userId = current_user()->id;
			$ticket->user_id = $userId;
			$ticket->way = 1;
			$ticket->message = $request->message;
			$ticket->save();
			if($request->file && $request->file != 'null'){
				$fileName = $request->filename;
				$fileNameE = explode('\\', $fileName);
				$fileNameLength = count($fileNameE);
				if(!$fileNameLength){
					$fileNameE = explode('/', $fileName);
					$fileNameLength = count($fileNameE);
				}
				$image_64 = $request->file;
				$extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
				$replace = substr($image_64, 0, strpos($image_64, ',')+1); 
				$image = str_replace($replace, '', $image_64); 
				$image = str_replace(' ', '+', $image); 
				if($fileNameLength){
					$imageName = $fileNameE[$fileNameLength-1];
				} else {
					$imageName = Str::random(10).'.'.$extension;
				}
				$exists = Storage::disk('local')->exists('photos/ticket/'.$imageName);
				if ($exists) {
					$increment = 0;
					if (preg_match('/(^.*?)+(?:\((\d+)\))?(\.(?:\w){0,3}$)/si', $imageName, $regs)){
						$filename = $regs[1];
						$fileext = $regs[3];
						$this->name = $filename.$fileext;
						while(Storage::disk('local')->exists('photos/ticket/'.$imageName)) {
							$increment++;
							$imageName = $filename.$increment.$fileext;
						}
					}
				}
				Storage::disk('local')->put('photos/ticket/'.$imageName, base64_decode($image));
				
				$attachments = new TicketAttachment();
				$attachments->ticket_id = $id;
				$attachments->message_id = $ticket->id;
				$attachments->file = $imageName;
				$attachments->save();
			}
			$supportTicket = Ticket::findorfail($id);
			if($supportTicket->status == 3){
                $supportTicket->status = 4;
                $supportTicket->save();
            }
			return ['status'=>'success'];
        }
    }

    public function getAddresses()
    {
        $addresses = current_user()->addresses->where('udelete', '=', 0);
        //return $addresses->values()->toArray();
		return AddressResource::collection($addresses);
    }

    public function getUAddresses(Request $request)
    {
		if($request->countryId){
			$addresses = current_user()->addresses->where('udelete', '=', 0)->where('country_id', '=', $request->countryId);
			$addressesCount = $addresses->count();
			$addressesP = current_user()->addresses->where('udelete', '=', 0)->where('primary', '=', 1)->where('country_id', '=', $request->countryId)->first();
			if($addressesCount == 1){
				return [
					'count' => 1,
					'primary' => new AddressResource($addresses->first()),
					'addresses' => AddressResource::collection($addresses),
				];
			}
			if($addressesP){
				return [
					'count' => $addressesCount,
					'primary' => new AddressResource($addressesP),
					'addresses' => AddressResource::collection($addresses),
				];
			} else {
				return [
					'count' => $addressesCount,
					'primary' => NULL,
					'addresses' => AddressResource::collection($addresses),
				];
			}
			return ['test'=>'tes'];
		}
		return [
			'count' => 0,
			'primary' => NULL,
			'addresses' => [],
		];
    }

    public function getAddressDetail(Request $request, $id)
    {
        if(is_numeric($id)) {
            $address = current_user()->addresses->where('id', $id)->first();
            if($address){
                //return $address;
				return [
					'id' => $address->id,
					'name' => $address->name,
					'phone' => $address->phone,
					'address' => $address->address,
					'zipcode' => $address->zipcode,
					'country_id' => $address->country_id,
					'city' => ((is_numeric($address->city))? $address->city*1 : $address->city),
					'primary' => $address->primary,
				];
            }
        }
        abort(404);
    }
	
	public function primaryAddress(Request $request, $id)
	{
		if(is_numeric($id)) {
			current_user()->addresses()->where('primary', 1)->update(['primary'=>0]);
			$address = current_user()->addresses->where('id', $id)->first();
			$address->primary = 1;
			$address->save();
		}
		return ['status'=>'success'];
	}
	
	public function addAddress(Request $request)
	{
		$validatedDate = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'country' => 'required',
        ]);
		$zipcode = ($request->zipcode) ? $request->zipcode : '';
		$address = new UserAddress();
        $address->user_id = current_user()->id;
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->address = $request->address;
        $address->address2 = '';
        $address->zipcode = $zipcode;
        $address->city = $request->city;
        $address->country_id = $request->country;
        $address->save();
		return ['status'=>'success'];
	}
	
	public function editAddress(Request $request, $id)
	{
		if(is_numeric($id)) {
			$validatedDate = $request->validate([
				'name' => 'required',
				'address' => 'required',
				'phone' => 'required',
				'city' => 'required',
				'country' => 'required',
			]);
			$zipcode = ($request->zipcode) ? $request->zipcode : '';
			$address = UserAddress::findOrFail($id);
			$address->name = $request->name;
			$address->phone = $request->phone;
			$address->address = $request->address;
			$address->zipcode = $zipcode;
			$address->city = $request->city;
			$address->country_id = $request->country;
			$address->save();
			return ['status'=>'success'];
		}
	}
	
	public function deleteAddress($id)
	{
		if(is_numeric($id)){
			$address = UserAddress::findOrFail($id);
			$address->udelete = 1;
            $address->save();
			return ['status'=>'success'];
		}
	}
}