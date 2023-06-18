<x-admin-layout>
    @push('scripts')
       
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Dyqanet</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.vendors.index')}}">Dyqanet</a>
            </li>
        </ul>
    </x-slot>
    <form action="{{ route('admin.vendor.namechange.store', ['id' => $vendor->id]) }}" method="POST" class="card">
        <div class="card-header"><h5>Kërkesë për ndryshim emri për Dyqanin {{ $vendor->vendor->name }}</h5></div>
        <div class="card-body">
            @if($vendor->status == 0)
                @csrf
            @endif
            <div class="form-group">
                <label>Emri i Ri</label>
                <input type="text" value="{{ $vendor->name }}" disabled class="form-control">
            </div>
            <div class="form-group">
                <label>Arsyeja e Ndryshimit të Emrit</label>
                <textarea class="form-control" disabled>{{ $vendor->description }}</textarea>
            </div>
            <div class="form-group"  x-data="{ openR: false, selradio: 1 }">
                @if($vendor->status == 0)
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="acceptRequest" id="acceptRequest1" value="1" x-on:click="selradio = 1" checked>
                            <label class="form-check-label" for="acceptRequest1">Aprovo</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="acceptRequest" id="acceptRequest2" value="2" x-on:click="selradio = 2">
                            <label class="form-check-label" for="acceptRequest2">Anullo</label>
                        </div>
                    </div>
                    <div class="form-group" x-show="selradio === 2">
                        <label>Arsyeja e Refuzimit</label>
                        <textarea class="form-control" name="reason"></textarea>
                        @error('reason') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                @elseif($vendor->status == 2)
                    <div class="form-group" >
                        <label>Arsyeja e Refuzimit</label>
                        <textarea class="form-control" disabled>{{ $vendor->reason }}</textarea>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-footer pl-0">
            @if($vendor->status == 0)
                <button type="submit" class="btn btn-primary ">Ruaj</button>
            @elseif($vendor->status ==1)
                <p>Kërkesa është Aprovuar</p>
            @else
                <p>Kërkesa është Refuzuar</p>
            @endif
        </div>
    </form>
</x-admin-layout>