<div class="form-group">
    <label for="selectMultiMandatory">Kategoritë
        <p class="mb-0">
            <small>Zgjidh Kategoritë që i përshaten këtij produkti</small>
        </p>
    </label>
    <select name="parentCategory" wire:model="selectedParent">
        <option value="0" >Zgjidh Kategorinë Kryesore *</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
    <select name="subCategory" wire:model="selectedSub">
        <option value="0" >Zgjidh Nën Kategorinë *</option>
        @if($selectedParent)
            @if($subCategories)
            @foreach ($subCategories as $category)
                <option value="{{ $category->id }}" >{{ $category->name }}</option>
            @endforeach
            @endif
        @endif
    </select>
    <select name="subsubCategory" wire:model="selectedSubSub">
        <option value="0" >Zgjidh Nën Kategorinë</option>
        @if($selectedParent && $subCategories)
            @if($subsubCategories)
            @foreach ($subsubCategories as $category)
                <option value="{{ $category->id }}" >{{ $category->name }}</option>
            @endforeach
            @endif
        @endif
    </select>
</div>