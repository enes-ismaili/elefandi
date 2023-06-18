<div>
    <div class="list-group" wire:sortable="updateTagOrder">
        @foreach ($selectedTags as $tag)
            <div class="list-group-item nested-1" wire:sortable.item="{{ $tag['id'] }}" wire:key="task-{{ $tag['id'] }}">
                <div class="list-group-title" >
                    <input type="hidden" name="tagid[]" value="{{ $tag['id'] }}" }}>
                    <input type="hidden" name="order[]" value="{{ $loop->index + 1 }}">
                    <i class="fas fa-arrows-alt handle" wire:sortable.handle></i><span>{{ $tag['name'] }}</span>
                    <div class="categories-right">
                        <div class="delete-tags" wire:click="removeTag('{{ $tag['id'] }}')"><i class="fas fa-trash-alt"></i></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="form-group mt-4">
        <label for="selectAction">Shto Tag</label>
        <input type="text" wire:model="tagS" class="form-control" placeholder="Kërko Tags ...">
        @if($tags)
        <div class="search-results">
            <ul>
                @foreach($tags as $tag)
                    <li wire:click="addTag('{{$tag->id}}')">
                        <span>{{ $tag->name }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
