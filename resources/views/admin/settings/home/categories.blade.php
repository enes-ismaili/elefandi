<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Kategoritë Slider</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Kategoritë Slider</span>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-header">
            <h5>Kategoritë Kreu</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Emri</th>
                            <th>Shfaq</th>
                            <th>Slider</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ ($category->trending) ? 'Po' : 'Jo' }}</td>
                                <td>{{ $category->trendingtag->count() }}</td>
                                <td class="action-icons">
                                    <a href="{{ route('admin.homesettings.categories.edit', [$category->id]) }}" class="action-icon" title="Ndrysho"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>