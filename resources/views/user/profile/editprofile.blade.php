<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/user.css') }}">
    @endpush
    @section('pageTitle', 'Ndrysho të dhënat tuaja')
    <div class="container">
        <div class="site-main profile">
            <aside class="profile-sidebar">
                @include('user.sidebar')
            </aside>
            <main class="main-content sh b1 p3">
                <h1 class="profile-title">Ndrysho të dhënat tuaja</h1>
                <form action="{{ route('profile.store') }}" method="post" class="profile-edit">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="first_name">Emri *</label>
                                <input class="form-control" type="text" name="first_name" id="first_name" value="{{ current_user()->first_name }}" placeholder="Emri *" required="">
                                @error('first_name') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="last_name">Mbiemri *</label>
                                <input class="form-control" type="text" name="last_name" id="last_name" value="{{ current_user()->last_name }}" placeholder="Mbiemri *" required="">
                                @error('last_name') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="last_name">Email-i *</label>
                                <input class="form-control" type="email" name="email" id="email" value="{{ current_user()->email }}" placeholder="Email-i *" required="">
                                @error('email') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="phone">Numri i telefonit *</label>
                                <input class="form-control" type="text" name="phone" id="phone" value="{{ current_user()->phone }}" placeholder="Numri i telefonit *" required="">
                                @error('phone') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        @livewire('user.select-countries', ['selCountry'=>current_user()->country_id, 'selCity'=>current_user()->city])
                        <div class="col-12">
                            <div class="form-group">
                                <label for="address">Adresa *</label>
                                <input class="form-control" type="text" name="address" id="address" value="{{ current_user()->address }}" placeholder="Adresa *" required="">
                                @error('address') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group submtit">
                                <button class="btn fullwidth">Përditëso profilin tuaj</button>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                @livewire('user.change-password')
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</x-app-layout>