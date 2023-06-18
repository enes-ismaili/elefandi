<x-admin-layout>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.0/slimselect.min.js"></script>
        <script src="{{ mix('js/datatime.js') }}"></script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/datatime.css') }}">
<style>
    .text-small {
        font-size: 12px;
    }
</style>
    @endpush
    <form action="{{ route('admin.stories.limit.update') }}" method="POST" class="">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Ndrysho Limitin</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="limit">Limiti Mujor pët Story</label>
                            <input type="number" name="limit" class="form-control" id="limit" placeholder="Limiti Story" value="{{ $limit }}">
                            @error('limit') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="slimit">Limiti Mujor për Foto/Video për çdo Story</label>
                            <input type="number" name="slimit" class="form-control" id="slimit" placeholder="Limiti Foto/Video" value="{{ $slimit }}">
                            @error('slimit') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <p class="text-small">Këtu ju po ndryshoni limit mujor për story në pritje dhe të pranuara për cdo dyqan<br>
                            Për të ndryshuar limitin mujor vetëm të një dyqani shkoni tek faqja Dyqanet dhe ndryshoni dyqanin
                        </p>
                    </div>
                    <div class="card-footer pl-0">
                        <button type="submit" class="btn btn-success">Ruaj</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin-layout>