
<nav id="sidebar" class="nav-sidebar">
    <ul class="links">
        <li @if(Request::is('profile/orders') || Request::is('profile/orders/*')) class="active" @endif>
            <a href="{{ route('profile.orders.index') }}">Porosite</a>
        </li>
        <li @if(Request::routeIs('profile.edit')) class="active" @endif>
            <a href="{{ route('profile.edit') }}">Ndrysho Profilin</a>
        </li>
        <li @if(Request::routeIs('profile.address')) class="active" @endif>
            <a href="{{ route('profile.address') }}">Adresat e dërgimit</a>
        </li>
        <li @if(Request::routeIs('profile.ticket.*')) class="active" @endif>
            <a href="{{ route('profile.ticket.index') }}">Kërkesa për Suport</a>
        </li>
        @if(current_user()->vendor())
            <li>
                <a href="{{ route('vendor.home') }}">Menaxhimi i Dyqanit</a>
            </li>
        @endif
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</a>
            </form>
        </li>
    </ul>
</nav>