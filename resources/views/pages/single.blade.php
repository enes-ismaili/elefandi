<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', $page->name)
    <div class="container single-page">
        <h1 class="title">{{ $page->name }}</h1>
        <div class="content tmce">{!! $page->description !!}</div>
    </div>
</x-app-layout>