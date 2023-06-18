<div class="report-article">
    <div class="report-article-text cpointer" wire:click="openReport()">Raportoni Artikullin</div>
    <div class="modal {{ ($openReport)?'show':'' }}">
        <div class="modal_bg" wire:click.prevent="closeReport()"></div>
        <div class="modal-dialog">
            <form class="modal-content" wire:submit.prevent="SendReport">
                <div class="modal-header">
                    <div class="modal-title">
                        <h3>Raporto Artikullin</h3>
                        <h5>Në rast se keni vërejtur parregullësi në lidhje me këtë artikull, ju lutem na spjegoni më shumë tek fusha më poshtë.</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click.prevent="closeReport()"><span aria-hidden="true">×</span></button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @foreach($errorss as $error)
                                <div class="text-danger error">{{ $error }}</div>
                            @endforeach
                            @if($successReport)
                                <div style="color: #2d8727">{{ $successReport }}</div>
                            @endif
                            @if(!$this->isLoggin)
                                <div class="form-group">
                                    <label for="name">Emri</label>
                                    <input type="text" id="name" class="form-control" placeholder="Emri i Plotë" wire:model="name">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email-i</label>
                                    <input type="email" id="email" class="form-control" placeholder="Email-i" wire:model="email">
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="report_reason">Informacione</label>
                                <textarea name="reason" id="report_reason" class="form-control" placeholder="Shkruani arsyen dhe informacione shtese për këtë raportim" wire:model="reason"></textarea>
                            </div>
                            <button class="btn text-white btn-success fullwidth small mt-10 c1" wire:click.prevent="SendRequest()">Raporto</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>