<x-app-layout>
    @push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        let registerLogin = document.querySelector('.go-login');
        if(registerLogin){
            registerLogin.addEventListener('click', e=>{
                window.livewire.emitTo('header.login-user', 'open-login');
                // document.getElementsByTagName('body')[0].scollTop(0);
                window.scrollTo(0,0);
            })
        }
    });
</script>
    @endpush
    @push('styles')
<style>
    .go-login {
        margin-top: 15px;
        text-align: center;
        font-size: 16px;
    }
    .register .social-login {
        margin: 20px 0 15px;
    }
    .register .social-login img {
        max-width: 320px;
        margin: 5px auto;
    }
</style>
    @endpush
    @section('pageTitle', 'Regjistrohu')
    <div class="container">
        <h1 class="tcenter">Regjistrohu</h1>
        <p class="page-information tcenter">Regjistrohu llogarinë tuaj në Elefandi</p>
        <form action="{{ route('register.store') }}" method="POST" class="card p25 mt-30 register">
            @csrf
            <div class="row">
                <div class="col-6 col-sm-12">
                    <div class="form-group">
                        <label for="first_name">Emri *</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Emri" value="{{old('first_name')}}">
                        @error('first_name') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-6 col-sm-12">
                    <div class="form-group">
                        <label for="last_name">Mbiemri *</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Mbiemri" value="{{old('last_name')}}">
                        @error('last_name') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-6 col-sm-12">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        @if(Request::get('stoken'))
                            <input type="hidden" name="stoken" value="{{Request::get('stoken')}}">
                            <input type="hidden" name="email" value="{{($emailReq) ? $emailReq : old('email')}}">
                        @endif
                        <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{ ($emailReq) ? $emailReq : old('email')}}" @if(Request::get('stoken')) disabled @endif>
                        @error('email') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-6 col-sm-12">
                    <div class="form-group">
                        <label for="phone">Numri i telefonit *</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Numri i telefonit" value="{{old('phone')}}">
                        @error('phone') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-6 col-sm-12">
                    <div class="form-group">
                        <label for="password">Fjalkalimi *</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Fjalkalimi">
                        @error('password') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-6 col-sm-12">
                    <div class="form-group">
                        <label for="password_confirm">Konfirmo Fjalkalimin *</label>
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Konfirmo Fjalkalimin">
                        @error('password_confirm') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
                @livewire('user.select-countries', ['selCountry'=> (old('country') ? old('country') : 0), 'selCity'=>(old('city') ? old('city') : '')])
                @error('country') <span class="text-danger error">{{ $message }}</span>@enderror
                @error('city') <span class="text-danger error">{{ $message }}</span>@enderror
                <div class="col-12">
                    <div class="form-group">
                        <label for="address">Adresa *</label>
                        <input type="text" name="address" id="address" class="form-control" placeholder="Adresa" value="{{old('address')}}">
                        @error('address') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn text-white btn-success fullwidth" wire:click.prevent="registerStore">Regjistrohuni</button>
                    <div class="col-12 social-login">
                        <a href="{{ route('social.login', 'google') }}">
                            <img src="{{ asset('images/google-login.jpg') }}" alt="google login">
                        </a>
                        <a href="{{ route('social.login', 'facebook') }}">
                            <img src="{{ asset('images/fblogin.jpg') }}" alt="facebook login">
                        </a>
                    </div>
                    <div class="go-login">Keni llogari në Elefandi? <a class="pointer"><strong>Hyr këtu!</strong></a></div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>