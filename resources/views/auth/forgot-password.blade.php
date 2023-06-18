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
		font-size: 13px;
		padding: 10px 8px;
	}
}
</style>
    @endpush
    <div class="container login">
        <h1 class="tcenter">Kërko ndryshimin e fjalkalimit</h1>
        <h3 class="tcenter">Ju lutem shkruani adresën tuaj të email-it. Pas kërkesës do t'ju vijë një email me instruksionet për të ndryshuar fjalkalimin tuaj.</h3>
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif
        <form method="POST" action="{{ route('password.email') }}" class="card">
            @csrf
            <div class="card-body">
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
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div class="col-12">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <button type="submit" class="btn fullwidth">Dërgo Linkun për të krijuar një fjalkalim të ri</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
