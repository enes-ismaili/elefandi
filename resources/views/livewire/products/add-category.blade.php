<div>
    <form>
        <div class="form-group">
            <label for="categoryname">Emri</label>
            <input type="text" wire:model="name" class="form-control" id="categoryname" placeholder="Emri">
            @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="categorydesc">Përshkrimi</label>
            <textarea class="form-control" wire:model="description" id="categorydesc" rows="3" placeholder="Përshkrimi"></textarea>
            @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="parentcategory">Përshkrimi</label>
            <select class="form-control" wire:model="parent" id="parentcategory">
                @foreach($categories as $parentCategory)
                    <option value="{{ $parentCategory->id }}" style="color: #000;font-weight: 700;">{{ $parentCategory->name }}</option>
                        @foreach($parentCategory->children as $category)
                            <option value="{{ $category->id }}">&nbsp;&nbsp;{{ $category->name }}</option>
                        @endforeach
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="parentcategory">Ngarko Foto</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="customFile" lang="sq">
                <label class="custom-file-label" for="customFile">Zgjidh Foto</label>
            </div>
        </div>
        @livewire('icon-picker')
    </form>
</div>
