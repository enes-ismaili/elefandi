<?php

namespace App\Http\Controllers;

use App\Models\Pages;
use App\Models\VendorPages;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        if(check_permissions('manage_vendor')){
            $pages = Pages::all();
            return view('admin.pages.index', compact('pages'));
        }
        abort(404);
    }

    public function single($slug)
    {
        $page = Pages::where('slug', '=', $slug)->first();
        return view('pages.single', compact('page'));
    }

    public function create()
    {
        if(check_permissions('manage_vendor')){
            return view('admin.pages.create');
        }
        abort(404);
    }

    public function store(Request $request)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'description' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'description.required' => 'Përshkrimi është i detyrueshëm',
            ]);
            $pages = new Pages();
            $pages->name = $request->name;
            $pages->description = $request->description;
            $pages->save();
            session()->put('success','Faqja u krijua me sukses.');
            return redirect()->route('admin.pages.index');
        }
        abort(404);
    }

    public function edit($id)
    {
        if(check_permissions('manage_vendor') && is_numeric($id)){
            $page = Pages::where('id', '=', $id)->first();
            return view('admin.pages.edit', compact('page'));
        }
        abort(404);
    }

    public function update(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'description' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'description.required' => 'Përshkrimi është i detyrueshëm',
            ]);
            if(is_numeric($id)){
                $pages = Pages::findorfail($id);
                $pages->name = $request->name;
                $pages->slug = $request->slug;
                $pages->description = $request->description;
                $pages->save();
                session()->put('success','Ndryshimet për faqen u ruajtën.');
                return redirect()->route('admin.pages.index');
            }
        }
        abort(404);
    }

    public function vedit()
    {
        if(check_permissions('manage_vendor')){
            if(current_vendor()->pages){
                $pages = current_vendor()->pages;
                $new = false;
            } else {
                $pages = '';
                $new = true;
            }
            return view('admin.pages.vedit', compact('pages', 'new'));
        }
        abort(404);
    }

    public function vupdate(Request $request)
    {
        if(check_permissions('manage_vendor')){
            if(current_vendor()->pages){
                $pages = current_vendor()->pages;
            } else {
                $pages = new VendorPages();
                $pages->vendor_id = current_vendor()->id;
            }
            $pages->perdorimi = $request->perdorimi;
            $pages->kthimi = $request->kthimi;
            $pages->save();
            session()->put('success','Ndryshimet për faqet u ruajtën.');
            return redirect()->route('vendor.pages.edit');
        }
        abort(404);
    }

    public function delete($id)
    {
        if(check_permissions('manage_vendor') && check_permissions('delete_rights') && is_numeric($id)){
            $pages = Pages::findorfail($id);
            if($pages && $pages->delete == 1){
                $pages->delete();
                return redirect()->route('admin.pages.index');
            }
        }
        abort(404);
    }
}
