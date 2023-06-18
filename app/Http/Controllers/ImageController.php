<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function image(Request $request)
    {
        // request()->validate([
        //     'file'  => 'required|mimes:doc,docx,pdf,txt|max:2048',
        // ]);
        if ($files = $request->file('file')) {
            $name = $request->file->getClientOriginalName();
            $exists = Storage::disk('local')->exists('photos/'.$name);
            if ($exists) {
                $increment = 0;
                if (preg_match('/(^.*?)+(?:\((\d+)\))?(\.(?:\w){0,3}$)/si', $name, $regs)){
                    $filename = $regs[1];
                    $fileext = $regs[3];
                    $name = $filename.$fileext;
                    while(Storage::disk('local')->exists('photos/'.$name)) {
                        $increment++;
                        $name = $filename.$increment.$fileext;
                    }
                }
            }
            $filename = $request->file('file')->storeAs('photos', $name);
            return Response()->json([
                "success" => true,
                "file" => asset($filename),
                "name" => $name
            ]);
        }
        return Response()->json([
            "success" => false,
            "file" => ''
        ]);

    //     if ($files = $request->file('file')) {
    //         $file = $request->file->store('public/photos');
    //         // Storage::disk('local')->exists('photos/'.$this->name);
    //         return Response()->json([
    //             "success" => true,
    //             "file" => $file
    //         ]);
    //     }
    //     return Response()->json([
    //         "success" => false,
    //         "file" => ''
    //   ]);
    }

    public function remove(Request $request)
    {
        $re = '/.*(\/storage\/photos\/)/';
        $result = preg_replace($re, '', $request->image);
        if(Storage::disk('local')->exists('photos/'.$result)){
            Storage::delete('photos/'.$result);
            return Response()->json([
                "success" => true,
            ]);
        }
    }
}
