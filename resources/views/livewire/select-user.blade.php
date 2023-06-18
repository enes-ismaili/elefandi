<div>
    <style>
        .remove-user {
            position: absolute;
            top: 50%;
            right: 5px;
            transform: translate(0, -50%);
            padding: 2px 10px;
            cursor: pointer;
        }
    </style>
    @if($selectedUser)
    <label>Përdoruesi</label>
    <div class="form-group position-relative">
        <input type="hidden" class="form-control" name="user_id" value="{{ $selectedUserId }}">
        <input type="text" class="form-control" name="user" value="{{ $selectedUser->first_name .' '. $selectedUser->last_name .' ('. $selectedUser->email .')' }}" disabled>
        @if($edit)
            <span class="remove-user" wire:click="removeUser"><i class="fa fa-times"></i></span>
        @endif
    </div>
    @else
    <div class="form-group">
        <label for="selectAction">Shto Përdorues *</label>
        <input type="text" wire:model="userS" class="form-control" placeholder="Kërko Përdorues ...">
        @if($users)
        <div class="search-results">
            <ul>
                @foreach($users as $user)
                    <li wire:click="addUser('{{$user->id}}')">
                        <span>{{ $user->first_name .' '. $user->last_name .' ('. $user->email .')' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @endif
</div>
