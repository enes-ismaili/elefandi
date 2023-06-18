<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Chat</h4>
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Chat</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area chat-list">
        <div class="row">
            <div class="col-12">
                @livewire('chat.vendor-chat', ['chats'=>$chats])
            </div>
        </div>
    </div>
</x-admin-layout>