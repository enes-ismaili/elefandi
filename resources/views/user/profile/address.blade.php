<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/user.css') }}">
    @endpush
    @section('pageTitle', 'Menaxho Adresat')
    <div class="container">
        <div class="site-main profile">
            <aside class="profile-sidebar">
                @include('user.sidebar')
            </aside>
            <main class="main-content sh b1 p3">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            @livewire('user.change-address', ['type'=>'add'])
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h1 class="profile-title">Menaxho Adresat</h1>
                    </div>
                </div>
                <div class="row mr-0">
                    @forelse(current_user()->addresses->where('udelete', '=', 0) as $address)
                        <div class="address_single {{$address->id }}">
                            @if($address->primary)<div class="primary-address"><i class="icon-star"></i></div>@endif
                            <p><i class="fa fa-user"></i>{{$address->name}}</p>
							
                            <p><i class="fa fa-map-marker"></i>{{$address->address}}, {{$address->address2}}, {{((is_numeric($address->city) && $address->country_id < 4)?$address->cityF->name : $address->city)}}, {{$address->country->name}}</p>
                            <p><i class="fa fa-mobile"></i>{{$address->phone}}</p>
                            @livewire('user.change-address', ['type'=>'edit', 'id'=>$address->id])
                        </div>
                    @empty
                        <div class="col-12 tcenter">Nuk ka adresa të regjistruara</div>
                    @endforelse
                </div>
            </main>
        </div>
    </div>
</x-app-layout>