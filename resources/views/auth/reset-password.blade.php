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
</style>
    @endpush
    <div class="container login">
        <h1 class="tcenter">Ndrysho Fjalkalimin</h1>
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            @if ($errors->any())
                                <div class="tcenter">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        @php
                                            if($error == 'passwords.token'){
                                                $errMessage = 'Vlefshmëria e kësaj kërkese ka përfunduar. Ju lutem provoni sërisht';
                                            } else if( $error == 'validation.required') {
                                                $errMessage = 'Ju lutem plotësoni të gjitha fushat.';
                                            } else {
                                                $errMessage = $error;
                                            }
                                        @endphp
                                            <li class="text-danger error">{{ $errMessage }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div class="col-12">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email', $request->email) }}" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" id="password" required="required" autocomplete="new-password" placeholder="Fjalëkalimi">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password_confirmation" id="password_confirmation" required="required" autocomplete="new-password" placeholder="Konfirmo Fjalëkalimi">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <button type="submit" class="btn fullwidth">Ndrysho Fjalkalimin</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
