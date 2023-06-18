<div>
    <style>
.mini-chats.show [wire\:loading] {
    position: absolute;
    width: 100%;
    height: 100%;
    background: #0000001a;
    z-index: 2;
    top: 0;
    left: 0;
}
.mini-chats [wire\:loading] .sk-circle {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
    </style>
    <div class="chat-icon icon" wire:click="showChat(1)">
        <div class="h-icon">
            <i class="far fa-comment-dots"></i>
            <span class="notify-icon">{{ $unreadUM + $unreadVM}}</span>
        </div>
        <span class="dmobile icon-title">Çati</span>
    </div>
    <div class="mini-chats @if($showChat) show @endif">
        <div class="bg_modal" wire:click.prevent="showChat(0)"></div>
        <div wire:loading.delay>
            <div class="sk-circle">
                <div class="sk-circle1 sk-child"></div>
                <div class="sk-circle2 sk-child"></div>
                <div class="sk-circle3 sk-child"></div>
                <div class="sk-circle4 sk-child"></div>
                <div class="sk-circle5 sk-child"></div>
                <div class="sk-circle6 sk-child"></div>
                <div class="sk-circle7 sk-child"></div>
                <div class="sk-circle8 sk-child"></div>
                <div class="sk-circle9 sk-child"></div>
                <div class="sk-circle10 sk-child"></div>
                <div class="sk-circle11 sk-child"></div>
                <div class="sk-circle12 sk-child"></div>
            </div>
        </div>
        <div class="chat-lists">
            <div class="close-popup" wire:click.prevent="showChat(0)"></div>
            @if($singleChat && $selectedChat)
                @php
                    if($userOrVendor == 2){
                        if($selectedChat->logged){
                            $userName = $selectedChat->user->first_name.' '.$selectedChat->user->last_name;
                        } else {
                            $userName = $selectedChat->name.' (Jo Klient)';
                        }
                    } else {
                        $userName = $selectedChat->vendor->name;
                    }
                @endphp
                <div class="single-chat chat-form">
                    <div class="chat-header"><i class="fas fa-chevron-left" wire:click="goBack"></i> {{ $userName }} <span class="status {{ ($selectedChat->vendorLiveStatus()) ? 'active' : '' }}"></span></div>
                    <div class="chat-messages ch{{$userOrVendor}}">
                        <div class="loading-messages {{ ($showLoading)? 'show' : '' }}">
                            <div class="sk-circle">
                                <div class="sk-circle1 sk-child"></div>
                                <div class="sk-circle2 sk-child"></div>
                                <div class="sk-circle3 sk-child"></div>
                                <div class="sk-circle4 sk-child"></div>
                                <div class="sk-circle5 sk-child"></div>
                                <div class="sk-circle6 sk-child"></div>
                                <div class="sk-circle7 sk-child"></div>
                                <div class="sk-circle8 sk-child"></div>
                                <div class="sk-circle9 sk-child"></div>
                                <div class="sk-circle10 sk-child"></div>
                                <div class="sk-circle11 sk-child"></div>
                                <div class="sk-circle12 sk-child"></div>
                            </div>
                        </div>
                        @foreach($selectedMessage as $message)
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
                    </div>
                    <div class="chat-footer">
                        <div class="chat-text">
                            <div class="chat-input row">
                                <textarea name="message" id="message" class="form-control {{ ($messageError) ? 'required' : '' }}" placeholder="Shkruani mesazhin tuaj..." wire:model.defer="chat" wire:keydown.enter="sendMessage"></textarea>
                            </div>
                            <div class="chat-send" wire:click="sendMessage">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="chats-group">
                    @if($isLogged && $isVendor)
                    <div class="chat-vendor">
                        <div class="chat-header">Chat i Dyqanit</div>
                        {{-- <div class="chat-search">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Kërko sipas Emrit">
                            </div>
                        </div> --}}
                        <div class="chat-list">
                            @if(count($vChats))
                            <ul class="chats">
                                @foreach($vChats as $chat)
                                    @php
                                        $userName = ($chat->logged) ? $chat->user->first_name.' '.$chat->user->last_name : $chat->name.' (Jo Klient)';
                                        $lastMessage = $chat->messages()->orderBy('id', 'desc')->first();
                                    @endphp
                                    <li class="chat-single {{ (isset($lastMessage['seen']) && ($lastMessage['way'] == 1 && $lastMessage['seen'] == 0)) ? 'ns' : '' }}" wire:click="selectvChat('{{ $chat->id }}')">
                                        <div class="image">
                                            <i class="far fa-user"></i>
                                        </div>
                                        <div class="chat-info">
                                            <div class="name">{{ $userName }}</div>
                                            <div class="mesage">{{ $lastMessage->message }}</div>
                                        </div>
                                        <div class="chat-status"></div>
                                    </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        @if($tvChats > 5)
                            <div class="chat-more pointer"><a href="{{ route('vendor.chat.index') }}">Shiko të gjitha</a></div>
                        @endif
                    </div>
                    @endif
                    <div class="chat-user">
                        <div class="chat-header">Livechat</div>
                        @if(count($uChats))
                            {{-- <div class="chat-search">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Kërko sipas Emrit">
                                </div>
                            </div> --}}
                            <div class="chat-list">
                                <ul class="chats">
                                    @foreach($uChats as $chat)
                                        @php
                                            $vendor = $chat->vendor;
                                            $lastMessage = $chat->messages()->orderBy('id', 'desc')->first();
                                        @endphp
                                        <li class="chat-single {{ (isset($lastMessage['seen']) && ($lastMessage['way'] == 2 && $lastMessage['seen'] == 0)) ? 'ns' : '' }}" wire:click="selectChat('{{ $chat->id }}')">
                                            <div class="image">
                                                <img src="{{ asset('photos/vendor/'.$vendor->logo_path) }}" alt="">
                                            </div>
                                            <div class="chat-info">
                                                <div class="name">{!! strtoupper($vendor->name) !!} <span class="status {{ ($chat->vendorLiveStatus()) ? 'active' : '' }}"></span></div>
                                                <div class="mesage">{{ $chat->messages()->orderBy('id', 'desc')->first()->message }}</div>
                                            </div>
                                            <div class="chat-status"></div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @if($tuChats > (5 * $loadMorePages))
                                <div class="chat-more"><span class="pointer" wire:click="loadMoreChats">Shiko të gjitha</span></div>
                            @endif
                        @else
                            <div class="divider"></div>
                            <div class="no-chat tcenter p-10">Ju nuk keni asnje bisedë!</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    <script>
        let checkMessages = true;
        document.addEventListener("DOMContentLoaded", () => {
            window.livewire.on('lastMessages', () => {
                lastMessage();
            });
            window.livewire.on('startLoadMessages', () => {
                setTimeout(function(){
                    checkMessages = true;
                    loadMoreMessage();
                }, 300);
            });
            window.livewire.on('oldMessages', (more, position, height) => {
                let messagess = document.querySelector('.single-chat .chat-messages');
                messagess.scrollTop = messagess.scrollHeight - height + 10;
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
        });
        function lastMessage(){
            let messagesHeader = document.querySelector('.single-chat .chat-messages');
            messagesHeader.scrollTop = messagesHeader.scrollHeight - messagesHeader.clientHeight + 10;
            setTimeout(function(){ 
                messagesHeader.scrollTop = messagesHeader.scrollHeight - messagesHeader.clientHeight + 10;
            }, 100);
            setTimeout(function(){ 
                messagesHeader.scrollTop = messagesHeader.scrollHeight - messagesHeader.clientHeight + 10;
            }, 300);
        }
        function loadMoreMessage(){
            let messagesHeader2 = document.querySelector('.single-chat .chat-messages');
            messagesHeader2.onscroll = function (ev) {
                if(messagesHeader2.scrollTop < 30){
                    loadMore(messagesHeader2.scrollTop, messagesHeader2.scrollHeight);
                    checkMessages = false;
                }
            };
        }
        function loadMore(position, height){
            if(checkMessages){
                let loadingS = document.querySelector('.loading-messages');
                if(loadingS && !loadingS.classList.contains('show')){
                    document.querySelector('.loading-messages').classList.add('show');
                }
                window.livewire.emit('load-more-header', position, height);
            }
        }
    </script>
</div>
    