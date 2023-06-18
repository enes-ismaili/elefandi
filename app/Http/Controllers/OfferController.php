<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Category;
use App\Models\OfferDetail;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function offers()
    {
        if(check_permissions('manage_offers')){
            $offers = Offer::where('vendor_id', '<>', '0')->orderBy('updated_at', 'DESC')->get();
            $specialOffers = Offer::where('vendor_id', '=', '0')->orderBy('updated_at', 'DESC')->get();
            return view('admin.offers.index', compact('offers', 'specialOffers'));
        }
        abort(404);
    }

    public function newoffers($id = '')
    {
        if(check_permissions('manage_offers')){
            $categories = Category::where('parent', '0')->get();
            $vendor = current_vendor();
            $prod_id = '';
            if(is_numeric($id)){
                $productId = current_vendor()->products()->where('id', '=', $id)->first();
                if($productId){
                    $prod_id = $id;
                }
            }
            return view('admin.offers.new', compact('categories', 'vendor', 'prod_id'));
        }
        abort(404);
    }

    public function saveoffers(Request $request)
    {
        if(check_permissions('manage_offers')){
            $minDate = '2020-10-01';
            $request->merge([
                'before_date' => $minDate
            ]);
            $validatedDate = $request->validate([
                'name' => 'required',
                'start_date' => 'required|date|after:before_date',
                'expire_date' => 'required|date|after_or_equal:start_date',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'start_date.required' => 'Data e Fillimit është e detyrueshëm',
                'start_date.date' => 'Data e Fillimit duhet të jetë në formatin datë',
                'start_date.after' => 'Data e Fillimit duhet të jetë me e madhe se data 1 Janar 2020',
                'expire_date.required' => 'Data e Mbarimit është e detyrueshëm',
                'expire_date.date' => 'Data e Mbarimit duhet të jetë në formatin datë',
                'expire_date.after_or_equal' => 'Data e Mbarimit duhet të jetë me e madhe se data e fillimit',
            ]);
            $offer = new Offer();
            $vendorId = 0;
            $offer->vendor_id = 0;
            $offer->name = $request->name;
            $offer->type = $request->type;
            $type = $request->type;
            if($type){
                if($type == 1){
                    $offer->action = $request->action;
                    $offer->discount = $request->discount;
                } else if($type == 2){
                    $offer->action = $request->action;
                    $offer->discount = $request->discount;
                } else if($type == 3){
                    $offer->action = 2;
                    $offer->discount = 0;
                } else if($type == 4){
                    $offer->description = $request->description;
                    $offer->action = 4;
                    $offer->discount = 0;
                }
            }
            $offer->start_date = $request->start_date.' 00:00:05';
            $offer->expire_date = $request->expire_date.' 23:59:58';
            $offer->save();
            if($type){
                if($type == 4){
                    $prodNum = 0;
                    if($request->product_id && count($request->product_id)) {
                        foreach($request->product_id as $product){
                            if($request->product[$prodNum]){
                                if($request->variant_id[$prodNum]){
                                    $variant_id = $request->variant_id[$prodNum];
                                } else {
                                    $variant_id = 0;
                                }
                                if(!$vendorId){
                                    $currProdudct = Product::where('id', '=', $product)->first();
                                    $vendorId = $currProdudct->owner->id;
                                }
                                $offerDetail = new OfferDetail();
                                $offerDetail->vendor_id = $vendorId;
                                $offerDetail->offer_id = $offer->id;
                                $offerDetail->prod_id = $product;
                                $offerDetail->variant_id = $variant_id;
                                $offerDetail->action = 3;
                                $offerDetail->discount = $request->product[$prodNum];
                                $offerDetail->type = $type;
                                $offerDetail->save();
                            }
                            $prodNum++;
                        }
                    }
                }
            }
            session()->put('success','Oferta u krijua me sukses.');
            return redirect()->route('admin.offers.index');
        }
        abort(404);
    }

    public function editoffers($id)
    {
        if(check_permissions('manage_offers')){
            $offer = Offer::findorfail($id);
            $vendor = current_vendor();
            $categories = Category::where('parent', '0')->get();
            return view('admin.offers.edit', compact('offer', 'vendor', 'categories'));
        }
        abort(404);
    }

    public function storeoffers(Request $request, $id)
    {
        // dd($request);
        if(check_permissions('manage_offers')){
            $minDate = '2020-10-01';
            $request->merge([
                'before_date' => $minDate
            ]);
            $validatedDate = $request->validate([
                'name' => 'required',
                'type' => 'required',
                'start_date' => 'required|date|after:before_date',
                'expire_date' => 'required|date|after_or_equal:start_date',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'type.required' => 'Tipi është i detyrueshëm',
                'start_date.required' => 'Data e Fillimit është e detyrueshëm',
                'start_date.date' => 'Data e Fillimit duhet të jetë në formatin datë',
                'start_date.after' => 'Data e Fillimit duhet të jetë me e madhe se data 1 Janar 2020',
                'expire_date.required' => 'Data e Mbarimit është e detyrueshëm',
                'expire_date.date' => 'Data e Mbarimit duhet të jetë në formatin datë',
                'expire_date.after_or_equal' => 'Data e Mbarimit duhet të jetë me e madhe se data e fillimit',
            ]);

            $offer = Offer::findorfail($id);
            $vendorId = $offer->vendor_id;
            $offer->name = $request->name;
            if($request->discount){
                $requestDiscount = $request->discount;
            } else {
                $requestDiscount = 0;
            }
            $type = $offer->type;
            if($type){
                if($type == 1){
                    $offer->action = $request->action;
                    $offer->discount = $requestDiscount;
                } else if($type == 2){
                    $offer->action = $request->action;
                    $offer->discount = $requestDiscount;
                } else if($type == 4){
                    $offer->description = $request->description;
                    $offer->action = 4;
                    $offer->discount = 0;
                }
            }
            $offer->start_date = $request->start_date.' 00:00:05';
            $offer->expire_date = $request->expire_date.' 23:59:58';
            $offer->save();
            if($type){
                if($type == 1){
                    $offerDetails = OfferDetail::updateOrCreate(
                        ['vendor_id' => $vendorId, 'offer_id'=> $id, 'type'=> $type, 'prod_id'=> $vendorId],
                        ['action' => $request->action, 'discount' => $requestDiscount, 'active'=> 1]
                    );
                } else if($type == 2){
                    $offerDetails = OfferDetail::updateOrCreate(
                        ['vendor_id' => $vendorId, 'offer_id'=> $id, 'type'=> $type],
                        ['prod_id'=> $request->category, 'action' => $request->action, 'discount' => $requestDiscount, 'active'=> 1]
                    );
                } else if($type == 3){
                    $prodNum = 0;
                    $deletedOfferDetail = OfferDetail::where([
                        ['offer_id', '=', $id],
                        ['vendor_id', '=', $vendorId],
                        ['type', '=', $type],
                    ])->delete();
                    if($request->product_id && count($request->product_id)) {
                        foreach($request->product_id as $product){
                            if(!$vendorId){
                                $currProdudct = Product::where('id', '=', $product)->first();
                                $vendorId = $currProdudct->owner->id;
                            }
                            if($request->product[$prodNum]){
                                if($request->variant_id[$prodNum]){
                                    $variant_id = $request->variant_id[$prodNum];
                                } else {
                                    $variant_id = 0;
                                }
                                if($request->product[$prodNum]){
                                    $reqDisProd = $request->product[$prodNum];
                                } else {
                                    $reqDisProd = 0;
                                }
                                $offerDetails = OfferDetail::updateOrCreate(
                                    ['vendor_id' => $vendorId, 'offer_id'=> $id, 'type'=> $type, 'prod_id'=> $product, 'variant_id'=>$variant_id],
                                    ['action' => 3, 'discount' => $reqDisProd, 'active'=> 1]
                                );
                            }
                            $prodNum++;
                        }
                    }
                } else if($type == 4){
                    $prodNum = 0;
                    $deletedOfferDetail = OfferDetail::where([
                        ['offer_id', '=', $id],
                        ['type', '=', $type],
                    ])->delete();
                    if($request->product_id && count($request->product_id)) {
                        foreach($request->product_id as $product){
                            $currProdudct = Product::where('id', '=', $product)->first();
                            $vendorId = $currProdudct->owner->id;
                            if($currProdudct){
                                $variant_id = 0;
                                if($request->variant_id[$prodNum]){
                                    $variant_id = $request->variant_id[$prodNum];
                                }
                                $reqDisProd = 0;
                                if($request->product[$prodNum]){
                                    $reqDisProd = $request->product[$prodNum];
                                }
                                $offerDetails = OfferDetail::updateOrCreate(
                                    ['vendor_id' => $vendorId, 'offer_id'=> $id, 'type'=> $type, 'prod_id'=> $product, 'variant_id'=>$variant_id],
                                    ['action' => 3, 'discount' => $reqDisProd, 'active'=> 1]
                                );
                            }
                            $prodNum++;
                        }
                    }
                    
                }
            }
            session()->put('success','Oferta u ndryshua me sukses.');
            return redirect()->route('admin.offers.index');
        }
        abort(404);
    }

    public function deleteoffers($id)
    {
        if(check_permissions('manage_offers') && check_permissions('delete_rights') && is_numeric($id)){
            $offer = Offer::findorfail($id);
            $offer->delete();
            session()->put('success','Oferta u fshi me sukses.');
            return redirect()->route('admin.offers.index');
        }
        abort(404);
    }

    public function voffers()
    {
        if(check_permissions('manage_offers') && vendor_status()){
            $offers = current_vendor()->offers()->orderBy('updated_at', 'DESC')->get();
            $specialOffers = Offer::where('vendor_id', '=', 0)->get();
            return view('admin.offers.vindex', compact('offers', 'specialOffers'));
        }
        abort(404);
    }

    public function vnewoffers($id = '')
    {
        if(check_permissions('manage_offers') && vendor_status()){
            $categories = Category::where('parent', '0')->get();
            $vendor = current_vendor();
            $prod_id = '';
            if(is_numeric($id)){
                $productId = current_vendor()->products()->where('id', '=', $id)->first();
                if($productId){
                    $prod_id = $id;
                }
            }
            return view('admin.offers.vnew', compact('categories', 'vendor', 'prod_id'));
        }
        abort(404);
    }

    public function vsaveoffers(Request $request)
    {
        if(check_permissions('manage_offers') && vendor_status()){
            $validatedDate = $request->validate([
                'name' => 'required',
                'type' => 'required',
                'category' => 'required_if:type,2|numeric|min:1',
                'product_id' => 'required_if:type,3|array',
                'start_date' => 'required|date|after:yesterday',
                'expire_date' => 'required|date|after_or_equal:start_date',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'type.required' => 'Tipi është i detyrueshëm',
                'category.required_if' => 'Kategoritë është e detyrueshme',
                'category.numeric' => 'Kategoritë është e detyrueshme',
                'category.min' => 'Kategoritë është e detyrueshme',
                'product_id.required_if' => 'Produkti është i detyrueshëm',
                'product_id.array' => 'Produkti është i detyrueshëm',
                'start_date.required' => 'Data e Fillimit është e detyrueshëm',
                'start_date.date' => 'Data e Fillimit duhet të jetë në formatin datë',
                'start_date.after' => 'Data e Fillimit duhet të jetë me e madhe se data 1 Janar 2020',
                'expire_date.required' => 'Data e Mbarimit është e detyrueshëm',
                'expire_date.date' => 'Data e Mbarimit duhet të jetë në formatin datë',
                'expire_date.after_or_equal' => 'Data e Mbarimit duhet të jetë me e madhe se data e fillimit',
            ]);
            $offer = new Offer();
            $vendorId = current_vendor()->id;
            $offer->vendor_id = $vendorId;
            $offer->name = $request->name;
            $offer->type = $request->type;
            $type = $request->type;
            if($type){
                if($type == 1){
                    $offer->action = $request->action;
                    $offer->discount = $request->discount;
                } else if($type == 2){
                    $offer->action = $request->action;
                    $offer->discount = $request->discount;
                } else if($type == 3){
                    $offer->action = 2;
                    $offer->discount = 0;
                } else if($type == 3){
                    $offer->action = 4;
                    $offer->discount = 0;
                }
            }
            $offer->start_date = $request->start_date.' 00:00:05';
            $offer->expire_date = $request->expire_date.' 23:59:58';
            $offer->save();
            if($type){
                if($type == 1){
                    $offerDetail = new OfferDetail();
                    $offerDetail->vendor_id = $vendorId;
                    $offerDetail->offer_id = $offer->id;
                    $offerDetail->prod_id = $vendorId;
                    $offerDetail->action = $request->action;
                    $offerDetail->discount = $request->discount;
                    $offerDetail->type = $type;
                    $offerDetail->save();
                } else if($type == 2){
                    $currCategories = OfferDetail::where('offer_id', $offer->id)->update(['active'=> 0]);
                    $currCategory = Category::find($request->category);
                    if($currCategory){
                        $offerDetails = OfferDetail::updateOrCreate(
                            ['vendor_id' => $vendorId, 'offer_id'=> $offer->id, 'type'=> $type, 'prod_id'=> $currCategory->id],
                            ['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
                        );
                        foreach($currCategory->children as $secondLevel){
                            $offerDetails = OfferDetail::updateOrCreate(
                                ['vendor_id' => $vendorId, 'offer_id'=> $offer->id, 'type'=> $type, 'prod_id'=> $secondLevel->id],
                                ['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
                            );
                            foreach($secondLevel->children as $thirdLevel){
                                $offerDetails = OfferDetail::updateOrCreate(
                                    ['vendor_id' => $vendorId, 'offer_id'=> $offer->id, 'type'=> $type, 'prod_id'=> $thirdLevel->id],
                                    ['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
                                );
                            }
                        }
                    }
                    OfferDetail::where('offer_id', $offer->id)->where('active', 0)->delete();
                } else if($type == 3){
                    $prodNum = 0;
                    if($request->product_id && count($request->product_id)) {
                        foreach($request->product_id as $product){
                            if($request->product[$prodNum]){
                                if($request->variant_id[$prodNum]){
                                    $variant_id = $request->variant_id[$prodNum];
                                } else {
                                    $variant_id = 0;
                                }
                                $offerDetail = new OfferDetail();
                                $offerDetail->vendor_id = $vendorId;
                                $offerDetail->offer_id = $offer->id;
                                $offerDetail->prod_id = $product;
                                $offerDetail->variant_id = $variant_id;
                                $offerDetail->action = 3;
                                $offerDetail->discount = $request->product[$prodNum];
                                $offerDetail->type = $type;
                                $offerDetail->save();
                            }
                            $prodNum++;
                        }
                    }
                }
            }
            
            session()->put('success','Oferta u krijua me sukses.');
            return redirect()->route('vendor.offers.index');
        }
        abort(404);
    }

    public function veditoffers($id)
    {
        if(check_permissions('manage_offers') && vendor_status()){
            $offer = Offer::findorfail($id);
            $vendor = current_vendor();
            $categories = Category::where('parent', '0')->get();
            return view('admin.offers.vedit', compact('offer', 'vendor', 'categories'));
        }
        abort(404);
    }

    public function vstoreoffers(Request $request, $id)
    {
        if(check_permissions('manage_offers') && vendor_status()){
            $minDate = '2020-10-01';
            $request->merge([
                'before_date' => $minDate
            ]);
            $validatedDate = $request->validate([
                'name' => 'required',
                'type' => 'required',
                'category' => 'required_if:type,2|numeric|min:1',
                'product_id' => 'required_if:type,3|array',
                'start_date' => 'required|date|after:before_date',
                'expire_date' => 'required|date|after_or_equal:start_date',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'type.required' => 'Tipi është i detyrueshëm',
                'category.required_if' => 'Kategoritë është e detyrueshme',
                'category.numeric' => 'Kategoritë është e detyrueshme',
                'category.min' => 'Kategoritë është e detyrueshme',
                'product_id.required_if' => 'Produkti është i detyrueshëm',
                'product_id.array' => 'Produkti është i detyrueshëm',
                'start_date.required' => 'Data e Fillimit është e detyrueshëm',
                'start_date.date' => 'Data e Fillimit duhet të jetë në formatin datë',
                'start_date.after' => 'Data e Fillimit duhet të jetë me e madhe se data 1 Janar 2020',
                'expire_date.required' => 'Data e Mbarimit është e detyrueshëm',
                'expire_date.date' => 'Data e Mbarimit duhet të jetë në formatin datë',
                'expire_date.after_or_equal' => 'Data e Mbarimit duhet të jetë me e madhe se data e fillimit',
            ]);
            $offer = Offer::findorfail($id);
            $vendorId = $offer->vendor_id;
            $type = $offer->type;
            if($type != 4){
                $offer->name = $request->name;
            }
            if($type){
                if($type == 1){
                    $offer->action = $request->action;
                    $offer->discount = $request->discount;
                } else if($type == 2){
                    $offer->action = $request->action;
                    $offer->discount = $request->discount;
                }
            }
            $offer->start_date = $request->start_date.' 00:00:05';
            $offer->expire_date = $request->expire_date.' 23:59:58';
            $offer->save();
            if($type){
                if($type == 1){
                    $offerDetails = OfferDetail::updateOrCreate(
                        ['vendor_id' => $vendorId, 'offer_id'=> $id, 'type'=> $type, 'prod_id'=> $vendorId],
                        ['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
                    );
                } else if($type == 2){
                    $currCategories = OfferDetail::where('offer_id', $id)->update(['active'=> 0]);
                    $currCategory = Category::find($request->category);
                    if($currCategory){
                        $offerDetails = OfferDetail::updateOrCreate(
                            ['vendor_id' => $vendorId, 'offer_id'=> $id, 'type'=> $type, 'prod_id'=> $currCategory->id],
                            ['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
                        );
                        foreach($currCategory->children as $secondLevel){
                            $offerDetails = OfferDetail::updateOrCreate(
                                ['vendor_id' => $vendorId, 'offer_id'=> $id, 'type'=> $type, 'prod_id'=> $secondLevel->id],
                                ['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
                            );
                            foreach($secondLevel->children as $thirdLevel){
                                $offerDetails = OfferDetail::updateOrCreate(
                                    ['vendor_id' => $vendorId, 'offer_id'=> $id, 'type'=> $type, 'prod_id'=> $thirdLevel->id],
                                    ['action' => $request->action, 'discount' => $request->discount, 'active'=> 1]
                                );
                            }
                        }
                    }
                    OfferDetail::where('offer_id', $id)->where('active', 0)->delete();
                } else if($type == 3){
                    $prodNum = 0;
                    $deletedOfferDetail = OfferDetail::where([
                        ['offer_id', '=', $id],
                        ['vendor_id', '=', $vendorId],
                        ['type', '=', $type],
                    ])->delete();
                    if($request->product_id && count($request->product_id)) {
                        foreach($request->product_id as $product){
                            if($request->variant_id[$prodNum]){
                                $variant_id = $request->variant_id[$prodNum];
                            } else {
                                $variant_id = 0;
                            }
                            $prodDisc = 0;
                            if($request->product[$prodNum]){
                                $prodDisc = $request->product[$prodNum];
                            }
                            $offerDetails = OfferDetail::updateOrCreate(
                                ['vendor_id' => $vendorId, 'offer_id'=> $id, 'type'=> $type, 'prod_id'=> $product, 'variant_id'=>$variant_id],
                                ['action' => 3, 'discount' => $prodDisc, 'active'=> 1]
                            );
                            $prodNum++;
                        }
                    }
                } else if($type == 4){
                    $prodNum = 0;
                    $deletedOfferDetail = OfferDetail::where([
                        ['offer_id', '=', $id],
                        ['vendor_id', '=', current_vendor()->id],
                        ['type', '=', $type],
                    ])->delete();
                    if($request->product_id && count($request->product_id)) {
                        foreach($request->product_id as $product){
                            $currProdudct = Product::where('id', '=', $product)->first();
                            if($currProdudct){
                                $vendorId = $currProdudct->owner->id;
                                if($request->variant_id[$prodNum]){
                                    $variant_id = $request->variant_id[$prodNum];
                                } else {
                                    $variant_id = 0;
                                }
                                $prodDisc = 0;
                                if($request->product[$prodNum]){
                                    $prodDisc = $request->product[$prodNum];
                                }
                                $offerDetails = OfferDetail::updateOrCreate(
                                    ['vendor_id' => $vendorId, 'offer_id'=> $id, 'type'=> $type, 'prod_id'=> $product, 'variant_id'=>$variant_id],
                                    ['action' => 3, 'discount' => $prodDisc, 'active'=> 1]
                                );
                            }
                            $prodNum++;
                        }
                    }
                }
            }
            session()->put('success','Ndryshimet në ofertën u ruajtën me sukses.');
            return redirect()->route('vendor.offers.index');
        }
        abort(404);
    }

    public function vdeleteoffers($id)
    {
        if(check_permissions('manage_offers') && vendor_status() && check_permissions('delete_rights') && is_numeric($id)){
            $offer = Offer::findorfail($id);
            $offer->details()->delete();
            $offer->delete();
            session()->put('success','Oferta u fshi me sukses.');
            return redirect()->route('vendor.offers.index');
        }
        abort(404);
    }
}
