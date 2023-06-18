<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\Product;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatSingleResource;
use App\Http\Controllers\Api\BaseController;

class ChatController extends BaseController
{
	public function countChats(Request $request)
    {
		if(current_user()){
			
			/*$getChatsCount = current_user()->chats()->whereHas('latestMessageU', function($q){
				$q->where('seen', '=', '0');
			})->get();*/
			//$getChatsCount = current_user()->chats()->with('latestMessageU')->get();
			//$getChatsCount = current_user()->chats()->has('latestMessageU')->get();
			//$getChatsCount = current_user()->chats()->whereRelation('latestMessage', 'seen', 0)->get();
			/*$getChatsCount = current_user()->chats()->with('latestMessageU')->whereHas('latestMessageU', function($q){
				$q->where('seen', '=', '0');
			})->get();*/
			$getUChatsCount = current_user()->chats()->with('latestMessageU')->has('latestMessageU')->get()->where('latestMessageU.seen', '=', 0)->count();
			$getChatsCount = [
				'chat' => $getUChatsCount,
			];
			if($request->vid){
				//return current_vendor()->chats()->with('latestMessageV')->has('latestMessageV')->get();
				$getVChatsCount = current_vendor()->chats()->with('latestMessageV')->has('latestMessageV')->get()->where('latestMessageV.seen', '=', 0)->count();
				$getChatsCount['vchat'] = $getVChatsCount;
			}
			
			//return $getVChatsCount;
		} else {
			$getChatsCount = 0;
		}
		return $getChatsCount;
	}
    public function getChats(Request $request)
    {
        if($request->uid) {
			if($request->logged == 'true' && current_user()){
				$chats = current_user()->chats()->orderBy('updated_at', 'DESC')->get();
			} else {
				$chats = Chat::where('user_id', $request->uid)->orderBy('updated_at', 'DESC')->get();
			}
            
            $allChats = [];
            foreach($chats as $chat) {
                $lastMessage = $chat->latestMessage;
                $seen = 1;
                $message = '';
                if($lastMessage){
					if($lastMessage->way == 2){
						$seen = $lastMessage->seen;
					}
                    $message = $lastMessage->message;
                }
                $schat = [
                    'id' => $chat->id,
                    'name' => $chat->vendor->name,
                    'message' => $message,
                    'seen' => $seen,
					'way' => $lastMessage->way,
                ];
				$allChats[] = $schat;
                //array_push($allChats, $schat);
            }
			return $allChats;
			// if(count($allChats)){
			// }
            // return ['results'=>'no'];
        }
    }
	
	public function getVChats(Request $request)
    {
		if($request->vid) {
			if($request->logged == 'true'){
				//return current_vendor();
				//return current_user()->vroles->first();
				if(mcheck_permissions('manage_chat')){
					$chats = current_vendor()->chats()->orderBy('updated_at', 'DESC')->get();
					//return $chats;
				} else {
					return ['status'=>403];
				}
				//$chats = current_user()->chats;
			}
            
            $allChats = [];
            foreach($chats as $chat) {
                $lastMessage = $chat->latestMessage;
                $seen = 1;
                $message = '';
				$chatName = $chat->name;
				if($chat->logged){
					$chatName = $chat->user->first_name .' '. $chat->user->last_name;
				}
                if($lastMessage){
					if($lastMessage->way == 1){
						$seen = $lastMessage->seen;
					}
                    $message = $lastMessage->message;
                }
                $schat = [
                    'id' => $chat->id,
                    'name' => $chatName,
                    'message' => $message,
                    'seen' => $seen
                ];
				$allChats[] = $schat;
                //array_push($allChats, $schat);
            }
			return $allChats;
			// if(count($allChats)){
			// }
            // return ['results'=>'no'];
        }
    }
	
	public function getSingleChatMessages(Request $request)
    {
		if($request->id && $request->uid) {
			if($request->logged == 'true'){
				$chats = current_user()->chats;
				$thisChat = current_user()->chats->where('id', $request->id)->first();
			} else {
				$thisChat = Chat::where([['user_id', $request->uid],['id', $request->id]])->first();
			}
			
			if($thisChat){
				$allMessages = [];
				$thisChat->messages()->where('way', 2)->where('seen', '=', 0)->update(['seen' => 1]);
				$lastMessages = $thisChat->messages()->orderBy('id', 'DESC')->paginate(25);
				$i=0;
				foreach($lastMessages as $messages){
					$i++;
                    $showTime = false;
					$createdC = Carbon::parse($messages->created_at)->format('U');
					if(!isset($lastMessages[$i])){
						$showTime = true;
					} else {
						$createdN = Carbon::parse($lastMessages[$i]['created_at'])->format('U');
						if(($createdC - $createdN) > 3600){
							$showTime = true;
						}
					}
                    $smessage = [
                        'way' => $messages->way,
                        'message' => $messages->message,
                        'type' => $messages->type,
                        'created' => $createdC,
                        'created_at' => Carbon::parse($messages->created_at)->format('H:i d.m.Y'),
                        'showTime' => $showTime
                    ];
					if($messages->type == 2 && is_numeric($messages->message)){
						$product = Product::find($messages->message);
						if($product && $product->status==1 && $product->vstatus==1){
							if(file_exists(public_path('/photos/products/230/'.$product->image))){
								$image = asset('/photos/products/230/'.$product->image);
							} else {
								$image = asset('/photos/products/'.$product->image);
							}
							$smessage['product'] = [
								'name' => $product->name,
								'image' => $image
							];
						}
					}
                    array_push($allMessages, $smessage);
                }
                return [
                    'exists' => true,
                    'chat_id' => $thisChat->id,
                    'messages' => array_reverse($allMessages),
					'name' => $thisChat->vendor->name
                ];
			}
		}
        return [
            'exists' => false,
        ];
	}
	
	public function getSingleVChatMessages(Request $request)
    {
		if($request->id && $request->vid) {
			if($request->logged == 'true'){
				if(mcheck_permissions('manage_chat')){
					$thisChat = current_vendor()->chats->where('id', $request->id)->first();
				} else {
					return ['status'=>403];
				}
			} else {
				$thisChat = Chat::where([['user_id', $request->uid],['id', $request->id]])->first();
			}
			
			if($thisChat){
				$allMessages = [];
				$thisChat->messages()->where('way', 1)->where('seen', '=', 0)->update(['seen' => 1]);
				$lastMessages = $thisChat->messages()->orderBy('id', 'DESC')->paginate(25);
				$i=0;
				foreach($lastMessages as $messages){
					$i++;
					$showTime = false;
					$createdC = Carbon::parse($messages->created_at)->format('U');
					if(!isset($lastMessages[$i])){
						$showTime = true;
					} else {
						$createdN = Carbon::parse($lastMessages[$i]['created_at'])->format('U');
						if(($createdC - $createdN) > 3600){
							$showTime = true;
						}
					}
                    $smessage = [
                        'way' => $messages->way,
                        'message' => $messages->message,
                        'type' => $messages->type,
                        'created' => $createdC,
                        'created_at' => Carbon::parse($messages->created_at)->format('H:i d.m.Y'),
                        'showTime' => $showTime
                    ];
					if($messages->type == 2 && is_numeric($messages->message)){
						$product = Product::find($messages->message);
						if($product && $product->status==1 && $product->vstatus==1){
							if(file_exists(public_path('/photos/products/230/'.$product->image))){
								$image = asset('/photos/products/230/'.$product->image);
							} else {
								$image = asset('/photos/products/'.$product->image);
							}
							$smessage['product'] = [
								'name' => $product->name,
								'image' => $image
							];
						}
					}
                    array_push($allMessages, $smessage);
                }
                return [
                    'exists' => true,
                    'chat_id' => $thisChat->id,
                    'messages' => array_reverse($allMessages),
					'name' => (($thisChat->logged) ? $thisChat->user->first_name.' '.$thisChat->user->last_name : $thisChat->name),
                ];
			}
		}
        return [
            'exists' => false,
        ];
	}

    public function getChatMessages(Request $request)
    {
        if($request->uid && $request->vid) {
			if($request->logged == 'true'){
				$chats = current_user()->chats;
				$thisChat = current_user()->chats->where('vendor_id', $request->vid)->first();
			} else {
				$thisChat = Chat::where([['user_id', $request->uid],['vendor_id', $request->vid]])->first();
			}
            if($thisChat){
                $allMessages = [];
                // $lastMessage = 0;
				$lastMessages = $thisChat->messages()->orderBy('id', 'DESC')->paginate(25);
				$i=0;
                foreach($lastMessages as $messages){
					$i++;
					$showTime = false;
					$createdC = Carbon::parse($messages->created_at)->format('U');
					if(!isset($lastMessages[$i])){
						$showTime = true;
					} else {
						$createdN = Carbon::parse($lastMessages[$i]['created_at'])->format('U');
						if(($createdC - $createdN) > 3600){
							$showTime = true;
						}
					}
                    $smessage = [
                        'way' => $messages->way,
                        'message' => $messages->message,
                        'type' => $messages->type,
                        'created' => $createdC,
                        'created_at' => Carbon::parse($messages->created_at)->format('H:i d.m.Y'),
                        'showTime' => $showTime
                    ];
					if($messages->type == 2 && is_numeric($messages->message)){
						$product = Product::find($messages->message);
						if($product && $product->status==1 && $product->vstatus==1){
							if(file_exists(public_path('/photos/products/230/'.$product->image))){
								$image = asset('/photos/products/230/'.$product->image);
							} else {
								$image = asset('/photos/products/'.$product->image);
							}
							$smessage['product'] = [
								'name' => $product->name,
								'image' => $image
							];
						}
					}
                    array_push($allMessages, $smessage);
                }
                return [
                    'exists' => true,
                    'chat_id' => $thisChat->id,
                    'messages' => array_reverse($allMessages),
                ];
            }
        }
        return [
            'exists' => false,
        ];
    }
	
	public function sendSingleMessage(Request $request)
	{
		if($request->uid && $request->id && $request->message) {
			if(current_user()){
				$chat = current_user()->chats->where('id', '=', $request->id)->first();
				$userUuid = current_user()->id;
			} else {
				$chat = Chat::where('id', '=', $request->id)->first();
				$userUuid = $request->uid;
			}
			
			if($chat && $chat->user_id == $userUuid){
				$chat->touch();
				$vendor = $chat->vendor;
				$message = new ChatMessage();
				$message->chat_id = $chat->id;
				$message->message = $request->message;
				$message->way = 1;
				$message->type = 1;
				$message->save();

				$created = Carbon::parse($message->created_at)->format('U');
				$showTime = false;
				if(($created - $request->lastTime) > 3600){
					$showTime = true;
				}
				$smessage = [
					'way' => 1,
					'message' => $message->message,
					'created' => $created,
					'created_at' => Carbon::parse($message->created_at)->format('H:i d.m.Y'),
					'showTime' => $showTime,
					'type' => 1
				];
				$broadcast = broadcast(new MessageSent($vendor->uvid, 1, $message))->toOthers();
				return ['message'=> $smessage, 'status'=>'success'];
			}
		}
		return ['status'=>'error'];
	}
	
	public function sendVSingleMessage(Request $request)
	{
		if($request->id && $request->message) {
			if(mcheck_permissions('manage_chat')){
				$chat = current_vendor()->chats->where('id', $request->id)->first();
			} else {
				return ['status'=>403];
			}
			if($chat){
				if($chat->logged){
					$userUID = $chat->user->uuid;
				} else {
					$userUID = $chat->user_id;
				}
				$vendor = $chat->vendor;
				$chat->touch();
				$message = new ChatMessage();
				$message->chat_id = $chat->id;
				$message->message = $request->message;
				$message->way = 2;
				$message->type = 1;
				$message->save();

				$created = Carbon::parse($message->created_at)->format('U');
				$showTime = false;
				if(($created - $request->lastTime) > 3600){
					$showTime = true;
				}
				$smessage = [
					'way' => 2,
					'message' => $message->message,
					'created' => $created,
					'created_at' => Carbon::parse($message->created_at)->format('H:i d.m.Y'),
					'showTime' => $showTime,
					'type' => 1
				];
				$broadcast = broadcast(new MessageSent($userUID, 2, $message))->toOthers();
				//return $broadcast;
				return ['message'=> $smessage, 'status'=>'success'];
			}
		}
	}

    public function sendMessage(Request $request)
    {
        if($request->uid && $request->vid && $request->message) {
            if($request->exists){
                $chat = Chat::where('id', '=', $request->exists)->first();
                if($chat && $chat->vendor_id == $request->vid){
					$chat->touch();
                    $vendor = $chat->vendor;
					$sproduct = false;
					$lastProductSend = $chat->messages->where('type', 2)->last();
					if(($request->product && !$lastProductSend) || ($request->product && $lastProductSend && $lastProductSend->message != $request->product)){
						$cproduct = new ChatMessage();
						$cproduct->chat_id = $chat->id;
						$cproduct->message = $request->product;
						$cproduct->way = 1;
						$cproduct->type = 2;
						$cproduct->save();

						$created = Carbon::parse($cproduct->created_at)->format('U');
						$showTime = false;
						if(($created - $request->lastTime) > 3600){
							$showTime = true;
						}
						$sproduct = [
							'way' => 1,
							'message' => $cproduct->message,
							'type' => 2,
							'created' => $created,
							'created_at' => Carbon::parse($cproduct->created_at)->format('H:i d.m.Y'),
							'showTime' => $showTime,
							'type' => 2
						];
						$product = Product::find($cproduct->message);
						if($product && $product->status==1 && $product->vstatus==1){
							if(file_exists(public_path('/photos/products/230/'.$product->image))){
								$image = asset('/photos/products/230/'.$product->image);
							} else {
								$image = asset('/photos/products/'.$product->image);
							}
							$sproduct['product'] = [
								'name' => $product->name,
								'image' => $image
							];
						}
						$showTime = false;
					}

                    $message = new ChatMessage();
                    $message->chat_id = $chat->id;
                    $message->message = $request->message;
                    $message->way = 1;
                    $message->type = 1;
                    $message->save();

					if(!$sproduct){
						$created = Carbon::parse($message->created_at)->format('U');
						$showTime = false;
						if(($created - $request->lastTime) > 3600){
							$showTime = true;
						}
					}
					
                    $smessage = [
                        'way' => 1,
                        'message' => $message->message,
						'type' => 1,
                        'created' => $created,
                        'created_at' => Carbon::parse($message->created_at)->format('H:i d.m.Y'),
                        'showTime' => $showTime,
						'type' => 1
                    ];
                    $broadcast = broadcast(new MessageSent($vendor->uvid, 1, $message))->toOthers();
                    return ['message'=> $smessage, 'product'=> $sproduct, 'status'=>'success'];
                }
				return $chat->messages->where('type', 2)->last();

            } else {
				$chatName = '';
				if($request->name){
					$chatName = $request->name;
				}
                $chat = new Chat();
				if(current_user()){
					$chat->user_id = current_user()->id;
				} else {
					$chat->user_id = $request->uid;
				}
                $chat->logged = $request->logged;
                $chat->name = $chatName;
                $chat->vendor_id = $request->vid;
                $chat->save();
                $vendor = $chat->vendor;

				$sproduct =false;
				if($request->product){
					$cproduct = new ChatMessage();
					$cproduct->chat_id = $chat->id;
					$cproduct->message = $request->product;
					$cproduct->way = 1;
					$cproduct->type = 2;
					$cproduct->save();
	
					$created = Carbon::parse($cproduct->created_at)->format('U');
					$showTime = false;
					if(($created - $request->lastTime) > 3600){
						$showTime = true;
					}

					$sproduct = [
						'way' => 1,
						'message' => $cproduct->message,
						'type' => 2,
						'created' => $created,
						'created_at' => Carbon::parse($cproduct->created_at)->format('H:i d.m.Y'),
						'showTime' => $showTime,
						'type' => 2
					];
					$showTime = false;
					$product = Product::find($cproduct->message);
					if($product && $product->status==1 && $product->vstatus==1){
						if(file_exists(public_path('/photos/products/230/'.$product->image))){
							$image = asset('/photos/products/230/'.$product->image);
						} else {
							$image = asset('/photos/products/'.$product->image);
						}
						$sproduct['product'] = [
							'name' => $product->name,
							'image' => $image
						];
					}
				}

                $message = new ChatMessage();
                $message->chat_id = $chat->id;
                $message->message = $request->message;
                $message->way = 1;
                $message->type = 1;
                $message->save();
				if(!$sproduct){
					$created = Carbon::parse($message->created_at)->format('U');
					$showTime = false;
					if(($created - $request->lastTime) > 3600){
						$showTime = true;
					}
				}
                $smessage = [
                    'way' => 1,
					'exists' => $chat->id,
                    'message' => $message->message,
					'type' => 1,
                    'created' => $created,
                    'created_at' => Carbon::parse($message->created_at)->format('H:i d.m.Y'),
                    'showTime' => $showTime,
					'type' => 1
                ];
                $broadcast = broadcast(new MessageSent($vendor->uvid, 1, $message))->toOthers();
                return ['message'=> $smessage, 'product'=> $sproduct, 'status'=>'success'];
            }
            // ray($request->exists);
        }
        return ['message'=>'tes', 'status'=>'error'];
    }
	
	public function mchangeStatus(Request $request)
	{
		$chat = ChatMessage::where('id', '=', $request->id)->first();
		if($chat && $chat->chat_id == $request->cid){
			$chat->seen = 1;
			$chat->save();
			return ['status'=>'success'];
		}
		return ['status'=>'error'];
	}
}
