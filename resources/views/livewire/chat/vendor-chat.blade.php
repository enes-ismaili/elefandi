<div>
    <div class="card vchats">
        <div class="card-header">LiveChat</div>
        <div class="card-body">
            <div class="chat-left">
                <div class="chat-body">
                    @if($selChat)
                        @foreach($messages as $message)
                            @if($message['type'] == 2)
                                @php
                                    $currProduct = App\Models\Product::where('id', '=', $message['message'])->first();
                                @endphp
                                @if($currProduct)
                                <div class="message w1 p {{ (isset($message['seen']) && $message['seen'] == 0) ? 'ns' : '' }}">
                                    <div class="chat-product">
                                        <a href="{{route('single.product', [$currProduct->owner->slug, $currProduct->id])}}">
                                            <div class="chat-product-thumb">
                                                <img src="{{asset('/photos/products/'.$currProduct->image)}}">
                                            </div>
                                            <div class="chat-product-title">{{ $currProduct->name }}</div>
                                        </a>
                                    </div>
                                </div>
                                @endif
                            @else
                                <div class="message w{{ $message['way'] }} {{ (isset($message['seen']) && $message['seen'] == 0) ? 'ns' : '' }}"><div class="txt">{{ $message['message'] }}</div></div>
                            @endif
                        @endforeach
                    @else
                        <div class="no-chat">Zgjidhni një klient për të hapur bisedën</div>
                    @endif
                </div>
                @if($selChat)
                <div class="chat-footer">
                    <div class="chat-text">
                        <div class="chat-input row">
                            <textarea id="message" class="form-control {{ ($messageError) ? 'required' : '' }}" placeholder="Shkruani mesazhin tuaj..." wire:model.defer="chat" wire:keydown.enter="sendMessage"></textarea>
                        </div>
                        <div class="chat-send" wire:click="sendMessage">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="chat-right">
                @if(count($chats))
                <ul class="clients">
                    @foreach($chats as $chat)
                        <li wire:click="selectClient('{{$chat->id}}')" @if($selChat && $chat->id == $selChat->id) class="active" @endif>{{ ($chat->logged) ? $chat->user->first_name.' '.$chat->user->last_name : $chat->name.' (Jo Klient)' }} <span>{{ $chat->messages()->where([['way', '=', 1],['seen', '=', 0]])->count() }}</span></li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
    {{-- <script src="https://cdn.socket.io/4.0.1/socket.io.js"></script>
    <script>
        var socket = io('https://e2.m:3000');
        console.log(socket);
        socket.on('connect', function(data) {
            console.log('testtt')
        });
        socket.on('private-chats', (socket) => {
            console.log(socket);
        });
        socket.on("private-chats", function(message){
            console.log(message);
        });
    </script> --}}
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let htmlS = document.getElementsByTagName('html')[0];
            let checkMessages = true;
            window.livewire.on('lastMessages', () => {
                lastMessage();
            });
            window.livewire.on('removeNS', () => {
                let notSeenMessages = document.querySelectorAll('.message.ns');
                notSeenMessages.forEach(nsMessage => {
                    nsMessage.classList.remove('ns');
                })
            });
            window.livewire.on('oldMessages', (more, position, height) => {
                let messagess = document.querySelector('.chat-body');
                messagess.scrollTop = messagess.scrollHeight - height;
                setTimeout( () => {
                    let loadingS = document.querySelector('.loading-messages');
                    if(loadingS && loadingS.classList.contains('show')){
                        loadingS.classList.remove('show');
                    }
                    if(more){
                        checkMessages = true;
                    }
                }, 600)
            });
            function lastMessage(){
                let messagess = document.querySelector('.chat-body');
                messagess.scrollTop = messagess.scrollHeight - messagess.clientHeight + 10;
                setTimeout(function(){ 
                    messagess.scrollTop = messagess.scrollHeight - messagess.clientHeight + 10;
                 }, 100);
                setTimeout(function(){ 
                    messagess.scrollTop = messagess.scrollHeight - messagess.clientHeight + 10;
                 }, 300);
            }
            let messages = document.querySelector('.chat-body');
            messages.onscroll = function (ev) {
                if(messages.scrollTop < 30){
                    loadMore(messages.scrollTop, messages.scrollHeight);
                    checkMessages = false;
                }
            };
            function loadMore(position, height){
                if(checkMessages){
                    let loadingS = document.querySelector('.loading-messages');
                    if(loadingS && !loadingS.classList.contains('show')){
                        document.querySelector('.loading-messages').classList.add('show');
                    }
                    window.livewire.emit('load-more', position, height);
                }
            }
        });
    </script>
</div>
