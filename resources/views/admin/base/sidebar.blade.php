@php
    $currentUrl = '';
    if(Request::routeIs('admin.orders.*')){
        $currentUrl = 'orders';
    } else if(Request::routeIs('admin.products.*')){
        $currentUrl = 'products';
    } else if(Request::routeIs('admin.users.*')){
        $currentUrl = 'users';
    } else if (Request::routeIs('admin.vendors.*')) {
        $currentUrl = 'vendors';
    } else if (Request::routeIs('admin.settings.*')) {
        $currentUrl = 'settings';
    } else if (Request::routeIs('admin.homesettings.*')) {
        $currentUrl = 'settings';
    } else if (Request::routeIs('admin.coupons.*')) {
        $currentUrl = 'coupons';
    }
    if(session('logAsVendor')){
        session()->forget('logAsVendor');
    }
@endphp
<style>
    #sidebar ul>li>ul>li a {
        justify-content: left;
    }
    #sidebar ul li ul li a::before {
        font-weight: 700;
    }
    #sidebar ul>li>ul>li a.accordion-toggle {
        color: #8a8a8a;
    }
    #sidebar ul>li>ul>li a.accordion-toggle:hover {
        color: #333;
        background: #e8e8e8;
    }
    #sidebar ul>li>ul>li>ul>li a {
        margin-left: 10px;
    }
</style>
<nav id="sidebar" class="nav-sidebar">
    <ul class="list-unstyled components" id="accordion">
        <li @if(Request::routeIs('admin.home')) class="active" @endif>
            <a href="/admin" class="wave-effect waves-effect waves-button"><i
                    class="fa fa-home mr-2"></i>Dashboard</a>
        </li>
        @if(check_permissions('manage_orders'))
        <li @if(Request::routeIs('admin.orders.*')) class="active" @endif>
            <a href="#order" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="{{($currentUrl=='orders') ? 'true' : 'false'}}"><i class="fas fa-hand-holding-usd"></i>Porositë</a>
            <ul class="collapse list-unstyled {{($currentUrl=='orders') ? 'show' : ''}}" id="order" data-parent="#accordion">
                <li>
                    <a href="{{ route('admin.orders.index') }}" @if($currentUrl=='orders' && Request::routeIs('admin.orders.index')) class="active" @endif> Të gjitha Porositë</a>
                </li>
                <li>
                    <a href="{{ route('admin.orders.pending') }}" @if($currentUrl=='orders' && Request::routeIs('admin.orders.pending')) class="active" @endif> Porositë në pritje</a>
                </li>
                <li>
                    <a href="{{ route('admin.orders.completed') }}" @if($currentUrl=='orders' && Request::routeIs('admin.orders.completed')) class="active" @endif> Porositë e Dërguara</a>
                </li>
                <li>
                    <a href="{{ route('admin.orders.canceled') }}" @if($currentUrl=='orders' && Request::routeIs('admin.orders.canceled')) class="active" @endif> Porositë e Anulluara</a>
                </li>
            </ul>
        </li>
        @endif
        @if(check_permissions('manage_products'))
        <li @if(Request::routeIs('admin.products.*')) class="active" @endif>
            <a href="#menu2" class="accordion-toggle wave-effect waves-effect waves-button {{($currentUrl=='products') ? 'active' : ''}}" data-toggle="collapse"
                aria-expanded="{{($currentUrl=='products') ? 'true' : 'false'}}">
                <i class="fas fa-shopping-cart"></i>Produktet
            </a>
            <ul class="collapse list-unstyled {{($currentUrl=='products') ? 'show' : ''}}" id="menu2" data-parent="#accordion">
                <li>
                    <a href="{{ route('admin.products.show') }}" @if($currentUrl=='products' && Request::routeIs('admin.products.show')) class="active" @endif><span>Të gjithë Produktet</span></a>
                </li>
                <li>
                    <a href="{{route('admin.products.categories.index')}}" @if($currentUrl=='products' && Request::routeIs('admin.products.categories.index')) class="active" @endif><span>Kategoritë</span></a>
                </li>
                <li>
                    <a href="{{route('admin.products.tags.index')}}" @if($currentUrl=='products' && Request::routeIs('admin.products.tags.index')) class="active" @endif><span>Tags</span></a>
                </li>
                <li>
                    <a href="{{route('admin.products.brands.index')}}" @if($currentUrl=='products' && Request::routeIs('admin.products.brands.index')) class="active" @endif><span>Brandet</span></a>
                </li>
                <li>
                    <a href="{{route('admin.products.colors.index')}}" @if($currentUrl=='products' && Request::routeIs('admin.products.colors.index')) class="active" @endif><span>Ngjyrat</span></a>
                </li>
                <li>
                    <a href="{{route('admin.products.variants.index')}}" @if($currentUrl=='products' && Request::routeIs('admin.products.variants.index')) class="active" @endif><span>Variantet</span></a>
                </li>
                </li>
                <li>
                    <a href="{{route('admin.products.reports.index')}}" @if($currentUrl=='products' && Request::routeIs('admin.products.reports.*')) class="active" @endif><span>Raportimet</span></a>
                </li>
            </ul>
        </li>
        @endif
        {{-- <li>
            <a href="#affiliateprod" class="accordion-toggle wave-effect waves-effect waves-button"
                data-toggle="collapse" aria-expanded="false">
                <i class="icofont-cart"></i>Affiliate Products
            </a>
            <ul class="collapse list-unstyled" id="affiliateprod" data-parent="#accordion">
                <li>
                    <a href="/admin/products/import/create"><span>Add Affiliate Product</span></a>
                </li>
                <li>
                    <a href="/admin/products/import/index"><span>All Affiliate Products</span></a>
                </li>
            </ul>
        </li> --}}
        @if(check_permissions('manage_users'))
        <li>
            <a href="{{ route('admin.users.index') }}" class=" wave-effect waves-effect waves-button {{($currentUrl=='users') ? 'active' : ''}}"><i class="fas fa-users"></i>Lista e Përdoruesve</a>
        </li>
        @endif
        @if(check_permissions('manage_vendors'))
        <li @if(Request::routeIs('admin.vendors.*')) class="active" @endif>
            <a href="#vendor" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="{{($currentUrl=='vendors') ? 'true' : 'false'}}">
                <i class="fas fa-store"></i>Dyqanet
            </a>
            <ul class="collapse list-unstyled {{($currentUrl=='vendors') ? 'show' : ''}}" id="vendor" data-parent="#accordion">
                <li>
                    <a href="{{ route('admin.vendors.index') }}"  @if($currentUrl=='vendors' && Request::routeIs('admin.vendors.index')) class="active" @endif><span>Lista e Dyqaneve</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.vendors.membership.invoice') }}" @if($currentUrl=='vendors' && Request::routeIs('admin.vendors.membership.invoice')) class="active" @endif><span>Faturat</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.vendors.requests') }}"  @if($currentUrl=='vendors' && Request::routeIs('admin.vendors.requests')) class="active" @endif><span>Kërkesat regjistrim</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.vendors.namechange') }}"  @if($currentUrl=='vendors' && Request::routeIs('admin.vendors.namechange')) class="active" @endif><span>Kërkesat për ndryshim emri</span></a>
                </li>
                {{-- <li>
                    <a href="/admin/vendors/subs"><span>Vendor Subscriptions</span></a>
                </li>
                <li>
                    <a href="/admin/vendor/color"><span>Default Background</span></a>
                </li> --}}
            </ul>
        </li>
        @endif
        {{-- <li>
            <a href="#vendor1" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="false">
                <i class="icofont-verification-check"></i>Vendor Verifications
            </a>
            <ul class="collapse list-unstyled" id="vendor1" data-parent="#accordion">
                <li>
                    <a href="/admin/verificatons"><span>All Verifications</span></a>
                </li>
                <li>
                    <a href="/admin/verificatons/pendings"><span>Pending Verifications</span></a>
                </li>
            </ul>
        </li> --}}
        {{-- <li>
            <a href="/admin/subscription" class=" wave-effect waves-effect waves-button"><i
                    class="fas fa-dollar-sign"></i>Vendor Subscription Plans</a>
        </li> --}}
        {{-- <li>
            <a href="#menu5" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="false"><i class="fas fa-sitemap"></i>Manage Categories</a>
            <ul class="collapse list-unstyled
            " id="menu5" data-parent="#accordion">
                <li class="">
                    <a href="/admin/category"><span>Main Category</span></a>
                </li>
                <li class="">
                    <a href="/admin/subcategory"><span>Sub Category</span></a>
                </li>
                <li class="">
                    <a href="/admin/childcategory"><span>Child Category</span></a>
                </li>
            </ul>
        </li> --}}
        {{-- <li>
            <a href="/admin/products/import"><i class="fas fa-upload"></i>Bulk Product Upload</a>
        </li> --}}
        {{-- <li>
            <a href="#menu4" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="false">
                <i class="icofont-speech-comments"></i>Product Discussion
            </a>
            <ul class="collapse list-unstyled" id="menu4" data-parent="#accordion">
                <li>
                    <a href="/admin/ratings"><span>Product Reviews</span></a>
                </li>
                <li>
                    <a href="/admin/comments"><span>Comments</span></a>
                </li>
                <li>
                    <a href="/admin/reports"><span>Reports</span></a>
                </li>
            </ul>
        </li> --}}
        @if(check_permissions('manage_offers'))
        <li>
            <a href="{{ route('admin.offers.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('admin.offers.*')) active @endif"><i class="fas fa-percentage"></i>Ofertat</a>
        </li>
        @endif
        @if(check_permissions('manage_offers'))
        <li>
            <a href="{{ route('admin.coupons.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('admin.coupons.*')) active @endif"><i class="fas fa-ticket-alt"></i>Kuponat</a>
        </li>
        @endif
        {{-- <li>
            <a href="#blog" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="false">
                <i class="fas fa-fw fa-newspaper"></i>Blog
            </a>
            <ul class="collapse list-unstyled" id="blog" data-parent="#accordion">
                <li>
                    <a href="/admin/blog/category"><span>Categories</span></a>
                </li>
                <li>
                    <a href="/admin/blog"><span>Posts</span></a>
                </li>
            </ul>
        </li> --}}
        @if(check_permissions('manage_supports'))
        <li>
            <a href="{{ route('admin.ticket.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('admin.ticket.*')) active @endif"><i class="far fa-question-circle"></i>Kërkesa për Suport</a>
        </li>
        @endif
        @if(check_permissions('manage_stories'))
        <li>
            <a href="{{ route('admin.stories.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('admin.stories.*')) active @endif"><i class="fas fa-photo-video"></i>Menaxho Story-it</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor'))
        <li>
            <a href="{{ route('admin.notifications.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('admin.notifications.*')) active @endif"><i class="fas fa-bell"></i>Njoftimet</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor'))
        <li>
            <a href="{{ route('admin.ads.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('admin.ads.*')) active @endif"><i class="fas fa-ad"></i>Reklamat</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor'))
        <li>
            <a href="{{ route('admin.pages.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('admin.pages.*')) active @endif"><i class="fas fa-file-code"></i>Menaxho Faqet</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor'))
        <li @if(Request::routeIs('admin.settings.*') || Request::routeIs('admin.homesettings.*')) class="active" @endif>
            <a href="#general" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="{{($currentUrl=='settings') ? 'true' : 'false'}}">
                <i class="fas fa-cogs"></i>Rregullimet
            </a>
            <ul class="collapse list-unstyled {{($currentUrl=='settings') ? 'show' : ''}}" id="general" data-parent="#accordion">
                <li>
                    <a href="{{ route('admin.settings.main') }}" @if($currentUrl=='settings' && Request::routeIs('admin.settings.main')) class="active" @endif><span>Kryesore</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.countries.index') }}" @if($currentUrl=='settings' && Request::routeIs('admin.settings.countries.*')) class="active" @endif><span>Shtetet & Qytetet</span></a>
                </li>
                <li id="faqjaKryesore">
                    <a href="#faqjaKryesore">---- Faqja Kryesore ----</a>
                </li>
                <li>
                    <a href="{{ route('admin.homesettings.slider') }}" @if($currentUrl=='settings' && Request::routeIs('admin.homesettings.slider')) class="active" @endif><span>Fotot Kryesore Web</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.homesettings.slidermobile') }}" @if($currentUrl=='settings' && Request::routeIs('admin.homesettings.slidermobile')) class="active" @endif><span>Slider Kryesore Mobile</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.homesettings.features') }}" @if($currentUrl=='settings' && Request::routeIs('admin.homesettings.features')) class="active" @endif><span>Vecoritë Elefandi</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.homesettings.featuredProduct') }}" @if($currentUrl=='settings' && Request::routeIs('admin.homesettings.featuredProduct')) class="active" @endif><span>Produktet e Preferuara</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.homesettings.trending.index') }}" @if($currentUrl=='settings' && Request::routeIs('admin.homesettings.trending.*')) class="active" @endif><span>Trendet e Fundit</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.homesettings.categories.index') }}" @if($currentUrl=='settings' && Request::routeIs('admin.homesettings.categories.*')) class="active" @endif><span>Kategoritë Slider</span></a>
                </li>
                <li>
                    <a href="#footer" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse" aria-expanded="false">Social Settings</a>
                    <ul class="collapse list-unstyled show" id="socials" data-parent="#accordion">
                        <li>
                            <a href="{{ route('admin.settings.footer1.index') }}" @if($currentUrl=='settings' && Request::routeIs('admin.settings.footer1.*')) class="active" @endif><span>Footer Col 1</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.footer2.index') }}" @if($currentUrl=='settings' && Request::routeIs('admin.settings.footer2.*')) class="active" @endif><span>Footer Col 2</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.footer3.index') }}" @if($currentUrl=='settings' && Request::routeIs('admin.settings.footer3.*')) class="active" @endif><span>Footer Col 3</span></a>
                        </li>
                    </ul>
                </li>
                {{-- <li>
                    <a href="/admin/settings/sliders"><span>Sliderat</span></a>
                </li>
                <li>
                    <a href="/admin/settings/banners"><span>Banerat</span></a>
                </li>
                <li>
                    <a href="/admin/shipping"><span>Shipping Methods</span></a>
                </li>
                <li>
                    <a href="/admin/package"><span>Packagings</span></a>
                </li>
                <li>
                    <a href="/admin/pickup"><span>Pickup Locations</span></a>
                </li>
                <li>
                    <a href="/admin/general-settings/contents"><span>Website Contents</span></a>
                </li>
                <li>
                    <a href="/admin/general-settings/footer"><span>Footer</span></a>
                </li>
                <li>
                    <a href="/admin/general-settings/affilate"><span>Affiliate Information</span></a>
                </li>
                <li>
                    <a href="/admin/general-settings/popup"><span>Popup Banner</span></a>
                </li>
                <li>
                    <a href="/admin/general-settings/error-banner"><span>Error Banner</span></a>
                </li>
                <li>
                    <a href="/admin/general-settings/maintenance"><span>Website Maintenance</span></a>
                </li> --}}
            </ul>
        </li>
        @endif
        {{-- <li>
            <a href="#homepage" class="accordion-toggle wave-effect waves-effect waves-button {{ ($currentUrl == 'homesettings') ? 'active' : '' }}" data-toggle="collapse"
                aria-expanded="{{($currentUrl=='homesettings') ? 'true' : 'false'}}">
                <i class="fas fa-edit"></i>Rregullimet e Kreut
            </a>
            <ul class="collapse list-unstyled {{($currentUrl=='homesettings') ? 'show' : ''}}" id="homepage" data-parent="#accordion">
                <li>
                    <a href="/admin/slider"><span>Sliders</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.homesettings.features') }}" @if($currentUrl=='homesettings' && Request::routeIs('admin.homesettings.features')) class="active" @endif><span>Vecoritë Elefandi</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.homesettings.featuredProduct') }}" @if($currentUrl=='homesettings' && Request::routeIs('admin.homesettings.featuredProduct')) class="active" @endif><span>Produktet e Preferuara</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.homesettings.trending.index') }}" @if($currentUrl=='homesettings' && Request::routeIs('admin.homesettings.trending.*')) class="active" @endif><span>Trendet e Fundit</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.homesettings.categories.index') }}" @if($currentUrl=='homesettings' && Request::routeIs('admin.homesettings.categories.*')) class="active" @endif><span>Kategoritë Slider</span></a>
                </li>
                <li>
                    <a href="/admin/page-settings/best-seller"><span>Right Side Banner1</span></a>
                </li>
                <li>
                    <a href="/admin/page-settings/big-save"><span>Right Side Banner2</span></a>
                </li>
                <li>
                    <a href="/admin/top/small/banner"><span>Top Small Banners</span></a>
                </li>
                <li>
                    <a href="/admin/large/banner"><span>Large Banners</span></a>
                </li>
                <li>
                    <a href="/admin/bottom/small/banner"><span>Bottom Small Banners</span></a>
                </li>
                <li>
                    <a href="/admin/review"><span>Reviews</span></a>
                </li>
                <li>
                    <a href="/admin/partner"><span>Partners</span></a>
                </li>
                <li>
                    <a href="/admin/page-settings/customize"><span>Home Page Customization</span></a>
                </li>
            </ul>
        </li> --}}
        @if(check_permissions('manage_vendor'))
        <li>
            <a href="{{ route('admin.emails.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('admin.emails.*')) active @endif"><i class="fas fa-user-tag"></i>Emailet</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor'))
        <li>
            <a href="{{ route('admin.roles.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('admin.roles.*')) active @endif"><i class="fas fa-user-tag"></i>Menaxho Rolet</a>
        </li>
        @endif
        @if(check_permissions('manage_vendor'))
        <li>
            <a href="{{ route('admin.staff.index') }}" class=" wave-effect waves-effect waves-button @if(Request::routeIs('admin.staff.*')) active @endif"><i class="fas fa-user-secret"></i>Menaxho Stafin</a>
        </li>
        @endif
        {{-- <li>
            <a href="#menu" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="false">
                <i class="fas fa-file-code"></i>Menu Page Settings
            </a>
            <ul class="collapse list-unstyled" id="menu" data-parent="#accordion">
                <li>
                    <a href="/admin/faq"><span>FAQ Page</span></a>
                </li>
                <li>
                    <a href="/admin/page-settings/contact"><span>Contact Us Page</span></a>
                </li>
                <li>
                    <a href="/admin/page"><span>Other Pages</span></a>
                </li>
            </ul>
        </li> --}}
        {{-- <li>
            <a href="#emails" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="false">
                <i class="fas fa-at"></i>Email Settings
            </a>
            <ul class="collapse list-unstyled" id="emails" data-parent="#accordion">
                <li><a href="/admin/email-templates"><span>Email Template</span></a></li>
                <li><a href="/admin/email-config"><span>Email Configurations</span></a></li>
                <li><a href="/admin/groupemail"><span>Group Email</span></a></li>
            </ul>
        </li> --}}
        {{-- <li>
            <a href="#payments" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="false">
                <i class="fas fa-file-code"></i>Payment Settings
            </a>
            <ul class="collapse list-unstyled" id="payments" data-parent="#accordion">
                <li><a href="/admin/payment-informations"><span>Payment Information</span></a></li>
                <li><a href="/admin/paymentgateway"><span>Payment Gateways</span></a></li>
                <li><a href="/admin/currency"><span>Currencies</span></a></li>
            </ul>
        </li> --}}
        {{-- <li>
            <a href="#socials" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="false">
                <i class="fas fa-paper-plane"></i>Social Settings
            </a>
            <ul class="collapse list-unstyled" id="socials" data-parent="#accordion">
                <li><a href="/admin/social"><span>Social Links</span></a></li>
                <li><a href="/admin/social/facebook"><span>Facebook Login</span></a></li>
                <li><a href="/admin/social/google"><span>Google Login</span></a></li>
            </ul>
        </li> --}}
        {{-- <li>
            <a href="#langs" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="false">
                <i class="fas fa-language"></i>Language Settings
            </a>
            <ul class="collapse list-unstyled" id="langs" data-parent="#accordion">
                <li><a href="/admin/languages"><span>Website Language</span></a></li>
                <li><a href="/admin/adminlanguages"><span>Admin Panel Language</span></a></li>
            </ul>
        </li> --}}
        {{-- <li>
            <a href="#seoTools" class="accordion-toggle wave-effect waves-effect waves-button" data-toggle="collapse"
                aria-expanded="false">
                <i class="fas fa-wrench"></i>SEO Tools
            </a>
            <ul class="collapse list-unstyled" id="seoTools" data-parent="#accordion">
                <li>
                    <a href="/admin/products/popular/30"><span>Popular Products</span></a>
                </li>
                <li>
                    <a href="/admin/seotools/analytics"><span>Google Analytics</span></a>
                </li>
                <li>
                    <a href="/admin/seotools/keywords"><span>Website Meta Keywords</span></a>
                </li>
            </ul>
        </li> --}}
        {{-- <li>
            <a href="/admin/subscribers" class=" wave-effect waves-effect waves-button"><i class="fas fa-users-cog mr-2"></i>Subscribers</a>
        </li> --}}
    </ul>
</nav>