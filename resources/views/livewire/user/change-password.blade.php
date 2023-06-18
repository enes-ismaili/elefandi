<div>
    <div class="btn fullwidth small" wire:click.prevent="openModal(true)">Ndrysho Fjalkalimin</div>
    @if($showForm)
    <div class="modal show">
        <div class="modal_bg" wire:click.prevent="closeModal()"></div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Ndrysho Fjalkalimin</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click.prevent="closeModal()"><span aria-hidden="true">×</span></button>
                </div>
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif
                <form>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Fjalkalimi Aktual</label>
                                    <input type="password" wire:model="currentPassword" class="form-control">
                                    @error('currentPassword') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Fjalkalimi i ri</label>
                                    <input type="password" wire:model="newPassword" class="form-control">
                                    @error('newPassword') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Konfirmo fjalkalimin e ri</label>
                                    <input type="password" wire:model="confirmNewPassword" class="form-control">
                                    @error('confirmNewPassword') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-12">
                                <button class="btn small" wire:click.prevent="changePass">Ndrysho</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
