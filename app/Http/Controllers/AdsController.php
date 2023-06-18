<?php

namespace App\Http\Controllers;

use App\Models\Ads;
use App\Models\AdsSingle;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    public function viewads($id)
    {
        $cAds = AdsSingle::findOrFail($id);
        $currClicks = $cAds->click;
        $cAds->click = $currClicks + 1;
        $cAds->save();
        return redirect($cAds->link);
    }

    public function index()
    {
        if(check_permissions('manage_ads')){
            $ads = Ads::all();
            return view('admin.ads.index', compact('ads'));
        }
        abort(404);
    }

    public function editAds($id)
    {
        if(check_permissions('manage_ads') && is_numeric($id)){
            $ads = Ads::findOrFail($id);
            return view('admin.ads.edit', compact('ads'));
        }
        abort(404);
    }

    public function updateAds(Request $request, $id)
    {
        if(check_permissions('manage_ads') && is_numeric($id)){
            $validatedDate = $request->validate([
                'name' => 'required',
                'price' => 'required',
                'dimage' => 'required',
                'mimage' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'price.required' => 'Çmimi është i detyrueshëm',
                'dimage.required' => 'Foto Desktop është i detyrueshëm',
                'mimage.required' => 'Foto Mobile është i detyrueshëm',
            ]);
            $ads = Ads::findOrFail($id);
            $ads->name = $request->name;
            $ads->price = $request->price;
            $ads->dimage = $request->dimage;
            $ads->mimage = $request->mimage;
            $ads->save();
            session()->put('success','Reklama u ndryshua me sukses.');
            return redirect()->route('admin.ads.index');
        }
        abort(404);
    }

    public function single($id)
    {
        if(check_permissions('manage_ads') && is_numeric($id)){
            $mads = Ads::findOrFail($id);
            $adsW = $mads->ads()->where('astatus', '=', 0)->orderBy('created_at', 'DESC')->get();
            $ads = $mads->ads()->where('astatus', '=', 1)->orderBy('created_at', 'DESC')->get();
            return view('admin.ads.view', compact('mads', 'adsW', 'ads'));
        }
        abort(404);
    }

    public function addSingleAds($id)
    {
        if(check_permissions('manage_ads') && is_numeric($id)){
            $mads = Ads::findOrFail($id);
            return view('admin.ads.single.add', compact('mads'));
        }
        abort(404);
    }

    public function storeSingleAds(Request $request, $id)
    {
        if(check_permissions('manage_ads') && is_numeric($id)){
            $validatedDate = $request->validate([
                'link' => 'required',
                'dimage' => 'required',
            ], [
                'link.required' => 'Linku është i detyrueshëm',
                'dimage.required' => 'Imazhi i Reklamës është i detyrueshëm',
            ]);
            $mads = Ads::findOrFail($id);
            $ad = new AdsSingle();
            $ad->ads_id = $mads->id;
            $ad->link = $request->link;
            $ad->dimage = $request->dimage;
            // $ad->mimage = $request->mimage;
            $ad->astatus = 1;
            $ad->save();
            session()->put('success','Reklama u shtua me sukses.');
            return redirect()->route('admin.ads.view', $mads->id);
        }
        abort(404);
    }

    public function editSingleAds($id, $sid)
    {
        if(check_permissions('manage_ads') && is_numeric($id) && is_numeric($sid)){
            $mads = AdsSingle::findOrFail($id);
            $ads = AdsSingle::findOrFail($sid);
            return view('admin.ads.single.edit', compact('mads', 'ads'));
        }
    }

    public function updateSingleAds(Request $request, $id, $sid)
    {
        if(check_permissions('manage_ads') && is_numeric($id) && is_numeric($sid)){
            $validatedDate = $request->validate([
                'link' => 'required',
                'dimage' => 'required',
            ], [
                'link.required' => 'Linku është i detyrueshëm',
                'dimage.required' => 'Imazhi i Reklamës është i detyrueshëm',
            ]);
            $mads = Ads::findOrFail($id);
            $ad = AdsSingle::findOrFail($sid);
            $ad->link = $request->link;
            $ad->dimage = $request->dimage;
            // $ad->mimage = $request->mimage;
            $ad->astatus = $request->astatus;
            $ad->fvaction = $request->fvaction;
            $ad->fview = $request->fview;
            $ad->fcaction = $request->fcaction;
            $ad->fclick = $request->fclick;
            $ad->message = $request->message;
            $ad->save();
            session()->put('success','Reklama u ndryshua me sukses.');
            return redirect()->route('admin.ads.view', $mads->id);
        }
    }

    public function deleteSingleAds($id, $sid)
    {
        if(check_permissions('manage_ads') && check_permissions('delete_rights') && is_numeric($id) && is_numeric($sid)){
            $ad = AdsSingle::findOrFail($sid);
            if($ad->ads_id == $id){
                $ad->delete();
                session()->put('success','Reklama u fshi me sukses.');
                return redirect()->route('admin.ads.view', $id);
            }
        }
    }

    public function vindex()
    {
        if(check_permissions('manage_ads') && vendor_status()){
            $ads = Ads::all();
            return view('admin.ads.vindex', compact('ads'));
        }
        abort(404);
    }

    public function vsingle($id)
    {
        if(check_permissions('manage_ads') && vendor_status() && is_numeric($id)){
            $mads = Ads::findOrFail($id);
            $adsW = current_vendor()->ads()->where([['ads_id', '=', $id],['astatus', '=', 0]])->orderBy('created_at', 'DESC')->get();
            $ads = current_vendor()->ads()->where([['ads_id', '=', $id],['astatus', '=', 1]])->orderBy('created_at', 'DESC')->get();
            ray($adsW);
            return view('admin.ads.vview', compact('mads', 'adsW', 'ads'));
        }
        abort(404);
    }

    public function vaddSingleAds($id)
    {
        if(check_permissions('manage_ads') && vendor_status() && is_numeric($id)){
            $mads = Ads::findOrFail($id);
            return view('admin.ads.single.vadd', compact('mads'));
        }
        abort(404);
    }

    public function vstoreSingleAds(Request $request, $id)
    {
        if(check_permissions('manage_ads') && vendor_status() && is_numeric($id)){
            $validatedDate = $request->validate([
                'link' => 'required',
                'dimage' => 'required',
            ], [
                'link.required' => 'Linku është i detyrueshëm',
                'dimage.required' => 'Imazhi i Reklamës është i detyrueshëm',
            ]);
            $mads = Ads::findOrFail($id);
            $ad = new AdsSingle();
            $ad->vendor_id = current_vendor()->id;
            $ad->ads_id = $mads->id;
            $ad->link = $request->link;
            $ad->dimage = $request->dimage;
            $ad->message = $request->message;
            // $ad->mimage = $request->mimage;
            $ad->astatus = 0;
            $ad->save();
            session()->put('success','Reklama u shtua me sukses.');
            return redirect()->route('vendor.ads.view', $mads->id);
        }
        abort(404);
    }

    public function veditSingleAds($id, $sid)
    {
        if(check_permissions('manage_ads') && vendor_status() && is_numeric($id) && is_numeric($sid)){
            $mads = AdsSingle::findOrFail($id);
            $ads = AdsSingle::findOrFail($sid);
            return view('admin.ads.single.vedit', compact('mads', 'ads'));
        }
    }

    public function vupdateSingleAds(Request $request, $id, $sid)
    {
        if(check_permissions('manage_ads') && vendor_status() && is_numeric($id) && is_numeric($sid)){
            $validatedDate = $request->validate([
                'link' => 'required',
                'dimage' => 'required',
            ], [
                'link.required' => 'Linku është i detyrueshëm',
                'dimage.required' => 'Imazhi i Reklamës është i detyrueshëm',
            ]);
            $mads = Ads::findOrFail($id);
            $ad = AdsSingle::findOrFail($sid);
            $ad->link = $request->link;
            $ad->dimage = $request->dimage;
            $ad->message = $request->message;
            // $ad->mimage = $request->mimage;
            $ad->save();
            session()->put('success','Reklama u ndryshua me sukses.');
            return redirect()->route('vendor.ads.view', $mads->id);
        }
    }

    public function vdeleteSingleAds($id, $sid)
    {
        if(check_permissions('manage_ads') && vendor_status() && check_permissions('delete_rights') && is_numeric($id) && is_numeric($sid)){
            $ad = current_vendor()->ads->where('id', '=', $sid)->first();
            if($ad->ads_id == $id){
                $ad->delete();
                session()->put('success','Reklama u fshi me sukses.');
                return redirect()->route('vendor.ads.view', $id);
            }
        }
    }
}
 