<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
<style>
.login .card {
    width:550px;
    margin: 20px auto 0;
}
.login .card .form-group {
    position: relative;
}
.login .forget-password {
    position: absolute;
    top: 50%;
    -webkit-transform: translateY(-50%);
    -moz-transform: translateY(-50%);
    -ms-transform: translateY(-50%);
    -o-transform: translateY(-50%);
    transform: translateY(-50%);
    right: 10px;
    color: #06c;
}
.login .login-socials {
    margin: 0 0 15px;
}
.login .login-socials img {
    max-width: 320px;
    margin: 5px auto;
}
@media (max-width: 600px){
	.login .card {
		width: 100%;
	}
	.login .card .card-body {
		padding: 10px 7px;
	}
	.login .card .row .form-control {
		height: 48px;
		padding: 0 10px;
	}
    .login .forget-password {
        font-size: 12px;
        right: 5px;
    }
	.login .card .row .btn {
		font-size: 15px;
		padding: 10px 10px;
	}
}
</style>
    @endpush
    <div class="container login">
        <h1 class="tcenter">Hyr në llogarinë tuaj</h1>
        <form method="POST" action="{{ route('login') }}" class="card">
            <div class="card-body">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            @if ($errors->any())
                                <div class="tcenter">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li class="text-danger error">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Fjalkalimi" required autocomplete="current-password">
                            <a href="{{ route('password.request') }}" class="forget-password">Keni harruar fjalkalimin?</a>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="remember_me">
                                <input type="checkbox" id="remember_me" name="remember">
                                <span >Remember me</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <button type="submit" class="btn fullwidth">Hyr</button>
                        </div>
                    </div>
                    <div class="col-12 tcenter login-socials">
                        <a href="{{ route('social.login', 'google') }}">
                            <img src="{{ asset('images/google-login.jpg') }}" alt="google login">
                        </a>
                        <a href="{{ route('social.login', 'facebook') }}">
                            <img src="{{ asset('images/fblogin.jpg') }}" alt="facebook login">
                        </a>
                    </div>
                    <div class="col-12 tcenter">
                        <span class="">Nuk keni llogari? <a href="#">Regjistrohuni këtu!</a></span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>