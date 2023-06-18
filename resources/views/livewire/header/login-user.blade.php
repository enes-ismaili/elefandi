<div>
    <a href="#" wire:click.prevent="openModal(false)" class="loginaction {{($showForm) ? 'active' : ''}}">Hyr</a>
    <a href="{{ route('view.register') }}" class="loginaction">Regjistrohu</a>
    <div class="login-modal {{($showForm) ? 'active' : ''}}">
        <div class="background-login" wire:click.prevent="closeModal()"></div>
        @if($showForm)
            <form>
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="text-danger error">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" wire:model.defer="cartLocal" id="cartLocalS" class="form-control" value="">
                        <div class="form-group">
                            <label>Email :</label>
                            <input type="text" wire:model.defer="email" class="form-control small" placeholder="Email">
                            @error('email') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Fjalkalimi :</label>
                            <input type="password" wire:model.defer="password" class="form-control small" placeholder="Fjalkalimi">
                            @error('password') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn text-white btn-success fullwidth small mt-10 c1" wire:click.prevent="login">Hyr</button>
                    </div>
                    <div class="change-login"><a href="{{ route('password.request') }}" class="pointer">Keni harruar fjalkalimin?!</a></div>
                    <div class="col-12 social-login"">
                        <a href="{{ route('social.login', 'google') }}">
                            <img src="{{ asset('images/google-login.jpg') }}" alt="google login">
                        </a>
                        <a href="{{ route('social.login', 'facebook') }}">
                            <img src="{{ asset('images/fblogin.jpg') }}" alt="facebook login">
                        </a>
                    </div>
                    <div class="change-login">Nuk keni llogari? <a href="{{ route('view.register') }}" class="pointer"><strong>Regjistrohuni këtu!</strong></a></div>
                </div>
            </form>
        @endif
    </div>
</div>
