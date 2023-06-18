<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('js/datatime.js') }}"></script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/datatime.css') }}">
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Ndrysho Membership</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.vendors.requests')}}">Membership</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2" x-data="{ selType :  {{ $membership->type }}}">
            <form action="{{ route('admin.vendors.membership.update', [$vid, $mid]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="description">Përshkrimi</label>
                            <textarea name="description" id="description" class="form-control">{{ $membership->description }}</textarea>
                            @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">Data e fillimit</label>
                            <input class="flatpickr date" type=text placeholder="Data e Fillimit" name="start_date" value="{{ ($membership->start_date) ? $membership->start_date : Carbon\Carbon::now() }}">
                            @error('start_date') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group" x-show="selType == 1">
                            <label for="">Data e Përfundimit</label>
                            <input class="flatpickr date tomorrow" type=text placeholder="Data e Përfundimit" name="end_date" value="{{ ($membership->end_date) ? $membership->end_date : Carbon\Carbon::tomorrow() }}">
                            @error('end_date') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="type">Lloji i Membership</label>
                            <select name="type" id="type" class="form-control" x-on:change="selType = $event.target.value">
                                <option value="1" @if($membership->type == '1') selected @endif>Fiks</option>
                                <option value="2" @if($membership->type == '2') selected @endif>Përqindje</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="amount">Pagesa</label>
                            <div class="input-group mb-3">
                                <input type="number" name="amount" step="0.01" class="form-control" id="amount" value="{{ ($membership->amount) ? ($membership->amount * 1) : 0 }}">
                                <div class="input-group-append">
                                    <span class="input-group-text" x-text="((selType == 1) ? '€ / m' : '% / m')">€ / m</span>
                                </div>
                            </div>
                            @error('amount') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="active">Statusi i Membership</label>
                            <select name="active" id="active" class="form-control">
                                <option value="1" @if($membership->active == '1') selected @endif>Aktiv</option>
                                <option value="0" @if($membership->active == '0') selected @endif>Jo Aktiv</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12" x-show="selType == 1">
                        <div class="form-group">
                            <label for="paid">Është Paguar</label>
                            <select name="paid" id="paid" class="form-control">
                                <option value="1" @if($membership->paid == '1') selected @endif>Paguar</option>
                                <option value="0" @if($membership->paid == '0') selected @endif>Sështë Paguar</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 pl-0">
                    <button type="submit" class="btn btn-primary ">Ruaj</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>