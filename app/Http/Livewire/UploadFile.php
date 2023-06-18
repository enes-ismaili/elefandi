<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;

class UploadFile extends Component
{
    use WithFileUploads;
    public $fileTitle, $fileName = '';
    // public $photo;
    public $photogallery;
    public $name = '';
    public $images = [];
    public $imagesName = '';
    public $addedFiles = [];

    public $inputName = 'image_id';
    public $exis = '';
    public $path = '';
    public $uploadType = 0; // upload single 0 or multiple 1
    
    public $type = 1; // type 1 files, type 2 images, type 3 video
    public $singleFile;
    public $file; // single file
    public $photo; // single file type image
    public $multipleFiles;
    public $files = []; // multiple files
    public $deleteF = true;
    public $maxSize = 3072;
    public $maxWidth = 500;
    public $maxHeight = 500;

    // Customize
    public $style = 2; // style 1 list, style 2 gallery
    public $buttonName = 'Ngarko Foto';
    public $paragraphText = 'Ngarko nje ose disa foto / dokumente';
    public $uid = 1;

    public $image;
    public $validation_errors = [];
    public $sizes = [];

    public $uploadError = false;

    public $listeners = [
        // 'single_file_choosed' => 'uploadJs',
        'single_file_choosed1' => 'uploadJs'
    ];

    // TODO: Styles gallery or list

    public function mount($upload, $exis = '')
    {
        if($upload == 'multiple'){
            $this->uploadType = 1;
            if($exis){
                if(is_object($exis)){
                    foreach($exis as $galery){
                        $this->images[] = $galery;
                        $this->files[] = $galery;
                    }
                } else {
                    $this->images[] = $exis;
                    $this->files[] = $exis;
                }
            }
        } else {
            if($exis){
                $this->name = $exis;
                $this->photo = $exis;
                $this->singleFile = $exis;
            }
        }
    }
    
    public function render()
    {
        return view('livewire.upload-file');
    }

    // Click Upload
    public function updatedSingleFile()
    {
        $this->uploadError = true;
        if($this->type == 2){
            $this->validate(
                ['singleFile' => 'image|max:'.$this->maxSize.'|dimensions:max_width='.$this->maxWidth.',max_height='.$this->maxHeight],
                [
                    'singleFile.image' => 'Lejohet të ngarkoni vetëm foto.',
                    'singleFile.max' => 'Imazhi që po hidhni është më i madh se '.round($this->maxSize / 1024).'MB.',
                    'singleFile.dimensions' => 'Imazhi nuk duhet të jetë më shume se '.$this->maxWidth.'x'.$this->maxHeight.'px',
                ],
                ['singleFile' => 'File']
            );
        } else if($this->type == 3){
            $this->validate(
                ['singleFile' => 'video|max:'.$this->maxSize.'|dimensions:max_width='.$this->maxWidth.',max_height='.$this->maxHeight],
                [
                    'singleFile.image' => 'Lejohet të ngarkoni vetëm video.',
                    'singleFile.max' => 'Video që po hidhni është më i madh se '.round($this->maxSize / 1024).'MB.',
                    'singleFile.dimensions' => 'Video nuk duhet të jetë më shume se '.$this->maxWidth.'x'.$this->maxHeight.'px',
                ],
                ['singleFile' => 'File']
            );
        } else {
            $this->validate(
                ['singleFile' => 'mimes:jpg,bmp,png,jpeg,gif,svg,webp,doc,docx,pdf,xls,xlsx|max:'.$this->maxSize.'|dimensions:max_width='.$this->maxWidth.',max_height='.$this->maxHeight],
                [
                    'singleFile.image' => 'Lejohet të ngarkoni vetëm foto dhe dokumente pdf, word, excel.',
                    'singleFile.max' => 'Imazhi që po hidhni është më i madh se '.round($this->maxSize / 1024).'MB.',
                ],
                ['singleFile' => 'File']
            );
        }
        $this->uploadError = false;
        $this->name = $this->singleFile->getClientOriginalName();
        $exists = Storage::disk('local')->exists('photos/'.$this->path.$this->name);
        if ($exists) {
            $increment = 0;
            if (preg_match('/(^.*?)+(?:\((\d+)\))?(\.(?:\w){0,3}$)/si', $this->name, $regs)){
                $filename = $regs[1];
                $fileext = $regs[3];
                $this->name = $filename.$fileext;
                while(Storage::disk('local')->exists('photos/'.$this->path.$this->name)) {
                    $increment++;
                    $this->name = $filename.$increment.$fileext;
                }
            }
        }
        $newPathS = '';
        if($this->path){
            $newPathS = '/'.$this->path;
        }
        $filename = $this->singleFile->storeAs('photos'.$newPathS, $this->name);
        $imageExtensions = ['jpg','bmp','png','jpeg','gif','svg','webp'];
        if(in_array($this->singleFile->extension(), $imageExtensions)) {
            $this->photo = $this->name;
            $this->file = '';
            if(count($this->sizes)){
                $imgFile = Image::make($this->singleFile);
                foreach($this->sizes as $size){
                    $thumbnail = $imgFile->fit($size, $size, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $pathss = public_path('photos/products/'.$size)."/".$this->name;
                    $thumbnail->save($pathss);
                }
            }
        } else {
            $this->photo = '';
            $this->file = $this->name;
        }
    }
    public function updatedMultipleFiles()
    {
        if($this->type == 2){
            $this->validate(
                ['multipleFiles.*' => 'image|max:'.$this->maxSize.'|dimensions:max_width='.$this->maxWidth.',max_height='.$this->maxHeight],
                [
                    'multipleFiles.*.image' => 'Lejohet të ngarkoni vetëm foto.',
                    'multipleFiles.*.max' => 'Imazhi që po hidhni është më i madh se '.round($this->maxSize / 1024).'MB.',
                ],
                ['multipleFiles.*' => 'File']
            );
        } else if($this->type == 3){
            $this->validate(
                ['multipleFiles.*' => 'video|max:'.$this->maxSize.'|dimensions:max_width='.$this->maxWidth.',max_height='.$this->maxHeight],
                [
                    'multipleFiles.*.image' => 'Lejohet të ngarkoni vetëm video.',
                    'multipleFiles.*.max' => 'Video që po hidhni është më i madh se '.round($this->maxSize / 1024).'MB.',
                ],
                ['multipleFiles.*' => 'File']
            );
        } else {
            $this->validate(
                ['multipleFiles.*' => 'mimes:jpg,bmp,png,jpeg,gif,svg,webp,doc,docx,pdf,xls,xlsx|max:'.$this->maxSize.'|dimensions:max_width='.$this->maxWidth.',max_height='.$this->maxHeight],
                [
                    'multipleFiles.*.image' => 'Lejohet të ngarkoni vetëm foto.',
                    'multipleFiles.*.max' => 'Imazhi që po hidhni është më i madh se '.round($this->maxSize / 1024).'MB.',
                ],
                ['multipleFiles.*' => 'File']
            );
        }
        foreach($this->multipleFiles as $file){
            $this->name = $file->getClientOriginalName();
            $exists = Storage::disk('local')->exists('photos/'.$this->path.$this->name);
            if ($exists) {
                $increment = 0;
                if (preg_match('/(^.*?)+(?:\((\d+)\))?(\.(?:\w){0,4}$)/si', $this->name, $regs)){
                    $filename = $regs[1];
                    $fileext = $regs[3];
                    $this->name = $filename.$fileext;
                    while(Storage::disk('local')->exists('photos/'.$this->path.$this->name)) {
                        $increment++;
                        $this->name = $filename.$increment.$fileext;
                    }
                }
            }
            $newPathS = '';
            if($this->path){
                $newPathS = '/'.$this->path;
            }
            $filename = $file->storeAs('photos'.$newPathS, $this->name);
            $this->files[] = $this->name;
            if(count($this->sizes)){
                $imgFile = Image::make($file);
                foreach($this->sizes as $size){
                    $thumbnail = $imgFile->fit($size, $size, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $pathss = public_path('photos/products/'.$size)."/".$this->name;
                    $thumbnail->save($pathss);
                }
            }
        }
    }

    // Drag & Drop
    public function uploadJs($files, $name, $uniqueId)
    {
        if($uniqueId == $this->uid){
            $file_data = $this->getFileInfo($files);
    
            $rules=[
                'files' => 'required',
            ];
            $messages = [
                "files.required" => "Choose at least one file.",
            ];
            $validator = Validator::make([
                "files" => $files,
            ],$rules, $messages);
    
            $validator->after(function ($validator) {
                if($this->type == 2){
                    if($this->getFileInfo($validator->getData()["files"])["file_type"] != "image"){
                        $validator->errors()->add('files', 'Formati i lejuar është Foto');   
                    }
                } else if($this->type == 3){
                    if($this->getFileInfo($validator->getData()["files"])["file_type"] != "video"){
                        $validator->errors()->add('files', 'Formati i lejuar është Video');   
                    }
                } else {
                    if(!in_array($this->getFileInfo($validator->getData()["files"])["file_extension"], array('png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp', 'doc', 'docx', 'pdf', 'xls', 'xlsx'))){
                        $validator->errors()->add('files', 'Ngarkoni Foto ose Dokument (Word, Pdf, Excel)');
                    }
                }
            });
            if($validator->fails()){
                return $this->validation_errors = $validator->errors()->toArray();
            }else{
    
                $this->name = $name;
                $exists = Storage::disk('local')->exists('photos/'.$this->path.$this->name);
                if ($exists) {
                    $increment = 0;
                    if (preg_match('/(^.*?)+(?:\((\d+)\))?(\.(?:\w){0,3}$)/si', $this->name, $regs)){
                        $filename = $regs[1];
                        $fileext = $regs[3];
                        $this->name = $filename.$fileext;
                        while(Storage::disk('local')->exists('photos/'.$this->path.$this->name)) {
                            $increment++;
                            $this->name = $filename.$increment.$fileext;
                        }
                    }
                }
                
                // $file_name = time().'-'.current_user()->id.mt_rand(100, 999).'.'.$file_data['file_extension'];
                $result = Storage::disk('local')->put('photos/'.$this->path.$this->name, $file_data['decoded_file']);
                if($this->uploadType == 1){
                    $this->files[] = $this->name;
                } else {
                    $this->photo = '';
                    $this->file = $this->name;
                }
                if(count($this->sizes)){
                    $imgFile = Image::make('photos/'.$this->path.$this->name);
                    foreach($this->sizes as $size){
                        $thumbnail = $imgFile->fit($size, $size, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $pathss = public_path('photos/products/'.$size)."/".$this->name;
                        $thumbnail->save($pathss);
                    }
                }
            }
        }
    }
    public function getFileInfo($file){
        $info = [
            "decoded_file" => NULL,
            "file_meta" => NULL,
            "file_mime_type" => NULL,
            "file_type" => NULL,
            "file_extension" => NULL,
        ];
        try{
            $info['decoded_file'] = base64_decode(substr($file, strpos($file, ',') + 1));
            $info['file_meta'] = explode(';', $file)[0];
            $info['file_mime_type'] = explode(':', $info['file_meta'])[1];
            $info['file_type'] = explode('/', $info['file_mime_type'])[0];
            $info['file_extension'] = explode('/', $info['file_mime_type'])[1];
        }catch(Exception $ex){
            // TODO: Add Catch
        }

        return $info;
    }
    public function removeFile($file, $type)
    {
        //TODO: Condition to delete or not exists
        $exists = Storage::disk('local')->exists('photos/'.$this->path.$file);
        if($exists) {
            if($this->deleteF){
                Storage::delete('photos/'.$this->path.$file);
            }
        }
        if($type == 3){
            $this->photo = '';
            $this->singleFile = '';
        } else if($type == 2){
            $this->file = '';
            $this->singleFile = '';
        } else {
            $newAddedFiles = array_diff( $this->files, [$file] );
            $this->files = $newAddedFiles;
        }
    }
}
