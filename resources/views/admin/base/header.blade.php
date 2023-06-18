<style>
    .user-img {
        background-color: #fff;
        color: #000;
        font-size: 18px;
        font-weight: 700;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }
    .admin-header {
        width: 100%;
        padding: 5px 10px;
        background: #f00;
        color: #fff;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    </style>
    @if(auth()->user()->aroles && session('logAsVendor'))
    <div class="admin-header">
        <div class="left">Ju jeni futur në menaxhimin e Dyqanit " {{ current_vendor()->name }} "</div>
        <div class="right">
            <a href="{{ route('admin.vendors.return') }}"><div class="btn btn-info">Kthehu si Administrator</div></a>
        </div>
    </div>
    @endif
    <div class="bg_overlay"></div>
    <div class="header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <a href="@if(Request::routeIs('admin.*')){{ route('admin.home') }}@else{{ route('vendor.home') }}@endif"><img class="admin-logo" src="{{ asset('images/logo.png')}}" alt=""></a>
                <div class="menu-toggle-button">
                    <a class="nav-link" href="javascript:;" id="sidebarCollapse">
                        <div class="my-toggl-icon">
                            <span class="bar1"></span>
                            <span class="bar2"></span>
                            <span class="bar3"></span>
                        </div>
                    </a>
                </div>
                <div class="right-eliment">
                    <ul class="list">
                        <input type="hidden" id="all_notf_count">
                        <li class="bell-area">
                            <a id="notf_conv" class="dropdown-toggle-1" target="_blank"
                                href="{{ route('home') }}">
                                <i class="fas fa-globe-americas"></i>
                            </a>
                        </li>
                        @if(current_vendor())
                        <li class="bell-area">
                            <a id="notf_conv" class="dropdown-toggle-1" target="_blank"
                                href="{{ route('single.vendor', current_vendor()->slug) }}">
                                <i class="fas fa-store"></i>
                            </a>
                        </li>
                        @endif
                        {{-- <li class="login-profile-area">
                            <div class="dropdown-toggle-1"><div class="user-img">MC</div></div>
                            <div class="dropdown-menu">
                                <div class="dropdownmenu-wrapper">
                                    <ul>
                                        <h5>Welcome!</h5>
                                        <li>
                                            <a href="/profile"><i class="fas fa-user"></i> Edit Profile</a>
                                        </li>
                                        <li>
                                            <a href="/password"><i class="fas fa-cog"></i> Change Password</a>
                                        </li>
                                        <li>
                                            <a href="/logout"><i class="fas fa-power-off"></i> Logout</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li> --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>