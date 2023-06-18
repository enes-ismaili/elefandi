<x-admin-layout>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.0/slimselect.min.js"></script>
        <script src="{{ mix('js/datatime.js') }}"></script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/datatime.css') }}">
        <style>
.cth-switch input:empty~span {
    width: 140px;
    height: 26px;
}
.cth-switch input:empty~span:before {
    line-height: 26px;
    padding-left: 8px;
    width: 140px;
}
.cth-switch input:empty~span:after {
    content: "Tani";
    width: 70px;
    height: 22px;
    line-height: 26px;
    color: #000;
    font-weight: 500;
    text-transform: uppercase;
}
.cth-switch input:checked~span:after {
    margin-left: 68px;
    content: "Me Vone";
    color: #fff;
}
.cth-switch + label {
    display: inline-block;
    margin: 0 10px;
    position: relative;
    top: -8px;
}
        </style>
    @endpush
    <form action="{{route('admin.notifications.store')}}" method="POST" class="">
        @csrf
        <div class="row"  x-data="{ notificationtype: '1', sendTime: {{ old('send_later') ? 1 : 0 }} }">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Shto Njoftim</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Titulli *</label>
                            <input type="text" name="title" class="form-control" id="title" placeholder="Titulli" value="{{ old('title') }}">
                            @error('title') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="message">Mesazhi</label>
                            <textarea class="form-control" name="message" id="message" rows="3" placeholder="Mesazhi">{{ old('message') }}</textarea>
                            @error('message') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="selectAction">Zgjidh llojin e njoftimit *</label>
                            <select id="selectAction" name="ntype" value="{{ old('ntype') }}" x-on:change="notificationtype = $event.target.value">
                                <option value="1">Njoftim për Dyqan</option>
                                <option value="2">Njoftim për Produkt</option>
                                <option value="3">Njoftim për Kupon</option>
                                <option value="4">Njoftim për Ofertat Speciale</option>
                            </select>
                            @error('ntype') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group" x-show="notificationtype == 3">
                            <label for="coupon">Kuponi *</label>
                            <input type="text" name="coupon" class="form-control" id="coupon" placeholder="Kuponi" value="{{ old('coupon') }}">
                            @error('coupon') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group" x-show="['1','3'].includes(notificationtype)">
                            <label for="vlink">Linku i Dyqanit *</label>
                            <input type="text" name="vlink" class="form-control" id="vlink" placeholder="Linku i Dyqanit" value="{{ old('vlink') }}">
                            @error('vlink') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group" x-show="notificationtype == 2">
                            <label for="plink">Linku i Produktit *</label>
                            <input type="text" name="plink" class="form-control" id="plink" placeholder="Linku i Produktit" value="{{ old('plink') }}">
                            @error('plink') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group" x-show="notificationtype == 4">
                            <label for="olink">Linku i Ofertës * (E merr ne faqen kryesore)</label>
                            <input type="text" name="olink" class="form-control" id="olink" placeholder="Linku i Ofertës" value="{{ old('olink') }}">
                            @error('olink') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Detajet</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="plink">Dërgo</label>
                            <input type="hidden" name="send_later" value="0">
                            <div>
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" id="change_send" name="send_later" {{ (old('send_later')) ? 'checked' : '' }} x-on:change="sendTime = !sendTime">
                                    <span></span>
                                </label>
                                <label for="change_send">Dergo Tani ose Skedulo për me vonë</label>
                            </div>
                        </div>
                        <div class="form-group" x-show="sendTime == 1">
                            <label for="">Data e Dërgimit</label>
                            <input class="flatpickr datetime tomorrow" type=text placeholder="Data e Përfundimit" name="send_at" value="{{ Carbon\Carbon::now() }}">
                            @error('send_at') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                            
                    </div>
                    <div class="card-footer pl-0">
                        <button type="submit" class="btn btn-primary ">Ruaj</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin-layout>