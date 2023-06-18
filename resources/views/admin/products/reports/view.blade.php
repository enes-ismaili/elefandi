<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Raportim</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.reports.index')}}">Raportimet e Produkteve</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            <div class="form-group">
                <label for="name">Emri</label>
                <input type="text" name="name" class="form-control" id="name" value="{{ $report->name }}" disabled>
            </div>
            <div class="form-group">
                <label for="email">Email-i</label>
                <input type="text" name="name" class="form-control" id="email" value="{{ $report->email }}" disabled>
            </div>
            <div class="form-group">
                <label for="email">Person i Regjistruar</label>
                <input type="text" name="name" class="form-control" id="email" value="{{ ($report->user_id && $report->user) ? 'Po' : 'Jo' }}" disabled>
            </div>
            <div class="form-group">
                <label for="product">Produkti</label>
                <input type="text" name="name" class="form-control" id="product" value="{{ $report->product->name }}" disabled>
            </div>
            <div class="form-group">
                <label for="reason">Arsyeja dhe Informacioni</label>
                <textarea class="form-control" id="reason" disabled>{{ $report->reason }}</textarea>
            </div>
            <div class="form-group">
                <label for="product">Data e Dërgimit</label>
                <input type="text" name="name" class="form-control" id="product" value="{{ \Carbon\Carbon::parse($report->created_at)->format('d-m-Y H:i') }}" disabled>
            </div>
        </div>
    </div>
</x-admin-layout>