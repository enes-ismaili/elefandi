<div class="chat-form">
    <div id="chaticon" class="chat-icon {{ ($openChat) ? 'hide' : '' }}" wire:click="openChat">
        <div class="chaticon">
            <i class="far fa-comment-alt"></i>
        </div>
    </div>
    <div class="background-chat {{ ($openChat) ? 'show' : '' }}" wire:click="closeChat"></div>
    <div class="chat-box {{ ($openChat) ? 'show' : '' }}">
        <div class="chat-header">
            <div class="left">
                <div class="title">Livechat</div>
                <div class="vendor"><span class="status {{ ($vendorStatus) ? 'active' : '' }}"></span> Dyqani {{ $vendorName }}</div>
            </div>
            <div class="right">
                <div class="close-chat" wire:click="closeChat">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div>
        <div class="chat-messages">
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
            @if($hasChat)
                @foreach($messages as $message)
                    {{-- @ray($message) --}}
                    @if($message['type'] == 2)
                        @php
                            $currProduct = App\Models\Product::where('id', '=', $message['message'])->first();
                        @endphp
                        @if($currProduct)
                        <div class="message w1 p">
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
                        <div class="message w{{ $message['way'] }}"><div class="txt">{{ $message['message'] }}</div></div>
                    @endif
                @endforeach
            @else
                <div class="message w1"><div class="txt">Pershendetje, si mund të ju ndihmoj?</div></div>
            @endif
            {{-- <div class="message w1"><div class="txt">Pershendetje, si mund të ju ndihmoj?</div></div>
            <div class="message w2 p">
                <div class="chat-product">
                    <div class="chat-product-badge">-50%</div><a href="shikoProduktin?id=757">
                        <div class="chat-product-thumb"><img
                                src="https://elefandi.com/uploads/produktet/127996259_429091844764379_4905342935096819107_n.jpg"></div>
                        <div class="chat-product-title">Këmish SlimFit Nr. S</div>
                        <div class="chat-product-price onsale">15€ <del>25€</del></div>
                    </a>
                    <div class="chat-product-actions"><a style="cursor:pointer;"
                            onclick="if (!window.__cfRLUnblockHandlers) return false; addToCart(757,1);" data-toggle="tooltip"
                            data-placement="top" title="Shtoni produktin në shportë"><i class="icon-cart"></i></a><a
                            style="cursor:pointer;" onclick="if (!window.__cfRLUnblockHandlers) return false; addToWishlist(757);"
                            data-toggle="tooltip" data-placement="top" title="Shtoni në listen e dëshirave"><i
                                class="icon-heart"></i></a></div>
                </div>
            </div>
            <div class="message w2"><div class="txt">Ckemi</div></div> --}}
        </div>
        <div class="chat-footer">
            @if(!$isLoggedIn && $showName)
            <div class="chat-name row">
                <input name="name" id="name" class="form-control {{ ($nameError) ? 'required' : '' }}" placeholder="Emri juaj..." wire:model.defer="chatname" required>
            </div>
            @endif
            <div class="chat-text">
                <div class="chat-input row">
                    <textarea name="message" id="message" class="form-control {{ ($messageError) ? 'required' : '' }}" placeholder="Shkruani mesazhin tuaj {{ $chatname }}..." wire:model.defer="chat" wire:keydown.enter="sendMessage"></textarea>
                </div>
                <div class="chat-send" wire:click="sendMessage">
                    <i class="fas fa-paper-plane"></i>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let htmlS = document.getElementsByTagName('html')[0];
            let checkMessages = true;
            window.livewire.on('openChatJs', () => {
                if(!htmlS.classList.contains('noscroll')){
                    htmlS.classList.add('noscroll');
                }
                lastMessage();
            });
            window.livewire.on('closeChatJs', () => {
                if(htmlS.classList.contains('noscroll')){
                    htmlS.classList.remove('noscroll');
                }
            });
            window.livewire.on('lastMessagesV', () => {
                lastMessage();
            });
            window.livewire.on('oldMessagesV', (more, position, height) => {
                let messagess = document.querySelector('.chat-box .chat-messages');
                messagess.scrollTop = messagess.scrollHeight - height + 10;
                setTimeout( () => {
                    let loadingS = document.querySelector('.chat-box .loading-messages');
                    if(loadingS && loadingS.classList.contains('show')){
                        loadingS.classList.remove('show');
                    }
                    if(more){
                        checkMessages = true;
                    }
                }, 600)
            });
            function lastMessage(){
                let messagess = document.querySelector('.chat-box .chat-messages');
                messagess.scrollTop = messagess.scrollHeight - messagess.clientHeight + 10;
                setTimeout(function(){ 
                    messagess.scrollTop = messagess.scrollHeight - messagess.clientHeight + 10;
                 }, 100);
                setTimeout(function(){ 
                    messagess.scrollTop = messagess.scrollHeight - messagess.clientHeight + 10;
                 }, 300);
            }
            let messages = document.querySelector('.chat-box .chat-messages');
            messages.onscroll = function (ev) {
                if(messages.scrollTop < 30){
                    loadMore(messages.scrollTop, messages.scrollHeight);
                    checkMessages = false;
                }
            };
            function loadMore(position, height){
                if(checkMessages){
                    let loadingS = document.querySelector('.chat-box .loading-messages');
                    if(loadingS && !loadingS.classList.contains('show')){
                        loadingS.classList.add('show');
                    }
                    window.livewire.emit('load-more', position, height);
                }
            }
        });
    </script>
</div>
