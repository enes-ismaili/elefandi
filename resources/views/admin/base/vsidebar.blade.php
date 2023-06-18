@php
    $currentUrl = '';
    if(Request::routeIs('vendor.products.*')){
        $currentUrl = 'products';
    } else if (Request::routeIs('vendor.orders.*')) {
        $currentUrl = 'orders';
    }
@endphp
<nav id="sidebar" class="nav-sidebar">
    <ul class="list-unstyled components" id="accordion">
        <li @if(Request::routeIs('vendor.home')) class="active" @endif>
            <a href="{{ route('vendor.home') }}" class="wave-effect waves-effect waves-button @if(Request::routeIs('vendor.home')) active @endif"><i class="fa fa-home mr-2"></i>Dashboard</a>
        </li>
        @if(check_permissions('manage_orders') && vendor_status())
        <li @if(Request::routeIs('vendor.orders.*')) class="active" @endif>
            <a href="#order" class="accordion-toggle wave-effect waves-effect waves-button{{($currentUrl=='orders') ? 'active' : ''}}" data-toggle="collapse"
                aria-expanded="{{($currentUrl=='orders') ? 'true' : 'false'}}"><i class="fas fa-hand-holding-usd"></i>Porositë</a>
            <ul class="collapse list-unstyled {{($currentUrl=='orders') ? 'show' : ''}}" id="order" data-parent="#accordion">
                <li>
                    <a href="{{ route('vendor.orders.index') }}" @if(Request::routeIs('vendor.orders.index')) class="active" @endif> Të gjitha Porositë</a>
                </li>
                <li>
                    <a href="{{ route('vendor.orders.pending') }}" @if(Request::routeIs('vendor.orders.pending')) class="active" @endif> Porositë në pritje</a>
                </li>
                <li>
                    <a href="{{ route('vendor.orders.completed') }}" @if(Request::routeIs('vendor.orders.completed')) class="active" @endif> Porositë e Dërguara</a>
                </li>
                <li>
                    <a href="{{ route('vendor.orders.canceled') }}" @if(Request::routeIs('vendor.orders.canceled')) class="active" @endif> Porositë e Anulluara</a>
                </li>
            </ul>
        </li>
        @endif
        @if(check_permissions('manage_products') && vendor_status())
        <li @if(Request::routeIs('vendor.products.*')) class="active" @endif>
            <a href="#menu2" class="accordion-toggle wave-effect waves-effect waves-button {{($currentUrl=='products') ? 'active' : ''}}" data-toggle="collapse"
                aria-expanded="{{($currentUrl=='products') ? 'true' : 'false'}}">
                <i class="fas fa-shopping-cart"></i>Produktet
            </a>
            <ul class="collapse list-unstyled {{($currentUrl=='products') ? 'show' : ''}}" id="menu2" data-parent="#accordion">
                <li>
                    <a href="{{ route('vendor.products.index') }}" @if(Request::routeIs('vendor.products.index') && !Request::routeIs('vendor.products.new')) class="active" @endif><span>Të gjithë Produktet</span></a>
                </li>
                <li>
                    <a href="{{ route('vendor.products.new') }}" @if(Request::routeIs('vendor.products.new')) class="active" @endif><span>Shto Produkt</span></a>
                </li>
            </ul>
        </li>
        @endif
        @if(check_permissions('manage_offers') && vendor_status())
        <li>
            <a href="{{ route('vendor.offers.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.offers.*')) active @endif"><i class="fas fa-percentage"></i>Ofertat</a>
        </li>
        @endif
        @if(check_permissions('manage_offers') && vendor_status())
        <li>
            <a href="{{ route('vendor.coupons.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.coupons.*')) active @endif"><i class="fas fa-ticket-alt"></i>Kuponat</a>
        </li>
        @endif
        @if(check_permissions('manage_chat'))
        <li>
            <a href="{{ route('vendor.chat.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.chat.*')) active @endif"><i class="fas fa-comments"></i>Chat</a>
        </li>
        @endif
        @if(check_permissions('manage_supports'))
        <li>
            <a href="{{ route('vendor.ticket.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.ticket.*')) active @endif"><i class="fas fa-question-circle"></i>Kërkesa për Suport</a>
        </li>
        @endif
        @if(check_permissions('manage_stories') && vendor_status())
        <li>
            <a href="{{ route('vendor.stories.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.stories.*')) active @endif"><i class="fas fa-photo-video"></i>Menaxho Storit</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor') && vendor_status())
        <li>
            <a href="{{ route('vendor.notifications.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.notifications.*')) active @endif"><i class="fas fa-bell"></i>Njoftimet</a>
        </li>
        @endif
        @if(check_permissions('manage_ads') && vendor_status())
        <li>
            <a href="{{ route('vendor.ads.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.ads.*')) active @endif"><i class="fas fa-ad"></i>Reklamat</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor'))
        <li>
            <a href="{{ route('vendor.pages.edit') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.pages.*')) active @endif"><i class="fas fa-file-code"></i>Menaxho Faqet</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor'))
        <li>
            <a href="{{ route('vendor.edit.profile') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.edit.*')) active @endif"><i class="fas fa-users-cog mr-2"></i>Rregullimet/Profili</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor'))
        <li>
            <a href="{{ route('vendor.membership.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.membership.*')) active @endif"><i class="fas fa-users-cog mr-2"></i>Menaxho Antarësinë</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor') && vendor_status())
        <li>
            <a href="{{ route('vendor.staff.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('vendor.staff.*')) active @endif"><i class="fas fa-user-secret"></i>Menaxho Stafin</a>
        </li>
        @endif
    </ul>
</nav>