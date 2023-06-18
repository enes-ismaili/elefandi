<footer class="footer">
    <div class="container">
        @php
            $footerCol1 = App\Models\footercol1::orderBy('corder')->get();
            $footerCol2 = App\Models\footercol2::orderBy('corder')->get();
            $footerCol3 = App\Models\footercol3::orderBy('corder')->get();
        @endphp
        <div class="footer-top">
            <div class="footer-col footer-1">
                <div class="logo">
                    <img src="{{ (isset($allSettings['footer_logo'])) ? asset('photos/images/'.$allSettings['footer_logo']) : asset('images/logo.png') }}" alt="">
                </div>
                <div class="site-info">
                    <p>Telefononi ne</p>
                    <p class="telephone">
                        @if(isset($allSettings['footertel']))
                        <a href="tel:{{ $allSettings['footertel'] }}">{{ $allSettings['footertel'] }}</a>
                        @endif
                    </p>
                    <p>
                        {{ (isset($allSettings['footeraddress'])) ? $allSettings['footeraddress'] : '' }}
                    </p>
                    <p>{!! (isset($allSettings['footermail'])) ? '<b>'.$allSettings['footermail'].'</b>' : '' !!}</p>
                </div>
            </div>
            <div class="footer-menus">
                <div class="footer-col footer-2 footer-accordion">
                    <div class="title">
                        <h4>Kujdesi ndaj klientit</h4>
                    </div>
                    @php
                        // $dergesat = App\Models\Pages::find(1);
                        // $dergesatLink = '#';
                        // if($dergesat) $dergesatLink = route('pages.single', $dergesat->slug);
                        // $kthimi = App\Models\Pages::find(2);
                        // $kthimiLink = '#';
                        // if($kthimi) $kthimiLink = route('pages.single', $dergesat->slug);
                    @endphp
                    <ul>
                        @foreach($footerCol1 as $item)
                            <li>
                                <a href="{{ $item->link }}">{{ $item->name }}</a>
                            </li>
                        @endforeach
                        {{-- <li><a href="{{ route('view.wishlist') }}">Lista e dëshirave</a></li>
                        <li><a href="{{ route('view.track') }}">Gjurimi i dërgesës</a></li>
                        <li><a href="{{ $dergesatLink }}">Dërgesat</a></li>
                        <li><a href="{{ $kthimiLink }}">Kthimi i mallit</a></li>
                        <li><a href="{{ route('view.contact') }}">Kontaktoni</a></li> --}}
                    </ul>
                </div>
                @php
                    // $rrethnesh = App\Models\Pages::find(4);
                    // $rrethneshLink = '#';
                    // if($rrethnesh) $rrethneshLink = route('pages.single', $dergesat->slug);
                    // $mundesipunesimi = App\Models\Pages::find(5);
                    // $mundesipunesimiLink = '#';
                    // if($mundesipunesimi) $mundesipunesimiLink = route('pages.single', $dergesat->slug);
                    // $njoftimetmedia = App\Models\Pages::find(7);
                    // $njoftimetmediaLink = '#';
                    // if($njoftimetmedia) $njoftimetmediaLink = route('pages.single', $dergesat->slug);
                    // $politikat = App\Models\Pages::find(7);
                    // $politikatLink = '#';
                    // if($politikat) $politikatLink = route('pages.single', $dergesat->slug);
                    // $kushtet = App\Models\Pages::find(8);
                    // $kushtetLink = '#';
                    // if($kushtet) $kushtetLink = route('pages.single', $dergesat->slug);
                @endphp
                <div class="footer-col footer-3 footer-accordion">
                    <div class="title">
                        <h4>Kompania</h4>
                    </div>
                    <ul>
                        @foreach($footerCol2 as $item)
                            <li>
                                <a href="{{ $item->link }}">{{ $item->name }}</a>
                            </li>
                        @endforeach
                        {{-- <li><a href="{{ $rrethneshLink }}">Rreth Nesh</a></li>
                        <li><a href="{{ $mundesipunesimiLink }}">Mundësi Punësimi</a></li>
                        <li><a href="{{ $njoftimetmediaLink }}">Njoftimet & Media</a></li>
                        <li><a href="{{ $politikatLink }}">Politikat e Privatësisë</a></li>
                        <li><a href="{{ $kushtetLink }}">Kushtet e përdorimit</a></li> --}}
                    </ul>
                </div>
                <div class="footer-col footer-4 footer-accordion">
                    <div class="title">
                        <h4>Dyqani</h4>
                    </div>
                    <ul>
                        @foreach($footerCol3 as $item)
                            <li>
                                <a href="{{ $item->link }}">{{ $item->name }}</a>
                            </li>
                        @endforeach
                        {{-- <li><a href="{{ route('profile.edit') }}">Llogaria juaj</a></li>
                        <li><a href="{{ route('view.register') }}">Regjistrohu si konsumator</a></li>
                        <li><a href="{{ route('home.vendor') }}">Regjistrohu si dyqan</a></li>
                        <li><a href="{{ route('view.cart') }}">Shporta juaj</a></li> --}}
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="row">
                <div class="col-6">© 2021 ELEFANDI LLC.</div>
                <div class="col-6 tright">Mënyrat e pagesës: 
                    <svg xmlns="http://www.w3.org/2000/svg" width="42.239" height="33.295" viewBox="0 0 42.239 33.295">
                        <g id="Group_395" data-name="Group 395" transform="translate(935.494 34.628)">
                          <path id="Path_1304" data-name="Path 1304" d="M-858.943-16.238c-1.007.159-1.973.309-2.937.467-.149.024-.205-.032-.256-.162q-1.634-4.216-3.274-8.429c-.055-.141-.085-.292-.146-.429-.169-.375-.167-.4.218-.546q1.939-.757,3.879-1.51l8.691-3.371,7.889-3.058q1.613-.626,3.224-1.254c.085-.033.172-.062.276-.1l1.706,4.4,2.466-.448c.084.447.168.883.247,1.319.176.97.354,1.939.52,2.91.029.167.1.2.253.2.724-.005,1.447,0,2.192,0v15.669c-.121,0-.252,0-.383,0-.69-.009-1.38-.026-2.07-.026-.834,0-1.667.022-2.5.024-1.669,0-3.338,0-5.007,0a.742.742,0,0,0-.356.091q-5.338,3.11-10.67,6.23a1.56,1.56,0,0,1-1.2.151q-3.647-.875-7.295-1.752a1.834,1.834,0,0,0-1.166.087c-.672.286-1.324.618-1.984.932-.059.028-.115.065-.19.108l-3.461-7.437c.348-.153.688-.306,1.031-.453,1.88-.808,3.753-1.633,5.646-2.407a6.771,6.771,0,0,1,1.525-.324q3.758-.582,7.52-1.142,1.224-.184,2.452-.345a1.182,1.182,0,0,1,1.463,1.361,2.03,2.03,0,0,1-1,1.718,6,6,0,0,1-1.284.575c-1.752.572-3.512,1.119-5.27,1.674-.326.1-.357.159-.251.486a1.062,1.062,0,0,0,.647.726c.623.232,1.243.473,1.869.695a.681.681,0,0,0,.367-.008c1.2-.3,2.4-.6,3.6-.909.012,0,.02-.022.051-.06H-857.3a.706.706,0,0,1-.75-.567c.414-.127.835-.242,1.245-.387a3.616,3.616,0,0,1,1.254-.2c1.93.013,3.86.006,5.79.006h11.5c.075,0,.149-.007.234-.011a3.529,3.529,0,0,1,1.247-2.144,3.377,3.377,0,0,1,1.464-.709c.154-.033.2-.091.2-.248q-.007-3.578,0-7.156c0-.112.006-.2-.149-.238a3.461,3.461,0,0,1-2.744-2.777.8.8,0,0,0-.039-.095h-.264q-4.767,0-9.533,0c-.95,0-1.9-.021-2.85-.019-1.71,0-3.419.016-5.129.017-.172,0-.22.064-.26.22a3.439,3.439,0,0,1-2.66,2.648c-.174.043-.2.123-.2.278q.007,2.693,0,5.386ZM-844.749-32.1l-13.315,5.168.017.059,17.227-3.143-.158-.422a3.639,3.639,0,0,1-2.134-.219A3.553,3.553,0,0,1-844.749-32.1ZM-854.867-26.3v.044h17.277c.041-.168,0-.234-.163-.244a3.472,3.472,0,0,1-3.167-2.173c-.057-.125-.113-.161-.244-.136-.661.126-1.324.244-1.986.365l-10.134,1.851Zm-8.316,4.508,1.913,4.924.047-.017-1.039-5.685Zm3.078-.892c-.26.138-.491.279-.737.385a.224.224,0,0,0-.16.3c.121.61.227,1.224.338,1.837l.519,2.849.041,0Z" transform="translate(-59.262)"/>
                          <path id="Path_1305" data-name="Path 1305" d="M-926.275,217.262c-.11.127-.2.31-.341.378-1.482.7-2.972,1.394-4.463,2.081-.318.147-.532.042-.7-.32q-.794-1.705-1.584-3.413l-1.958-4.213c-.039-.084-.08-.167-.116-.253a.472.472,0,0,1,.259-.686q1.774-.827,3.551-1.649c.29-.134.58-.269.873-.4a.463.463,0,0,1,.684.253q1.8,3.871,3.59,7.744C-926.416,216.922-926.361,217.061-926.275,217.262Zm-4.191-.4a.767.767,0,0,0,.756.779.772.772,0,0,0,.79-.775.78.78,0,0,0-.759-.782A.768.768,0,0,0-930.465,216.866Z" transform="translate(0 -221.117)"/>
                          <path id="Path_1306" data-name="Path 1306" d="M-655.97,90.836a5.388,5.388,0,0,1-1.967,4.241,3.99,3.99,0,0,1-5.472-.376.832.832,0,0,1-.284-.81,1.724,1.724,0,0,0-1.024-1.939.21.21,0,0,1-.119-.136,5.4,5.4,0,0,1,2.893-5.989,3.983,3.983,0,0,1,4.429.991A5.652,5.652,0,0,1-655.97,90.836Zm-4.917-3.636a.421.421,0,0,0-.022.13c.044.227-.082.3-.274.359a2.2,2.2,0,0,0-.582.292,1.623,1.623,0,0,0-.451,2.306,2.585,2.585,0,0,0,1.115.757c.074.031.181.109.182.167.014.56.008,1.12.008,1.7a.932.932,0,0,1-.671-.775h-.944a1.962,1.962,0,0,0,1.6,1.794l.027.384h.915c-.118-.36.109-.43.365-.523a2.044,2.044,0,0,0,.6-.361,1.582,1.582,0,0,0-.206-2.656,2.152,2.152,0,0,0-.517-.26c-.229-.065-.272-.193-.259-.4.017-.277,0-.556,0-.834v-.665a.929.929,0,0,1,.659.773h.938a2.423,2.423,0,0,0-.034-.337,1.833,1.833,0,0,0-1.132-1.3c-.267-.111-.535-.164-.431-.547Z" transform="translate(-245.837 -109.177)"/>
                          <path id="Path_1307" data-name="Path 1307" d="M-627.383,120.738c-.377-.11-.611-.339-.629-.608a.842.842,0,0,1,.629-.809Z" transform="translate(-279.384 -139.882)"/>
                          <path id="Path_1308" data-name="Path 1308" d="M-610.626,150.66c.424.148.617.358.616.656a.825.825,0,0,1-.616.757Z" transform="translate(-295.183 -168.357)"/>
                        </g>
                      </svg>
                      <svg xmlns="http://www.w3.org/2000/svg" width="77" height="33" viewBox="0 0 77 33">
                        <text id="CASH" transform="translate(0 26)" fill="#1e1e1e" font-size="28" font-family="WorkSans-SemiBold, Work Sans" font-weight="600"><tspan x="0" y="0">CASH</tspan></text>
                      </svg>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="fixed-footer">
    @php
        $isHome = (Request::routeIs('home'))?true:false;
    @endphp
    <div class="{{ ($isHome)?'active':'' }} go-home">
        <div class="icon-home"></div>
        <div class="times">
            <div class="icon-times"></div>
            <span>Mbyll</span>
        </div>
        <a href="{{ route('home') }}">Kreu</a>
    </div>
    <div class="open-category">
        <div class="icon-th-list"></div>
        <span>Kategory</span>
    </div>
    <div class="open-search">
        <div class="icon-search"></div>
        <span>Kërko</span>
    </div>
    <div class="{{ (current_user())?'open-profile':'open-login' }}">
        <div class="icon-user-alt"></div>
        <span>Profili</span>
    </div>
    <div class="open-menu">
        <div class="fas fa-bars"></div>
        <span>Menu</span>
    </div>
</div>
<div class="bg_open"></div>