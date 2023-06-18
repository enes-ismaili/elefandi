<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('js/datatime.js') }}"></script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/datatime.css') }}">
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Ndrysho Faturë</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.vendors.requests')}}">Faturat</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            <form action="{{ route('admin.vendors.membership.invoice.update', $invoice->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="vendor">Dyqani</label>
                            <input type="text" name="vendor" id="vendor" class="form-control" disabled value="{{ $invoice->vendor->name }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="total">Totali i Shitjeve</label>
                            <input type="text" name="total" id="total" class="form-control" disabled value="{{ $invoice->total * 1 }}€">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="comission">Komisioni</label>
                            <input type="text" name="comission" id="comission" class="form-control" disabled value="{{ $invoice->comission * 1 }}%">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="amount">Shuma për tu paguar</label>
                            <input type="text" name="amount" id="amount" class="form-control" disabled value="{{ $invoice->amount * 1 }}€">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="paid">Është Paguar</label>
                            <select name="paid" id="paid" class="form-control">
                                <option value="1" @if($invoice->paid == '1') selected @endif>Paguar</option>
                                <option value="0" @if($invoice->paid == '0') selected @endif>Sështë Paguar</option>
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