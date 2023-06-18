<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Shitjet e Produktit</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.show')}}">Produktet</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            <form action="{{ route('admin.products.supdate', $product->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="productname">Emri i Produktit</label>
                    <input type="text" class="form-control" id="productname" placeholder="Emri" value="{{ $product->name }}" disabled>
                </div>
                <div class="form-group">
                    <label for="sales">Shitjet Reale</label>
                    <input type="number" class="form-control" name="sales" id="sales" placeholder="Shitjet Reale" value="{{ $product->sales }}" disabled>
                </div>
                <div class="mt-4">
                    <label for="salesAction">Veprimi</label>
                    <select name="salesAction" id="salesAction">
                        <option value="1" @if($product->psales && $product->psales->saction == 1) selected @endif>+ Shtim</option>
                        <option value="2" @if($product->psales && $product->psales->saction == 2) selected @endif>* Shumzim</option>
                    </select>
                    @error('storyStatus') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="fsales">Shitjet Fallco</label>
                    <input type="number" class="form-control" name="fsales" id="fsales" placeholder="Shitjet Fallco" value="{{ ($product->psales && $product->psales->fsales)? $product->psales->fsales : '' }}" min="0" step=".01">
                    @error('fsales') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="col-12 pl-0">
                    <button type="submit" class="btn btn-primary ">Ruaj</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>