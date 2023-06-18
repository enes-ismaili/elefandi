<div>
    <div class="single-image-upload unid-{{ $uid }}">
        @error('photo') {{ $message }} @enderror
        @error('photogallery.*') {{ $message }} @enderror
        <div id="single-image-upload">
            <div class="drop-area @if(!$uploadType && $singleFile && !$uploadError)upload @endif">
                <p>{{$paragraphText}}</p>
                @if($uploadType)
                    <input type="file" id="multiple" accept="file_extension" name="multipleFiles[]" wire:model="multipleFiles" multiple>
                    <label class="button" for="multiple">{{$buttonName}}</label>
                    @if(!empty($validation_errors["files"]))
                        @foreach($validation_errors["files"] as $k => $v)
                            <label for="" class="error" >{{ $v }}</label>
                        @endforeach
                    @endif
                    @error('multipleFiles.*') <span class="text-danger error ss">{{ $message }}</span>@enderror
                @else
                    @if($photo)
                        <div class="remove-image" wire:click="removeFile('{{ $photo }}', 3)"><i class="fas fa-times"></i></div>
                        @if($type == 3)
                            <video src="{{ asset('/photos/'.$path.$photo) }}" controls width="100%" height="auto"></video>
                        @else
                            <img src="{{ asset('/photos/'.$path.$photo) }}">
                        @endif
                        <input type="hidden" id="{{$inputName}}" name="{{$inputName}}" value="{{$photo}}">
                    @elseif($file)
                        <div class="remove-image" wire:click="removeFile('{{ $file }}', 2)"><i class="fas fa-times"></i></div>
                        <div class="single-file">
                            <a href="{{ asset('/photos/'.$path.$name) }}" target="_blank"><span>{{ $file }}</span></a>
                        </div>
                        <input type="hidden" id="{{$inputName}}" name="{{$inputName}}" value="{{$file}}">
                    @else
                        <input type="file" id="single" accept="file_extension" name="singleFile" wire:model="singleFile">
                        <label class="button" for="single">{{$buttonName}}</label>
                    @endif
                    @error('singleFile') <span class="text-danger error ss">{{ $message }}</span>@enderror
                @endif
                <p class="file-required">Madhësia maksimale: {{ round($this->maxSize / 1024).'MB' }}, Rezolucioni makisimal: {{ $maxWidth.'x'.$maxHeight.'px' }}</p>
            </div>
            @if(count($files))
                @if($style == 1)
                    <div class="additionalFiles">
                        @foreach($files as $file)
                            <div class="single-file">
                                @php
                                    $fileUrl = asset('/photos/'.$this->path.$file);
                                    $ext = pathinfo($fileUrl, PATHINFO_EXTENSION);
                                    $lExt = strtolower($ext);
                                @endphp
                                <input type="hidden" name="{{$inputName}}[]" value="{{ $file }}">
                                @if(in_array($lExt, array('png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp')))
                                    <a href="{{ $fileUrl }}" target="_blank"><img src="{{ $fileUrl }}" alt=""></a>
                                    <div class="remove-file" wire:click="removeFile('{{$file}}', 1)"><i class="fas fa-times"></i></div>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank"><span>{{ $file }}</span></a>
                                    <div class="remove-file" wire:click="removeFile('{{$file}}', 1)"><i class="fas fa-times"></i></div>
                                    {{-- <i class="fas fa-download"></i> --}}
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="upload-gallery">
                        @foreach ($files as $image)
                        <div class="single-image">
                            <input type="hidden" name="{{$inputName}}[]" value="{{ $image }}">
                            <div class="remove-image" wire:click="removeFile('{{$image}}')"><i class="fas fa-times"></i></div>
                            <img src="{{ asset('/photos/'.$this->path.$image) }}">
                        </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
    <script>
        @if($uploadType && !$singleFile)
        const dropArea{{ $uid }} = document.querySelector(".unid-{{ $uid }} .drop-area");
        dropArea{{ $uid }}.addEventListener("drop", handleDrop{{ $uid }}, false);
        ["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
            dropArea{{$uid}}.addEventListener(eventName, preventDefaults, false);
        });
        let files{{ $uid }} = [];
        function handleDrop{{ $uid }}(e){
            const dt = e.dataTransfer;
            let files = dt.files;
            files = [...files];
            @this.emit('single_file_choosedd{{$uid}}', files);
        }
        document.addEventListener("DOMContentLoaded", () => {
            window.livewire.on('single_file_choosedd{{$uid}}', function(files){
                try {
                    console.log(files);
                    files.forEach(file => {
                        console.log(file);
                        let reader = new FileReader();
                        reader.onloadend = () => {
                            window.livewire.emit('single_file_choosed1', reader.result, file.name, {{ $uid }});
                        }
                        reader.readAsDataURL(file);
                    })
                } catch (error) {
                    console.log(error);
                }
            });
        });
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        function highlight() {
            dropArea{{$uid}}.classList.add("highlight");
        }
        function unHighlight() {
            dropArea{{$uid}}.classList.remove("highlight");
        }
        ["dragenter", "dragover"].forEach(eventName => {
            dropArea{{$uid}}.addEventListener(eventName, highlight, false);
        });
        ["dragleave", "drop"].forEach(eventName => {
            dropArea{{$uid}}.addEventListener(eventName, unHighlight, false);
        });
        @endif
    </script>
</div>
