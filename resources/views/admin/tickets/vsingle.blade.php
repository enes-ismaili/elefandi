<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
<style>
    .header-button {
        display: flex;
        justify-content: end;
        margin-bottom: 15px;
    }
    .header-button button {
        margin-left: 15px;
    }
.additionalFiles {
    display: flex;
    margin-top: 15px;
    flex-wrap: wrap;
    margin-left: -10px;
}
.additionalFiles .single-file {
    flex: 0 0 auto;
    width: calc(25% - 10px);
    padding: 0 10px;
    background-color: #f1f1f1;
    box-shadow: 3px 3px 6px rgb(0 0 0 / 8%);
    margin-left: 10px;
    margin-bottom: 10px;
    height: 70px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.additionalFiles .single-file img {
    height: 100%;
}
.additionalFiles .single-file > a {
    padding: 5px;
    height: 100%;
    display: flex;
    align-items: center;
    word-break: break-all;
}
.additionalFiles .single-file .remove-file {
    padding: 5px;
    font-size: 17px;
}
</style>
    @endpush
    <x-slot name="breadcrumb">
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('vendor.orders.index')}}">Porositë</a>
            </li>
        </ul>
    </x-slot>
    @php
        $ticketStatus = 'Në Pritje';
        if($ticket->status == 1){
            $ticketStatus = 'Përgjigjur';
        } else if($ticket->status == 2){
            $ticketStatus = 'Anulluar';
        } else if($ticket->status == 3){
            $ticketStatus = 'Mbyllur';
        } else if($ticket->status == 4){
            $ticketStatus = 'Rishikim nga Elefandi';
        } else if($ticket->status == 6){
            $ticketStatus = 'Mbyllur Përfundimisht';
        } else if($ticket->status == 7){
            $ticketStatus = 'Rikthim Pagese';
        }
    @endphp
    @if($ticket->status < 6)
    <div class="header-button">
        <form action="">
            @csrf
            <button type="submit" class="btn btn-primary">Rikthe Pagesën</button>
        </form>
    </div>
    @endif
    <div class="card">
        @php
            if($ticket->type == 1){
                $ticketType = 'Porosia nuk ka mbërritur';
            } else if($ticket->type == 2){
                $ticketType = 'Probleme me Produktin';
            } else if($ticket->type == 3){
                $ticketType = 'Kërkesë për Rimbursim';
            } else {
                $ticketType = $ticket->subject;
            }
        @endphp
        <div class="card-header ticket-vendor"><h5>"{{ $ticketType }}" për dyqanin <b>{{ $ticket->vendor->name }}</b></h5><span>Krijuar më: {{ \Carbon\Carbon::parse($ticket->created_at)->format('d.m.Y H:i') }}</span></div>
        <div class="card-body">
            {{ $ticket->message }}
        </div>
        <div class="card-footer">
            <span>Statusi i kësaj çështje është <i>{{ $ticketStatus }}</i></span>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @if(count($ticket->messages))
                @foreach($ticket->messages as $message)
                    <div class="single-message w{{$message->way}}">
                        <div class="message-title">
                            @if($message->way == 2)
                                <span>{{ ($message->user_id) ? $message->vendor->name : 'Stafi Elefandi' }}</span>
                            @else
                                <span>{{ $message->user->first_name.' '.$message->user->last_name }}</span>
                            @endif
                            <span>{{ \Carbon\Carbon::parse($message->created_at)->format('d-m-Y H:i') }}</span>
                        </div>
                        <div class="divider"></div>
                        <div class="message-text">{!! $message->message !!}</div>
                        @if(count($message->attachment))
                            <div class="message-attachments additionalFiles">
                                @foreach($message->attachment as $attachment)
                                    @php
                                        $fileUrl = asset('/photos/ticket/'.$attachment->file);
                                        $ext = pathinfo($fileUrl, PATHINFO_EXTENSION);
                                        $lExt = strtolower($ext);
                                    @endphp
                                    <div class="single-file">
                                        @if(in_array($lExt, array('png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp')))
                                            <a href="{{ $fileUrl }}" target="_blank"><img src="{{ $fileUrl }}" alt=""></a>
                                            <a href="{{ $fileUrl }}" download><i class="fas fa-download"></i></a>
                                        @else
                                            <a href="{{ $fileUrl }}" target="_blank"><span>{{ $attachment->file }}</span></a>
                                            <a href="{{ $fileUrl }}" download><i class="fas fa-download"></i></a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="no-messages">Për momentin nuk ka asnjë përgjigje. Ju do të njoftoheni kur të ketë përditësim të kësaj çështje</div>
            @endif
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            @if(count($ticket->messages) && $ticket->messages->last()->way == 1)
                <h5>Kthe përgjigje për këtë Çështje</h5>
            @else
                <h5>Shto më shumë informacion për këtë Çështje</h5>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('vendor.ticket.store', $ticket->id) }}" method="post">
                @csrf
                <div class="row">
                    @if($ticket->status < 3)
                        <div class="col-12">
                            <div class="form-group">
                                <input type="hidden" name="close" value="0">
                                <label class="cth-switch cth-switch-success">
                                    <input value="1" type="checkbox" name="close" id="close">
                                    <span></span>
                                </label>
                                <span>Mbyll Çështjen</span>
                            </div>
                        </div>
                    @endif
                    <div class="col-12">
                        <div class="form-group">
                            <label for="message">Mesazhi</label>
                            <textarea name="message" id="message" class="form-control" placeholder="Mesazhi" required>{{ old('message') }}</textarea>
                            @error('message') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="image">Ngarko Foto/Dokument (Opsionale)</label>
                            @livewire('upload-file', [
                                'inputName' => 'attachment', 'upload' => 'multiple', 'exis' => '', 'path'=> 'ticket/', 'buttonName' => 'Ngarko Foto/Dokument', 
                                'style'=>1, 'maxWidth'=>1000, 'maxHeight'=>1000, 'maxSize'=>6144
                            ])
                            @error('image') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary small">Dërgo</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>