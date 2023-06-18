<?php

namespace App\Http\Livewire\Chat;

use Livewire\Component;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\UserOnesignal;

class VendorChat extends Component
{
    public $chat;
    public $chats;
    public $selChat;
    public $messages = [];
    public $messageError = false;

    public $showLoading = false;
    public $taken = 1;
    
    public $exludeMessage = [];

    protected $listeners = [
        'load-more' => 'loadMore',
        'recive-message' => 'reciveMessage',
        'sendAsyncOneSignal' => 'sendAsyncOneSignal'
    ];

    public function mount()
    {
        // $chats = current_vendor()->chats;
    }

    public function render()
    {
        return view('livewire.chat.vendor-chat');
    }

    public function selectClient($id)
    {
        $chats = current_vendor()->chats->where('id', '=', $id)->first();
        if($chats){
            $this->selChat = $chats;
            $messages = $chats->messages()->orderBy('id', 'desc')->take(20)->get();
            $this->messages = $messages->reverse();
            $chats->messages()->where('way', 1)->update(['seen' => 1]);
            $this->chat = '';
            $this->emit('lastMessages', "true");
        }
    }

    public function sendMessage()
    {
        if($this->chat && trim($this->chat)){
            $this->selChat->touch();
            $message = new ChatMessage();
            $message->chat_id = $this->selChat->id;
            $message->message = $this->chat;
            $message->way = 2;
            $message->type = 1;
            $message->save();
            $this->emit('lastMessages', "true");
            $this->emit('removeNS', "true");
            if(count($this->messages)){
                $this->messages = collect($this->messages)->push($message);
            } else {
                $this->messages = collect([])->push($message);
            }
            $this->chat = '';
            if($this->selChat->logged){
                $receiver = $this->selChat->user->uuid;
                $user = $this->selChat->user;
            } else {
                $receiver = $this->selChat->user_id;
                $user = false;
            }
            //send msg oneSignal
            // if($this->selChat->userOnesignal && $this->selChat->userOnesignal->onesignal){
            //     $this->sendOneSignal($this->selChat->userOnesignal->onesignal);
            // }
            $broadcast = broadcast(new MessageSent($receiver, 2, $message))->toOthers();
            $this->exludeMessage[] = $message->id;
            $this->emitSelf('sendAsyncOneSignal');
        }
    }

    public function sendAsyncOneSignal(){
        if($this->selChat->userOnesignal && $this->selChat->userOnesignal->onesignal){
            $this->sendOneSignal($this->selChat->userOnesignal->onesignal);
        }
    }

    private function sendOneSignal($receiver){
        if(!$receiver) return;
        $message='Ju keni një mesazh të ri nga '.current_vendor()->name;
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
			'data' => array("linkType" => 'umessage', 'chat_id' => $this->selChat->id),
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

    public function reciveMessage($id, $vid)
    {
        $chats = current_vendor()->chats;
        $this->chats = $chats;
        if($this->selChat && $vid == $this->selChat->id){
            $oldMesages = collect($this->messages);
            $message = ChatMessage::where('id', $id)->first();
            $message->seen = 1;
            $message->save();
            $message->seen = 0;
            if(count($this->messages)){
				$oldMesages = $oldMesages->map(function($item) { 
                    $item['seen'] = 1; 
                    return $item; 
                });
                $this->messages = collect($oldMesages)->push($message);
            } else {
                $this->messages = collect([])->push($message);
                $this->hasChat = true;
            }
            $this->emit('lastMessages', "true");
        }
    }

    public function loadMore($position, $old)
    {
        $this->showLoading = true;
        $offest = $this->taken;
        $this->taken = $offest + 1;
        $oldMessage = $this->selChat->messages()->orderBy('id', 'desc')->whereNotIn('id', $this->exludeMessage)->skip(($offest*20))->take(20)->get();
        $newMessages = $oldMessage->reverse()->toBase()->merge($this->messages);
        $this->messages = $newMessages;
        $hasMore = true;
        if(count($oldMessage) < 10){
            $this->showLoading = false;
            $hasMore = false;
        }
        $this->emit('oldMessages', $hasMore, $position, $old);
        
    }
}
