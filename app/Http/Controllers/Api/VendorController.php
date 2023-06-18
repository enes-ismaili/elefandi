<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use App\Models\TransportInfo;
use App\Models\ProductVariant;
use App\Models\ProductShipping;
use App\Models\SocialLink;
use App\Models\WorkHour;
use App\Models\OrderVendor;
use App\Models\OrderTrack;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Models\Story;
use App\Models\StoryItem;
use App\Models\Offer;
use App\Models\OfferDetail;
use App\Models\Coupon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Resources\ShippingResource;
use App\Http\Resources\TicketsVResource;
use App\Http\Resources\TicketSingleResource;
use App\Http\Resources\OrderVendorResource;
use App\Http\Resources\OrderVendorFResource;
use App\Http\Resources\VendorStoriesResource;
use App\Http\Resources\VendorStoriesItemResource;
use App\Http\Resources\OfferDetailsProductResource;
use App\Http\Resources\OfferResource;
use App\Http\Resources\CouponResource;
use App\Http\Resources\CouponDetailsProductResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderTrack as OrderTrackMail;
use App\Rules\UniqueCoupon;

class VendorController extends BaseController
{
    public function getVendorSettings()
    {
        if(current_vendor()){
            $vendor = current_vendor();
            return [
                'name' => $vendor->name,
                'email' => $vendor->email,
                'description' => $vendor->description,
                'address' => $vendor->address,
                'phone' => $vendor->phone,
                'zipcode' => $vendor->zipcode,
                'city' => $vendor->city,
                'country_id' => $vendor->country_id,
                'facebook' => ($vendor->socials()->where('name', 'facebook')->first()) ? $vendor->socials()->where('name', 'facebook')->first()->links : '',
                'twitter' => ($vendor->socials()->where('name', 'twitter')->first()) ? $vendor->socials()->where('name', 'twitter')->first()->links : '',
                'instagram' => ($vendor->socials()->where('name', 'instagram')->first()) ? $vendor->socials()->where('name', 'instagram')->first()->links : '',
                'youtube' => ($vendor->socials()->where('name', 'youtube')->first()) ? $vendor->socials()->where('name', 'youtube')->first()->links : '',
                'monday' => $vendor->workhour->monday,
                'monday_start' => $vendor->workhour->monday_start,
                'monday_end' => $vendor->workhour->monday_end,
                'tuesday' => $vendor->workhour->tuesday,
                'tuesday_start' => $vendor->workhour->tuesday_start,
                'tuesday_end' => $vendor->workhour->tuesday_end,
                'wednesday' => $vendor->workhour->wednesday,
                'wednesday_start' => $vendor->workhour->wednesday_start,
                'wednesday_end' => $vendor->workhour->wednesday_end,
                'thursday' => $vendor->workhour->thursday,
                'thursday_start' => $vendor->workhour->thursday_start,
                'thursday_end' => $vendor->workhour->thursday_end,
                'friday' => $vendor->workhour->friday,
                'friday_start' => $vendor->workhour->friday_start,
                'friday_end' => $vendor->workhour->friday_end,
                'saturday' => $vendor->workhour->saturday,
                'saturday_start' => $vendor->workhour->saturday_start,
                'saturday_end' => $vendor->workhour->saturday_end,
                'sunday' => $vendor->workhour->sunday,
                'sunday_start' => $vendor->workhour->sunday_start,
                'sunday_end' => $vendor->workhour->sunday_end,
            ];
        }
    }
	public function saveVendorSettings(Request $request)
    {
		if(current_vendor() && mcheck_permissions('manage_vendor')){
			$fields = $request->validate([
				'email' => 'required',
				'address' => 'required',
				'phone' => 'required',
				'city' => 'required',
				'country' => 'required',
			]);
			$zipcode = ($request->zipcode) ? $request->zipcode : '';
			$vendor = current_vendor();
			$vendor->email = $request->email;
			//$vendor->description = $request->description;
			$vendor->address = $request->address;
			$vendor->phone = $request->phone;
			$vendor->zipcode = $zipcode;
			$vendor->city = $request->city;
			$vendor->country_id = $request->country;
			if($request->facebook){
				$socials = SocialLink::updateOrCreate(
					['vendor_id' => $vendor->id, 'name'=> 'facebook'],
					['links' => $request->facebook]
				);
			}
			if($request->twitter){
				$socials = SocialLink::updateOrCreate(
					['vendor_id' => $vendor->id, 'name'=> 'twitter'],
					['links' => $request->twitter]
				);
			}
			if($request->instagram){
				$socials = SocialLink::updateOrCreate(
					['vendor_id' => $vendor->id, 'name'=> 'instagram'],
					['links' => $request->instagram]
				);
			}
			if($request->youtube){
				$socials = SocialLink::updateOrCreate(
					['vendor_id' => $vendor->id, 'name'=> 'youtube'],
					['links' => $request->youtube]
				);
			}
			$workHour = WorkHour::updateOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'monday' => (($request->monday)?1:0), 'monday_start' => $request->monday_start, 'monday_end'=> $request->monday_end,
                    'tuesday' => (($request->tuesday)?1:0), 'tuesday_start' => $request->tuesday_start, 'tuesday_end'=> $request->tuesday_end,
                    'wednesday' => (($request->wednesday)?1:0), 'wednesday_start' => $request->wednesday_start, 'wednesday_end'=> $request->wednesday_end,
                    'thursday' => (($request->thursday)?1:0), 'thursday_start' => $request->thursday_start, 'thursday_end'=> $request->thursday_end,
                    'friday' => (($request->friday)?1:0), 'friday_start' => $request->friday_start, 'friday_end'=> $request->friday_end,
                    'saturday' => (($request->saturday)?1:0), 'saturday_start' => $request->saturday_start, 'saturday_end'=> $request->saturday_end,
                    'sunday' => (($request->sunday)?1:0), 'sunday_start' => $request->sunday_start, 'sunday_end'=> $request->sunday_end,
                ]
            );
			$vendor->save();
			return ['status'=>'success'];
		}
	}
	
	public function getProduct($id)
    {
		if(is_numeric($id)){
			$product = Product::findOrFail($id);
			$productShipping = $product->shippings->where('country_id', '<', 4)->sortBy('country_id');
			return [
				'name' => $product->name,
				'weight' => $product->weight,
				'size' => $product->size,
				'personalizetitle' => $product->personalize,
				'personalizetitleToogle' => (($product->personalize) ? 'true' : 'false'),
				'price' => $product->price,
				'sku' => $product->sku,
				'stock' => $product->stock,
				'variants' => $product->variants,
				'shippings' => ShippingResource::collection($productShipping),
				'transport' => TransportInfo::all(),
			];
		}
	}
	
	public function saveProduct(Request $request, $id)
	{
		if(is_numeric($id)){
			$product = Product::findOrFail($id);
			if(mcheck_permissions('manage_products') && $product->vendor_id == current_vendor()->id){
				
				//return count(json_decode($request->shippings));
				$fields = $request->validate([
					'name' => 'required|string',
					'weight' => 'required',
					'size' => 'required',
					'price' => 'required|min:1',
					'stock' => 'required',
				]);
				$product->name = $request->name;
				$product->weight = $request->weight;
				$product->size = $request->size;
				if($request->personalizetitleToogle == 'true' && $request->personalizetitle){
					$product->personalize = $request->personalizetitle;
				} else {
					$product->personalize = '';
				}
				if($request->price > 0){
					$product->price = $request->price;
				}
				if($request->stock >= 0){
					$product->stock = $request->stock;
				}
				$product->sku = $request->sku;
				//return count($request->variants);
				if(count(json_decode($request->variants))){
					foreach(json_decode($request->variants) as $variant){
						if($variant->product_id == $id){
							$cVariant = ProductVariant::findOrFail($variant->id);
							if($variant->price <= 0){
								$cVariant->price = 0;
							} else {
								$cVariant->price = $variant->price;
							}
							$cVariant->stock = $variant->stock;
							$cVariant->save();
						}
					}
				}
				if(count(json_decode($request->shippings))){
					foreach(json_decode($request->shippings) as $shipping){
						$cShipping = ProductShipping::findOrFail($shipping->id);
						if($cShipping->product_id == $id){
							if($shipping->cost < 0){
								$cShipping->free = 1;
								$cShipping->cost = 0;
							} else {
								$cShipping->free = $shipping->free;
								$cShipping->cost = $shipping->cost;
							}
							$cShipping->shipping = $shipping->shipping;
							$cShipping->shipping_time = $shipping->shipping_time;
							$cShipping->save();
						}
					}
				}
				$product->save();
				return ['status'=>'success'];
			}
		}
	}
    
    public function getVOrders(Request $request)
    {
        if(current_vendor()){
            $orders = current_vendor()->orders()->orderBy('id', 'DESC')->get();
            return OrderVendorResource::collection($orders);
        }
    }

    public function getVOrderDetail(Request $request, $id)
    {
        if(is_numeric($id)) {
            $order = current_vendor()->orders->where('order_id', $id)->first();
            if($order){
                return new OrderVendorFResource($order);
            }
        }
        abort(404);
    }
	
	public function changeVOrder(Request $request, $id)
	{
		if(is_numeric($id) && is_numeric($request->status)) {
            $order = current_vendor()->orders->where('order_id', $id)->first();
			//return $order;
            if($order){
				$order->status = $request->status;
				$order->save();
				if($order->order->ordervendor()->where('status', '=', 0)->count() == 0){
					if($request->status == 1){
						if($order->order->ordervendor()->where('status', '=', 2)->count()){
							$order->order->status = 3;
							$order->order->save();
						} else {
							$order->order->status = 1;
							$order->order->save();
						}
					} else {
						if($order->order->ordervendor()->where('status', '=', 1)->count()){
							$order->order->status = 3;
							$order->order->save();
						} else {
							$order->order->status = 2;
							$order->order->save();
						}
					}
				} else {
					$order->order->status = 0;
					$order->order->save();
				}
				return ['status'=>'success'];
            }
        }
	}
	public function trackVOrder(Request $request, $id)
	{
		$validatedDate = $request->validate([
			'comment' => 'required'
		], [
			'comment.required' => 'Mesazhi është i detyrueshëm'
		]);
		if(is_numeric($id) && mcheck_permissions('manage_orders')) {
			$orderB = current_vendor()->orders->where('order_id', $id)->first();
			$tracking = new OrderTrack();
            //$orderId = OrderVendor::find($id);
            $tracking->order_id = $orderB->order_id;
            $tracking->order_vendor_id = $orderB->id;
            $tracking->comment = $request->comment;
            $tracking->save();
            $order = $orderB->order;
            $user = $order->user;
			//return $user;
            Mail::to($user->email)->send(new OrderTrackMail($user, $orderB, $tracking));
            // Mail::to('e.dalipi@codeit.al')->send(new OrderTrackMail($user, $orderB, $tracking));
			return ['status'=>'success'];
		}
		return ['status'=>'error'];
	}

    public function getTickets(Request $request)
    {
        $tickets = current_vendor()->tickets;
        return TicketsVResource::collection($tickets);
    }

    public function getTicketDetail(Request $request, $id)
    {
        if(is_numeric($id)) {
            $ticket = current_vendor()->tickets->where('id', $id)->first();
			if($ticket){
				return new TicketSingleResource($ticket);
			}
        }
    }

    public function addTicketDetail(Request $request, $id)
    {
        if(is_numeric($id) && mcheck_permissions('manage_supports')) {
			$validatedDate = $request->validate([
				'message' => 'required',
			], [
				'comment.required' => 'Mesazhi është i detyrueshëm'
			]);
			
			$fileName = $request->filename;
			$fileNameE = explode('\\', $fileName);
			$fileNameLength = count($fileNameE);
			if(!$fileNameLength){
				$fileNameE = explode('/', $fileName);
				$fileNameLength = count($fileNameE);
			}
			$ticket = new TicketMessage();
			$ticket->ticket_id = $id;
			$userId = current_vendor()->id;
			$ticket->user_id = $userId;
			$ticket->way = 2;
			$ticket->message = $request->message;
			$ticket->save();
			if($request->file){
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
				//$imageName = Str::random(10).'.'.$extension;
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
			if($supportTicket->status < 3 && $request->closeTicket == true) {
				$supportTicket->status = 3;
				$supportTicket->save();
			}
			return ['status'=>'success'];
        }
    }
	
	public function refundTicketDetail(Request $request, $id)
    {
		if(is_numeric($id)) {
			if(mcheck_permissions('manage_supports')){
				$supportTicket = Ticket::findorfail($id);
                $supportTicket->status = 7;
                $supportTicket->save();
                $order = OrderVendor::where([
                    ['order_id', '=', $supportTicket->order_id],
                    ['vendor_id', '=', $supportTicket->vendor_id],
                ])->first();
                $order->status = 3;
                $order->save();
				return ['status'=>'success'];
			}
		}
	}

	public function getVStories(Request $request)
	{
		if(current_vendor()){
            // $stories = current_vendor()->stories()->orderBy('id', 'DESC')->get();
			$story = current_vendor()->storie;
			if(current_vendor()->slimit && current_vendor()->slimit > 0){
                $slimit = current_vendor()->silimit;
            } else {
                $slimit = Setting::where('name', '=', 'storylimitItem')->first();
                if(!$slimit){
                    $slimit = 2;
                } else {
                    $slimit = $slimit->value;
                }
            }
			$limitLeft = (count($story->items->where('end_story', '>', date('Y-m-d H:i:s'))) < $slimit) ? false : true;
			$aItems = $story->items->where('end_story', '>', date('Y-m-d H:i:s'));
			$oldItems = $story->items->where('end_story', '<', date('Y-m-d H:i:s'));
            return [
				'id' => $story->id,
				'name' => $story->name,
				'limit' => $limitLeft,
				'items' => VendorStoriesItemResource::collection($aItems),
				'oitems' => VendorStoriesItemResource::collection($oldItems),
			];
            // return VendorStoriesResource::collection($story->items);
        }
	}

	public function getVStoryDetail(Request $request, $id)
	{
		if(current_vendor() && is_numeric($id)) {
            $story = current_vendor()->stories->where('id', $id)->first();
            if($story){
				return [
					'id' => $story->id,
					'name' => $story->name,
					'items' => VendorStoriesItemResource::collection($story->items),
				];
            }
        }
        abort(404);
	}

	public function addVStory(Request $request, $id)
	{
		if(current_vendor() && is_numeric($id)) {
			$validatedDate = $request->validate([
				'name' => 'required',
			], [
				'name.required' => 'Emri i Story është i detyrueshëm'
			]);
			if($id == 0){
				$message = 'Story u shtua me sukses';
				$stories = new Story();
				$stories->name = $request->name;
				$stories->vendor_id = current_vendor()->id;
				$stories->save();
			} else {
				$message = 'Story u ndryshua me sukses';
				$stories = Story::findOrFail($id);
				$stories->name = $request->name;
				$stories->save();
			}
			return ['status'=>'success', 'message'=>$message];
		}
		return ['status'=>'error'];
	}

	public function getVItemDetail($id)
	{
		if(current_vendor() && is_numeric($id)) {
			$item = StoryItem::findOrFail($id);
			if($item->main->vendor_id == current_vendor()->id){
				// return new VendorStoriesItemResource($item);
				return [
					'item' => new VendorStoriesItemResource($item),
					'active' => (($item->end_story > date('Y-m-d H:i:s')) ? false : true),
				];
			}
		}
	}

	public function postVItemDetail(Request $request, $id)
	{
		if(current_vendor() && is_numeric($id)) {
			$validatedDate = $request->validate([
				'storyType' => 'required',
				'name' => 'required',
				'link' => 'required',
			]);
			$story = StoryItem::findOrFail($id);
			if($story->main->vendor_id == current_vendor()->id){
				$changeStatus = false;
                $story->name = $request->name;
                if($story->link != $request->link){
                    $story->link = $request->link;
                    $changeStatus = true;
                }
                $story->length = $request->duration;
				$fileFormat = 0;
				if($request->file && $request->file != 'null'){
					$fileName = $request->filename;
					$fileNameE = explode('\\', $fileName);
					$fileNameLength = count($fileNameE);
					if(!$fileNameLength){
						$fileNameE = explode('/', $fileName);
						$fileNameLength = count($fileNameE);
					}
					$image_64 = $request->file;
					$extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1]);
					if(count($extension)){
						if($extension[0] == 'video'){
							$fileFormat = 2;
						} elseif($extension[0] == 'image'){
							$fileFormat = 1;
						}
						$replace = substr($image_64, 0, strpos($image_64, ',')+1); 
						$image = str_replace($replace, '', $image_64); 
						$image = str_replace(' ', '+', $image); 
						if($fileNameLength){
							$imageName = $fileNameE[$fileNameLength-1];
						} else {
							$imageName = Str::random(10).'.'.$extension;
						}
						$exists = Storage::disk('local')->exists('photos/story/'.$imageName);
						if ($exists) {
							$increment = 0;
							if (preg_match('/(^.*?)+(?:\((\d+)\))?(\.(?:\w){0,3}$)/si', $imageName, $regs)){
								$filename = $regs[1];
								$fileext = $regs[3];
								$this->name = $filename.$fileext;
								while(Storage::disk('local')->exists('photos/story/'.$imageName)) {
									$increment++;
									$imageName = $filename.$increment.$fileext;
								}
							}
						}
						Storage::disk('local')->put('photos/story/'.$imageName, base64_decode($image));
					}
					
				}
                if($request->storyType == 2){
					if($story->type != 2 && $fileFormat == 0){
						return ['status'=>'error'];
					}
                    $story->type = 2;
					if($fileFormat == 2){
						$story->image = $imageName;
                        $changeStatus = true;
					}
                } else {
					if($story->type != 1 && $fileFormat == 0){
						return ['status'=>'error'];
					}
                    $story->type = 1;
                    if($fileFormat == 1){
						$story->image = $imageName;
                        $changeStatus = true;
					}
                }
                if($changeStatus && $story->cactive != 0){
                    $story->cactive = 2;
					$storyMain = Story::findOrFail($story->stories_id);
                    $storyMain->needaction = 1;
                    $storyMain->save();
                }
                $story->save();
				return ['status'=>'success'];

				// return $request;
			}
		}
		return ['status'=>'error'];
	}

	

	public function postVAddItemDetail(Request $request, $id)
	{
		if(current_vendor() && is_numeric($id)) {
			$validatedDate = $request->validate([
				'storyType' => 'required',
				'name' => 'required',
				'link' => 'required',
			]);
			$storyMain = Story::findOrFail($id);
			$story = new StoryItem();
			if($storyMain->vendor_id == current_vendor()->id){
                $story->name = $request->name;
				$story->link = $request->link;
				$story->stories_id = $id;
                $story->length = $request->duration;
				$fileFormat = 0;
				if($request->file && $request->file != 'null'){
					$fileName = $request->filename;
					$fileNameE = explode('\\', $fileName);
					$fileNameLength = count($fileNameE);
					if(!$fileNameLength){
						$fileNameE = explode('/', $fileName);
						$fileNameLength = count($fileNameE);
					}
					$image_64 = $request->file;
					$extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1]);
					if(count($extension)){
						if($extension[0] == 'video'){
							$fileFormat = 2;
						} elseif($extension[0] == 'image'){
							$fileFormat = 1;
						}
						$replace = substr($image_64, 0, strpos($image_64, ',')+1); 
						$image = str_replace($replace, '', $image_64); 
						$image = str_replace(' ', '+', $image); 
						if($fileNameLength){
							$imageName = $fileNameE[$fileNameLength-1];
						} else {
							$imageName = Str::random(10).'.'.$extension;
						}
						$exists = Storage::disk('local')->exists('photos/story/'.$imageName);
						if ($exists) {
							$increment = 0;
							if (preg_match('/(^.*?)+(?:\((\d+)\))?(\.(?:\w){0,3}$)/si', $imageName, $regs)){
								$filename = $regs[1];
								$fileext = $regs[3];
								$this->name = $filename.$fileext;
								while(Storage::disk('local')->exists('photos/story/'.$imageName)) {
									$increment++;
									$imageName = $filename.$increment.$fileext;
								}
							}
						}
						Storage::disk('local')->put('photos/story/'.$imageName, base64_decode($image));
					}
					
				}
                if($request->storyType == 2){
                    $story->type = 2;
					if($fileFormat == 2){
						$story->image = $imageName;
					} else {
						return ['status'=>'error'];
					}
                } else {
                    $story->type = 1;
                    if($fileFormat == 1){
						$story->image = $imageName;
					} else {
						return ['status'=>'error'];
					}
                }
				$story->cactive = 2;
				$storyMain->needaction = 1;
				$storyMain->save();
                $story->save();
				return ['status'=>'success'];

				// return $request;
			}
		}
		return ['status'=>'error'];
	}

	public function deleteVItemDetail(Request $request, $id)
	{
		if(is_numeric($id) && mcheck_permissions('delete_rights')){
			$story = StoryItem::findOrFail($id);
			$story->delete();
			return ['status'=>'success'];
		}
		return ['status'=>'error'];
	}

	public function getVOffers()
	{
		if(current_vendor()){
            $offers = current_vendor()->offers()->orderBy('id', 'DESC')->get();
            return OfferResource::collection($offers);
        }
	}

	public function getVSingleOffers($id)
	{
		if(current_vendor() && is_numeric($id)){
			$offer = current_vendor()->offers->where('id', $id)->first();
			if($offer){
				if($offer->type == 1){
					return [
						'offer' => $offer,
						'selected' => 0,
					];
				} else if($offer->type == 2){
					$categories = Category::where('parent', '=', 0)->get();
					$allCategories = [];
					foreach($categories as $parentCategory){
						$allCategories[] = [
							'id' => $parentCategory->id,
							'name' => $parentCategory->name,
						];
						foreach($parentCategory->children as $category) {
							$allCategories[] = [
								'id' => $category->id,
								'name' => '&nbsp;&nbsp;'.$category->name,
							];
							foreach($category->children as $subCategory) {
								$allCategories[] = [
									'id' => $subCategory->id,
									'name' => '&nbsp;&nbsp;&nbsp;&nbsp;'.$subCategory->name,
								];
							}
						}
					}
					$selectedCatId = 0;
					$selectedCat = $offer->details()->first();
					if($selectedCat){
						$selectedCatId = $selectedCat->prod_id;
					}
					return [
						'offer' => $offer,
						'categories' => $allCategories,
						'selected' => $selectedCatId,
					];
				} else {
					$products = current_vendor()->products()->select('id', 'name', 'price')->with(['variants'=>function($query){$query->select('id', 'product_id','name', 'price');}])->get();
					$offerProducts =  $offer->details()->get()->unique('prod_id');
					return [
						// 'products' => $products,
						'offer' => $offer,
						'selected' => OfferDetailsProductResource::collection($offerProducts),
					];
				}
			}
		}
	}

	public function postVSingleOffers(Request $request, $id)
	{
		if(is_numeric($id) && current_vendor()){
			$validatedDate = $request->validate([
				'name' => 'required',
				'start_date' => 'required',
				'expire_date' => 'required',
			]);
			$offer = Offer::findOrFail($id);
			if($offer){
				if($offer->type == 1){
					$offerDetails = OfferDetail::updateOrCreate(
                        ['vendor_id' => current_vendor()->id, 'offer_id'=> $id, 'type'=> 1, 'prod_id'=> current_vendor()->id],
                        ['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
                    );
				} else if ($offer->type == 2){
					if($request->selcategories){
						$currCategories = OfferDetail::where('offer_id', $id)->update(['active'=> 0]);
						$currCategory = Category::find($request->selcategories);
						if($currCategory){
							$offerDetails = OfferDetail::updateOrCreate(
								['vendor_id' => current_vendor()->id, 'offer_id'=> $id, 'type'=> 2, 'prod_id'=> $currCategory->id],
								['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
							);
							foreach($currCategory->children as $secondLevel){
								$offerDetails = OfferDetail::updateOrCreate(
									['vendor_id' => current_vendor()->id, 'offer_id'=> $id, 'type'=> 2, 'prod_id'=> $secondLevel->id],
									['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
								);
								foreach($secondLevel->children as $thirdLevel){
									$offerDetails = OfferDetail::updateOrCreate(
										['vendor_id' => current_vendor()->id, 'offer_id'=> $id, 'type'=> 2, 'prod_id'=> $thirdLevel->id],
										['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
									);
								}
							}
						}
						OfferDetail::where('offer_id', $id)->where('active', 0)->delete();
					} else {
						return ['status'=>'error'];
					}
				} else if ($offer->type == 3){
					$selProducts =  json_decode($request->selProducts);
					foreach($selProducts as $product){
						if($product->allVariants && count($product->allVariants)){
							foreach($product->allVariants as $variant){
								if($variant->variant){
									if($variant->variant->id){
										$offerDetail = OfferDetail::find($variant->variant->id);
										if($offerDetail){
											if($variant->variant->discount){
												if($offerDetail->discount != $variant->variant->discount){
													$offerDetail->discount = $variant->variant->discount;
													$offerDetail->save();
												}
											} else {
												$offerDetail->delete();
											}
										}
									} else {
										if($variant->variant->discount){
											$offerDetails = OfferDetail::updateOrCreate(
												['vendor_id' => current_vendor()->id, 'offer_id'=> $id, 'type'=> 3, 'prod_id'=> $product->prod_id, 'variant_id'=>$variant->id],
												['action' => 3, 'discount' => $variant->variant->discount, 'active'=> 1]
											);
										}
									}
								}
								
							}
						} else {
							$offerDetail = OfferDetail::find($product->id);
							if($offerDetail && ($offerDetail->prod_id == $product->prod_id) && ($offerDetail->discount != $product->discount)){
								if($product->discount == 0){
									$offerDetail->delete();
								} else {
									$offerDetail->discount = $product->discount;
									$offerDetail->save();
								}
							}
						}
					}
				}
				$offer->name = $request->name;
				$startDate = explode('T', $request->start_date);
				$expireDate = explode('T', $request->expire_date);
				$offer->start_date = Carbon::parse($startDate[0])->format('Y-m-d').' 00:00:05';
				$offer->expire_date = Carbon::parse($expireDate[0])->format('Y-m-d').' 23:59:58';
				$offer->save();
				return ['status'=>'success'];
			}
			return ['status'=>'error'];
		}
	}

	public function addVSingleOffers($id)
	{
		if(current_vendor() && is_numeric($id)){
			return current_vendor()->products()->where('id', $id)->select('id', 'name', 'price')->with(['variants'=>function($query){$query->select('id', 'product_id','name', 'price');}])->first();
		}
	}

	public function storeVSingleOffers(Request $request, $id)
	{
		if(current_vendor()&& is_numeric($id)){
			$validatedDate = $request->validate([
				'name' => 'required',
				'start_date' => 'required',
				'expire_date' => 'required',
			]);
			$selProducts =  json_decode($request->selProducts);
			if($selProducts){
				if(count($selProducts->variants)){
					$dhaveProducts = true;
					$offer = new Offer();
					$vendorId = current_vendor()->id;
					$offer->vendor_id = $vendorId;
					$offer->name = $request->name;
					$offer->type = 3;
					$offer->action = 2;
                    $offer->discount = 0;
					$startDate = explode('T', $request->start_date);
					$expireDate = explode('T', $request->expire_date);
					$offer->start_date = Carbon::parse($startDate[0])->format('Y-m-d').' 00:00:05';
					$offer->expire_date = Carbon::parse($expireDate[0])->format('Y-m-d').' 23:59:58';
					$offer->save();
					foreach($selProducts->variants as $variant){
						if($variant && isset($variant->discount) && $variant->discount){
							$offerDetails = OfferDetail::updateOrCreate(
								['vendor_id' => $vendorId, 'offer_id'=> $offer->id, 'type'=> 3, 'prod_id'=> $selProducts->id, 'variant_id'=>$variant->id],
								['action' => 3, 'discount' => $variant->discount, 'active'=> 1]
							);
							$dhaveProducts = false;
						}
					}
					if($dhaveProducts){
						$offer->delete();
						return ['status'=>'error'];
					}
				} else {
					if($selProducts->discount){
						$offer = new Offer();
						$vendorId = current_vendor()->id;
						$offer->vendor_id = $vendorId;
						$offer->name = $request->name;
						$offer->type = 3;
						$offer->action = 2;
						$offer->discount = 0;
						$startDate = explode('T', $request->start_date);
						$expireDate = explode('T', $request->expire_date);
						$offer->start_date = Carbon::parse($startDate[0])->format('Y-m-d').' 00:00:05';
						$offer->expire_date = Carbon::parse($expireDate[0])->format('Y-m-d').' 23:59:58';
						$offer->save();
						$offerDetail = new OfferDetail();
						$offerDetail->vendor_id = $vendorId;
						$offerDetail->offer_id = $offer->id;
						$offerDetail->prod_id = $selProducts->id;
						$offerDetail->variant_id = 0;
						$offerDetail->action = 3;
						$offerDetail->discount = $selProducts->discount;
						$offerDetail->type = 3;
						$offerDetail->save();
					} else {
						return ['status'=>'error'];
					}
				}

				return ['status'=>'success'];
			}
		}
		return ['status'=>'error'];
	}

	public function deleteVSingleOffers(Request $request, $id)
	{
		if(is_numeric($id) && mcheck_permissions('delete_rights') && current_vendor()){
			$offer = current_vendor()->offers()->where('id', $id)->first();
			if($offer){
				$offer->details()->delete();
				$offer->delete();
				return ['status'=>'success'];
			}
		}
		return ['status'=>'error'];
	}

	public function getVCoupons()
	{
		$coupons = current_vendor()->coupons()->orderBy('id', 'DESC')->get();
		return CouponResource::collection($coupons);
	}

	public function getVScoupon($id)
	{
		if(current_vendor() && is_numeric($id)){
			$coupon = current_vendor()->coupons->where('id', $id)->first();
			if($coupon){
				if($coupon->type == 1){
					return [
						'coupon' => $coupon,
						'selected' => 0,
					];
				} else if($coupon->type == 2){
					$categories = Category::where('parent', '=', 0)->get();
					$allCategories = [];
					foreach($categories as $parentCategory){
						$allCategories[] = [
							'id' => $parentCategory->id,
							'name' => $parentCategory->name,
						];
						foreach($parentCategory->children as $category) {
							$allCategories[] = [
								'id' => $category->id,
								'name' => '&nbsp;&nbsp;'.$category->name,
							];
							foreach($category->children as $subCategory) {
								$allCategories[] = [
									'id' => $subCategory->id,
									'name' => '&nbsp;&nbsp;&nbsp;&nbsp;'.$subCategory->name,
								];
							}
						}
					}
					return [
						'coupon' => $coupon,
						'categories' => $allCategories,
						'selected' => $coupon->categories,
					];
				} else {
					$products = json_decode($coupon->products);
					$allProductsInfo = [];
					if(count($products)){
						$productsStr = '('.implode(',', $products).')';
						$allProducts = current_vendor()->products()->whereIn('id', $products)->get();
						$allProductsInfo = CouponDetailsProductResource::collection($allProducts);
					}
					return [
						'coupon' => $coupon,
						'products' => CouponDetailsProductResource::collection(current_vendor()->products),
						'selected' => $allProductsInfo,
					];
				}
			}
		}
	}

	public function postVScoupon(Request $request, $id)
	{
		if(is_numeric($id) && current_vendor()){
			$validatedDate = $request->validate([
				'code' => ['required', new UniqueCoupon($id)],
				'start_date' => 'required',
				'expire_date' => 'required',
                'discount' => 'required|gt:0',
			]);
			$coupon = Coupon::findOrFail($id);
			if($coupon){
				$coupon->ucode = $request->code;
				$coupon->code = 'v'.current_vendor()->id.'-'.$request->code;
				$coupon->description = $request->description;
				$startDate = explode('T', $request->start_date);
				$expireDate = explode('T', $request->expire_date);
				$coupon->start_date = Carbon::parse($startDate[0])->format('Y-m-d').' 00:00:05';
				$coupon->expire_date = Carbon::parse($expireDate[0])->format('Y-m-d').' 23:59:58';
				if($request->withoffer == 'true'){
					$coupon->withoffer = 1;
				} else {
					$coupon->withoffer = 0;
				}
				if($coupon->type == 1){
					$coupon->discount = $request->discount;
					$coupon->action = 1;
				} else if ($coupon->type == 2){
					$coupon->action = 1;
					if(is_array(json_decode($request->selectedCategories))){
						$coupon->categories = $request->selectedCategories;
					} else {
						$coupon->categories = '['.$request->selectedCategories.']';
					}
				} else if ($coupon->type == 3){
					$coupon->action = $request->action;
					$selProducts =  json_decode($request->selProducts);
					$selectedProducts = [];
					if(count($selProducts)){
						foreach($selProducts as $product){
							$selectedProducts[] = $product->id;
						}
					}
					$coupon->products = json_encode($selectedProducts);
				}
				$coupon->save();
				return ['status'=>'success'];
			}
			return ['status'=>'error'];
		}
	}

	public function addVScoupon($id)
	{
		if(is_numeric($id) && current_vendor()){
			if($id == 2){
				$categories = Category::where('parent', '=', 0)->get();
				$allCategories = [];
				foreach($categories as $parentCategory){
					$allCategories[] = [
						'id' => $parentCategory->id,
						'name' => $parentCategory->name,
					];
					foreach($parentCategory->children as $category) {
						$allCategories[] = [
							'id' => $category->id,
							'name' => '&nbsp;&nbsp;'.$category->name,
						];
						foreach($category->children as $subCategory) {
							$allCategories[] = [
								'id' => $subCategory->id,
								'name' => '&nbsp;&nbsp;&nbsp;&nbsp;'.$subCategory->name,
							];
						}
					}
				}
				return [
					'categories' => $allCategories,
					'products' => [],
					'vid' => current_vendor()->id,
				];
			} else if($id == 3) {
				return [
					'categories' => [],
					'products' => CouponDetailsProductResource::collection(current_vendor()->products),
					'vid' => current_vendor()->id,
				];
			}
			return [
				'categories' => [],
				'products' => [],
				'vid' => current_vendor()->id,
			];
		}
		return ['status'=>'error'];
	}

	public function storeVScoupon(Request $request)
	{
		if(current_vendor()){
			$validatedDate = $request->validate([
				'code' => ['required', new UniqueCoupon()],
				'start_date' => 'required',
				'expire_date' => 'required',
                'discount' => 'required|gt:0',
			]);
			$coupon = new Coupon();
            $coupon->vendor_id = current_vendor()->id;
            $coupon->code = 'v'.current_vendor()->id.'-'.$request->code;
            $coupon->ucode = $request->code;
            $coupon->description = $request->description;
			if($request->type == 2){
                $coupon->action = 1;
                if(is_array(json_decode($request->selectedCategories))){
					$coupon->categories = $request->selectedCategories;
				} else {
					$coupon->categories = '['.$request->selectedCategories.']';
				}
                $coupon->products = NULL;
                $coupon->type = 2;
            } elseif($request->type == 3){
                $coupon->action = $request->action;
                $coupon->categories = NULL;
                $selProducts =  json_decode($request->selProducts);
				$selectedProducts = [];
				if(count($selProducts)){
					foreach($selProducts as $product){
						$selectedProducts[] = $product->id;
					}
				}
				$coupon->products = json_encode($selectedProducts);
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
			if($request->withoffer == 'true'){
				$coupon->withoffer = 1;
			} else {
				$coupon->withoffer = 0;
			}
			$startDate = explode('T', $request->start_date);
			$expireDate = explode('T', $request->expire_date);
			$coupon->start_date = Carbon::parse($startDate[0])->format('Y-m-d').' 00:00:05';
			$coupon->expire_date = Carbon::parse($expireDate[0])->format('Y-m-d').' 23:59:58';
            $coupon->save();
			return ['status'=>'success'];
		}
	}

	public function deleteVScoupon(Request $request, $id)
	{
		if(is_numeric($id) && mcheck_permissions('delete_rights') && current_vendor()){
			$coupon = current_vendor()->coupons()->where('id', $id)->first();
			if($coupon){
				$coupon->delete();
				return ['status'=>'success'];
			}
		}
		return ['status'=>'error'];
	}
}