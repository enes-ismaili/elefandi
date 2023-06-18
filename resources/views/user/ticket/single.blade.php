<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/user.css') }}">
    @endpush
    @section('pageTitle', 'Shiko çështjen')
    <div class="container">
        <div class="site-main profile single-order">
            <aside class="profile-sidebar">
                @include('user.sidebar')
            </aside>
            <main class="main-content">
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
                    <div class="card-header"><h3>"{{ $ticketType }}" për dyqanin <b>{!! strtoupper($ticket->vendor->name) !!}</b></h3><span>Krijuar më: {{ \Carbon\Carbon::parse($ticket->created_at)->format('d.m.Y H:i') }}</span></div>
                    <div class="card-body">
                        {{ $ticket->message }}
                        @if(count($ticket->attachment))
                        <div class="message-attachments additionalFiles ticket-attachments">
                            @foreach($ticket->attachment as $attachment)
                                @php
                                    $fileUrl = asset('photos/ticket/'.$attachment->file);
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
                                            <span>{!! ($message->user_id) ? strtoupper($message->vendor->name) : 'Stafi Elefandi' !!}</span>
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
                                                $fileUrl = asset('photos/ticket/'.$attachment->file);
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
                @if($ticket->status < 4)
                <div class="card">
                    <div class="card-header">
                        @if($ticket->status != 3)
                            @if(count($ticket->messages) && $ticket->messages->last()->way == 2)
                                <h5>Kthe përgjigje për këtë Çështje</h5>
                            @else
                                <h5>Shto më shumë informacion për këtë Çështje</h5>
                            @endif
                        @else
                            <h5>Çështja është mbyllur nga Dyqani</h5>
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.ticket.store', $ticket->id) }}" method="post">
                            @csrf
                            <div class="row">
                                @if($ticket->status == 3)
                                    <div class="col-12">
                                        <p>Ju mund ta ankimoni çështjen tek Elefandi nëse mendoni se çështja juaj nuk është zgjidhur nga dyqani. 
                                            Personel i dedikuar do të merren me çështjen tuaj</p>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <div class="form-group">
                                        @if($ticket->status < 3)
                                            <label for="message">Mesazhi</label>
                                        @elseif($ticket->status == 3)
                                            <label for="message">Mesazhi për Elefandin</label>
                                        @endif
                                        <textarea name="message" id="message" class="form-control" placeholder="Mesazhi">{{ old('message') }}</textarea>
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
                                    <p class="small">* Duke shtypur dërgo ju i jepni të drejtë stafit të Elefandit të shikojë të gjitha informacionet që janë ne këtë çështje</p>
                                    @if($ticket->status < 3)
                                        <button type="submit" class="btn small">Dërgo</button>
                                    @elseif($ticket->status == 3)
                                        <button type="submit" class="btn small">Dërgo për Rishikim</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </main>
            <script>
                
            </script>
        </div>
    </div>
</x-app-layout>