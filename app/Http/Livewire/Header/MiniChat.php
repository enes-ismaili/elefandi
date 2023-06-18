<?php

namespace App\Http\Livewire\Header;

use App\Models\Chat;
use App\Models\Country;
use Livewire\Component;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class MiniChat extends Component
{
    protected $listeners = [
        'load-more-header' => 'loadMoreHeader',
        'recive-message' => 'reciveMessage',
        'sendAsyncOneSignal' => 'sendAsyncOneSignal'
    ];

    public $showChat = false;
    public $isLogged = false;
    public $isVendor = false;
    public $uChats =  [];
    public $tuChats =  0;
    public $vChats =  [];
    public $tvChats =  0;
    public $unreadUM =  0;
    public $unreadVM =  0;

    public $chat;
    public $singleChat = false;
    public $selectedChat;
    public $selectedMessage;
    public $userOrVendor = 1;
    public $messageError = false;
    public $taken = 1;
    public $showLoading = false;
    public $exludeMessage = [];
    public $isProduct;
    public $userId;

    public $loadMorePages = 1;

    public function mount()
    {
        $this->isProduct = Request::routeIs('single.product');
        if(current_user()){
            // ray(current_user()->generateUuid());
            $this->isLogged = true;
            $this->uChats = current_user()->chats()->orderBy('updated_at', 'DESC')->take(5)->get();
            $this->unreadUM = current_user()->chats()->with('latestMessage')->get()->pluck('latestMessage')->where('seen', 0)->where('way', 2)->count();
            $this->tuChats = current_user()->chats->count();
            if(current_vendor() && check_permissions('manage_chat')){
                $this->isVendor = true;
                $this->vChats = current_vendor()->chats()->orderBy('updated_at', 'DESC')->take(5)->get();
                $this->unreadVM = current_vendor()->chats()->with('latestMessage')->get()->pluck('latestMessage')->where('seen', 0)->where('way', 1)->count();
                $this->tvChats = current_vendor()->chats->count();
            }
        } else {
            if(isset($_COOKIE['uuids']) && $_COOKIE['uuids']){
                $userId = $_COOKIE['uuids'];
                $this->userId = $userId;
                $this->uChats = Chat::where('user_id', $userId)->orderBy('updated_at', 'DESC')->take(5)->get();
                $this->tuChats = Chat::where('user_id', $userId)->count();
                $this->unreadUM = Chat::where('user_id', $userId)->with('latestMessage')->get()->pluck('latestMessage')->where('seen', 0)->where('way', 2)->count();
            } else {
                $userId = Str::uuid();
                $this->userId = (string) $userId;
                setcookie('uuids', $userId, time() + (30*3110400), "/");
            }
        }
    }

    public function render()
    {
        if($this->isLogged){
            $this->unreadUM = current_user()->chats()->with('latestMessage')->get()->pluck('latestMessage')->where('seen', 0)->where('way', 2)->count();
        } else {
            $this->unreadUM = Chat::where('user_id', $this->userId)->with('latestMessage')->get()->pluck('latestMessage')->where('seen', 0)->where('way', 2)->count();

        }
        if($this->isVendor){
            $this->unreadVM = current_vendor()->chats()->with('latestMessage')->get()->pluck('latestMessage')->where('seen', 0)->where('way', 1)->count();
        }
        return view('livewire.header.mini-chat');
    }

    public function showChat($action)
    {
        if($action == 1){
            $this->showChat = true;
            $this->emit('getCart', "Updated Salary.");
        } else {
            $this->showChat = false;
            $this->singleChat = false;
        }
    }

    public function selectvChat($id)
    {
        $this->userOrVendor = 2;
        $selectedChat = current_vendor()->chats->where('id', $id)->first();
        $this->selectedChat = $selectedChat;
        $messages = $selectedChat->messages()->orderBy('id', 'desc')->take(10)->get();
        $selectedChat->messages()->where('seen', 0)->where('way', 1)->update(['seen' => 1]);
        $this->unreadVM = current_vendor()->chats()->with('latestMessage')->get()->pluck('latestMessage')->where('seen', 0)->where('way', 1)->count();
        $this->selectedMessage = $messages->reverse();
        $this->singleChat = true;
        $this->taken = 1;
        $this->emit('lastMessages', "true");
        $this->emit('startLoadMessages', "true");
    }

    public function selectChat($id)
    {
        $this->userOrVendor = 1;
        if($this->isLogged){
            $selectedChat = current_user()->chats->where('id', $id)->first();
            $this->unreadUM = current_user()->chats()->with('latestMessage')->get()->pluck('latestMessage')->where('seen', 0)->where('way', 2)->count();
        } else {
            $selectedChat = Chat::where('user_id', $this->userId)->where('id', $id)->first();
            $this->unreadUM = Chat::where('user_id', $this->userId)->with('latestMessage')->get()->pluck('latestMessage')->where('seen', 0)->where('way', 2)->count();
        }
        $this->selectedChat = $selectedChat;
        $messages = $selectedChat->messages()->orderBy('id', 'desc')->take(10)->get();
        $selectedChat->messages()->where('seen', 0)->where('way', 2)->update(['seen' => 1]);
        
        $this->selectedMessage = $messages->reverse();
        $this->singleChat = true;
        $this->taken = 1;
        $this->emit('lastMessages', "true");
        $this->emit('startLoadMessages', "true");
    }

    public function goBack()
    {
        $this->singleChat = false;
    }

    public function sendMessage()
    {
        if($this->chat && trim($this->chat)){
            $message = new ChatMessage();
            $this->selectedChat->touch();
            $message->chat_id = $this->selectedChat->id;
            $message->message = $this->chat;
            if($this->userOrVendor == 2){
                $message->way = 2;
                $way = 2;
                if($this->selectedChat->logged){
                    $receiver = $this->selectedChat->user->uuid;
                } else {
                    $receiver = $this->selectedChat->user_id;
                }
            } else {
                $message->way = 1;
                $way = 1;
                $receiver = $this->selectedChat->vendor->uvid;
            }
            $message->type = 1;
            $message->save();
            $this->emit('lastMessages', "true");
            $this->emit('removeNS', "true");
            if(count($this->selectedMessage)){
                $this->selectedMessage = collect($this->selectedMessage)->push($message);
            } else {
                $this->selectedMessage = collect([])->push($message);
                $this->hasChat = true;
            }
            $this->exludeMessage[] = $message->id;
            $this->chat = '';
            $broadcast = broadcast(new MessageSent($receiver, $way, $message))->toOthers();
            if($this->isProduct){
                $this->emitTo('products.vendor-chat', 'send-message', $message->id, $message->chat_id);
            }
            if($this->isLogged){
                $this->uChats = current_user()->chats()->orderBy('updated_at', 'DESC')->take($this->loadMorePages * 5)->get();
            } else {
                $this->uChats = Chat::where('user_id', $this->userId)->orderBy('updated_at', 'DESC')->take($this->loadMorePages * 5)->get();
            }
            // $this->sendOneSignalAsync($way);
            // if($way==1){
            //     if($this->selectedChat->vendor){
            //         foreach($this->selectedChat->vendor->owners as $user){
            //             if($user->onesignal && $user->onesignal->onesignal){
            //                 $userName = $user->first_name.' '.$user->last_name;
            //                 $this->sendOneSignal($user->onesignal->onesignal, $userName, 2);
            //             }
            //         }
            //     }
            // } else {
            //     if($this->selectedChat->userOnesignal && $this->selectedChat->userOnesignal->onesignal){
            //         $this->sendOneSignal($this->selectedChat->userOnesignal->onesignal, current_vendor()->name, 1);
            //     }
            // }
            $this->emitSelf('sendAsyncOneSignal', $way);
        }
    }

    public function sendAsyncOneSignal($way){
        if($way==1){
            if($this->selectedChat->vendor){
                foreach($this->selectedChat->vendor->owners as $user){
                    if($user->onesignal && $user->onesignal->onesignal){
                        $userName = $user->first_name.' '.$user->last_name;
                        $this->sendOneSignal($user->onesignal->onesignal, $userName, 2);
                    }
                }
            }
        } else {
            if($this->selectedChat->userOnesignal && $this->selectedChat->userOnesignal->onesignal){
                $this->sendOneSignal($this->selectedChat->userOnesignal->onesignal, current_vendor()->name, 1);
            }
        }
    }

    private function sendOneSignal($receiver, $name, $way){
        if(!$receiver) return;
        $message='Ju keni një mesazh të ri nga '.$name;
        $filters = array(
            array(
                "field" => "tag",
                "key" => "notification",
                "relation" => "=",
                "value" => "yes"
            )
        );
        if($way == 2){
            $data = array("linkType" => 'vmessage', 'chat_id' => $this->selectedChat->id);
        } else {
            $data = array("linkType" => 'umessage', 'chat_id' => $this->selectedChat->id);
        }
        $fields = array(
            'app_id' => env('ONESIGNAL_APP_ID'),
			'data' => $data,
			'headings' => array("en" => 'Mesazh i ri'),
			'contents' => array("en" => $message),
            'include_player_ids' => array($receiver),
		);
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.env('ONESIGNAL_AUTHORIZATION')));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    public function loadMoreHeader($position, $old)
    {
        $this->showLoading = true;
        $offest = $this->taken;
        $this->taken = $offest + 1;
        $oldMessage = $this->selectedChat->messages()->orderBy('id', 'desc')->whereNotIn('id', $this->exludeMessage)->skip(($offest*10))->take(10)->get();
        $newMessages = $oldMessage->reverse()->toBase()->merge($this->selectedMessage);
        $this->selectedMessage = $newMessages;
        $hasMore = true;
        $this->showLoading = false;
        if(count($oldMessage) < 10){
            $hasMore = false;
        }
        $this->emit('oldMessages', $hasMore, $position, $old);
        
    }

    public function reciveMessage($id, $vid)
    {
        if($this->isProduct){
            $this->emitTo('products.vendor-chat', 'recive-message', $id, $vid);
        }
        if($this->singleChat && ($this->selectedChat->id == $vid)){
            $message = ChatMessage::where('id', $id)->first();
            $message->seen = 1;
            $message->save();
            $message->seen = 0;
            if(count($this->selectedMessage)){
                $this->selectedMessage = collect($this->selectedMessage)->push($message);
            } else {
                $this->selectedMessage = collect([])->push($message);
                $this->hasChat = true;
            }
            $this->emit('lastMessages', "true");
        }
        // $chats = current_vendor()->chats;
        // ray($chats);
        // $this->chats = $chats;
        // if($this->selChat && $vid == $this->selChat->id){
        //     $message = ChatMessage::where('id', $id)->first();
        //     $message->seen = 1;
        //     $message->save();
        //     $message->seen = 0;
        //     if(count($this->messages)){
        //         $this->messages = collect($this->messages)->push($message);
        //     } else {
        //         $this->messages = collect([])->push($message);
        //         $this->hasChat = true;
        //     }
        //     $this->emit('lastMessages', "true");
        // }
    }

    public function loadMoreChats()
    {
        $this->loadMorePages += 1;
        $loadMorePages = $this->loadMorePages * 5;
        $this->uChats = current_user()->chats()->orderBy('updated_at', 'DESC')->take($loadMorePages)->get();
    }
}
