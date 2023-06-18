<div>
    <div class="list-group" wire:sortable="updateChildOrder">
        @foreach ($selectedCategory as $category)
            <div class="list-group-item nested-1" wire:sortable.item="{{ $category['id'] }}" wire:key="task-{{ $category['id'] }}">
                <div class="list-group-title" >
                    <input type="hidden" name="catid[]" value="{{ $category['id'] }}" }}>
                    <input type="hidden" name="order[]" value="{{ $loop->index + 1 }}">
                    <i class="fas fa-arrows-alt handle" wire:sortable.handle></i><span>{{ $category['name'] }}</span>
                    <div class="categories-right">
                        <div class="delete-tags" wire:click="removeCat('{{ $category['id'] }}')"><i class="fas fa-trash-alt"></i></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="form-group mt-4">
        <label for="selectAction">Shto Kategori</label>
        <input type="text" wire:model="catS" class="form-control" placeholder="Kërko Kategori ...">
        @if($categories)
        <div class="search-results">
            <ul>
                @foreach($categories as $tag)
                    <li wire:click="addCategory('{{$tag->id}}')">
                        <span>{{ $tag->name }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
