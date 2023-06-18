<?php

namespace App\Http\Livewire\Products;

use Carbon\Carbon;
use App\Models\Chat;
use Livewire\Component;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Support\Str;

class VendorChat extends Component
{
    public $vendor;
    public $pid;
    public $vendorName = '';
    public $openChat = false;
    public $hasChat = false;
    public $chats;
    public $messages = [];
    public $nextMessages = [];
    public $chat;
    public $taken = 1;
    public $showLoading = false;
    public $chatname = '';
    public $nameError = false;
    public $messageError = false;
    public $isLoggedIn = false;
    public $showName = true;
    public $exludeMessage = [];
    public $vendorStatus = false;

    protected $listeners = [
        'load-more' => 'loadMore',
        'recive-message' => 'reciveMessage',
        'send-message' => 'sendMessageHeader',
        'sendAsyncOneSignal' => 'sendAsyncOneSignal'
    ];

    public function mount()
    {
        $today = Str::lower(date('l'));
        $nowTime = date('H:i:00');
        $date1 = Carbon::createFromFormat('H:i:s', $nowTime);
        $date2 = Carbon::createFromFormat('H:i:s', $this->vendor->workhour[$today.'_start']);
        $resultS = $date1->gt($date2);
        $date3 = Carbon::createFromFormat('H:i:s', $nowTime);
        $date4 = Carbon::createFromFormat('H:i:s', $this->vendor->workhour[$today.'_end']);
        $resultE = $date3->lt($date4);
        if($this->vendor->workhour[$today] && $resultS && $resultE){
            $this->vendorStatus = true;
        }
        if(current_user()){
            $chats = current_user()->chats->where('vendor_id', '=', $this->vendor->id)->first();
            if($chats && $chats->messages->count()){
                $this->chats = $chats;
                $this->hasChat = true;
                $messages = $chats->messages()->orderBy('id', 'desc')->take(10)->get();
                $this->messages = $messages->reverse();
            }
            $this->chatname = current_user()->first_name;
            $this->isLoggedIn = true;
        } else {
            if(isset($_COOKIE['uuids']) && $_COOKIE['uuids']){
                $userId = $_COOKIE['uuids'];
                $chats = Chat::where([['user_id', '=', $userId],['vendor_id', '=', $this->vendor->id]])->first();
                if($chats && $chats->messages->count()){
                    $this->chats = $chats;
                    $this->chatname = $chats->name;
                    $this->showName = false;
                    $this->hasChat = true;
                    $messages = $chats->messages()->orderBy('id', 'desc')->take(10)->get();
                    $this->messages = $messages->reverse();
                } else {
                    $chats = Chat::where('user_id', '=', $userId)->first();
                    if($chats && $chats->name){
                        $this->chatname = $chats->name;
                        $this->showName = false;
                    }
                }
            } else {
                $userId = Str::uuid();
                setcookie('uuids', $userId, time() + (30*3110400), "/");
            }
        }
        $this->vendorName = $this->vendor->name;
    }

    public function render()
    {
        return view('livewire.products.vendor-chat');
    }

    public function openChat()
    {
        $this->showLoading = false;
        $this->openChat = true;
        $this->emit('openChatJs', "true");
    }

    public function closeChat()
    {
        $this->openChat = false;
        $this->emit('closeChatJs', "true");
    }

    public function sendMessage()
    {
        if($this->chat && trim($this->chat)){
            if($this->isLoggedIn){
                if(!$this->chat){
                    $this->messageError = true;
                    return;
                }
                $userId = current_user()->id;
                $chatNames = '';
            } else {
                if(!$this->chatname){
                    $this->nameError = true;
                    if(!$this->chat){
                        $this->messageError = true;
                    }
                    return;
                }
                if(!$this->chat){
                    $this->messageError = true;
                    return;
                }
                
                if(isset($_COOKIE['uuids']) && $_COOKIE['uuids']){
                    $userId = $_COOKIE['uuids'];
                } else {
                    $userId = Str::uuid();
                    setcookie('uuids', $userId, time() + (30*3110400), "/");
                }
                $chatNames = $this->chatname;
            }
            $this->nameError = false;
            $this->messageError = false;
            $sendProduct = false;
            $message = new ChatMessage();
            if($this->hasChat){
                $this->chats->touch();
                $chatId = $this->chats->id;
                if($this->pid){
                    $lastProduct = $this->chats->messages()->where('type', '=', 2)->orderBy('id', 'desc')->first();
                    if($lastProduct && $lastProduct->message != $this->pid){
                        $sendProduct = true;
                    }
                }
            } else {
                $chat = new Chat();
                $chat->user_id = $userId;
                $chat->logged = $this->isLoggedIn;
                $chat->name = $chatNames;
                $chat->vendor_id = $this->vendor->id;
                $chat->save();
                $this->chats = $chat;
                $chatId = $chat->id;
                $sendProduct = true;
            }
            $message->chat_id = $chatId;
            $message->message = $this->chat;
            $message->way = 1;
            $message->type = 1;
            if($sendProduct){
                if($this->pid){
                    $product = new ChatMessage();
                    $product->chat_id = $chatId;
                    $product->message = $this->pid;
                    $product->way = 1;
                    $product->type = 2;
                    $product->save();
                    if(count($this->messages)){
                        $this->messages = collect($this->messages)->push($product);
                    } else {
                        $this->messages = collect([])->push($product);
                        $this->hasChat = true;
                    }
                    $broadcast = broadcast(new MessageSent($this->vendor->uvid, 1, $product))->toOthers();
                    $this->exludeMessage[] = $product->id;
                }
            }
            $message->save();
            $this->emit('lastMessagesV', "true");
            if(count($this->messages)){
                $this->messages = collect($this->messages)->push($message);
            } else {
                $this->messages = collect([])->push($message);
                $this->hasChat = true;
            }
            $broadcast = broadcast(new MessageSent($this->vendor->uvid, 1, $message))->toOthers();
            $this->exludeMessage[] = $message->id;
            $this->showName = false;
            $this->chat = '';
            // if($this->chats->vendor){
            //     foreach($this->chats->vendor->owners as $user){
            //         if($user->onesignal && $user->onesignal->onesignal){
            //             $userName = $this->chats->name;
            //             if($this->chats->logged) $userName = $this->chats->user->first_name.' '.$this->chats->user->last_name;
            //             $this->sendOneSignal($user->onesignal->onesignal, $userName);
            //         }
            //     }
            // }
            $this->emitSelf('sendAsyncOneSignal');
        }
    }

    public function sendAsyncOneSignal(){
        if($this->chats->vendor){
            foreach($this->chats->vendor->owners as $user){
                if($user->onesignal && $user->onesignal->onesignal){
                    $userName = $this->chats->name;
                    if($this->chats->logged) $userName = $this->chats->user->first_name.' '.$this->chats->user->last_name;
                    $this->sendOneSignal($user->onesignal->onesignal, $userName);
                }
            }
        }
    }

    private function sendOneSignal($receiver, $name){
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
        $fields = array(
            'app_id' => env('ONESIGNAL_APP_ID'),
			'data' => array("linkType" => 'vmessage', 'chat_id' => $this->chats->id),
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

    public function sendMessageHeader($id, $vid)
    {
        if($vid == $this->chats->id){
            $message = ChatMessage::where('id', $id)->first();
            if(count($this->messages)){
                $this->messages = collect($this->messages)->push($message);
            } else {
                $this->messages = collect([])->push($message);
                $this->hasChat = true;
            }
            $this->exludeMessage[] = $message->id;
        }
    }

    public function reciveMessage($id, $vid)
    {
        if($this->openChat && $vid == $this->chats->id){
            $message = ChatMessage::where('id', $id)->first();
            $message->seen = 1;
            $message->save();
            $message->seen = 0;
            if(count($this->messages)){
                $this->messages = collect($this->messages)->push($message);
            } else {
                $this->messages = collect([])->push($message);
                $this->hasChat = true;
            }
            $this->exludeMessage[] = $message->id;
            $this->emit('lastMessagesV', "true");
        }
    }

    public function loadMore($position, $old)
    {
        $this->showLoading = true;
        $offest = $this->taken;
        $this->taken = $offest + 1;
        ray($this->exludeMessage);
        $oldMessage = $this->chats->messages()->orderBy('id', 'desc')->skip(($offest*10))->take(10)->get();
        $newMessages = $oldMessage->reverse()->toBase()->merge($this->messages);
        $this->messages = $newMessages;
        $hasMore = true;
        if(count($oldMessage) < 10){
            $this->showLoading = false;
            $hasMore = false;
        }
        $this->emit('oldMessagesV', $hasMore, $position, $old);
        
    }
}
