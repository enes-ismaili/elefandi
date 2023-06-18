<?php

namespace App\Http\Livewire\Offer;

use Carbon\Carbon;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;

class CreateOffer extends Component
{
    protected $listeners = [
        'addCategory'
    ];

    public $showForm = false;
    public $exis = false;
    public $prod_id;
    public $current;
    public $searchAll;
    public $editRole;
    public $selectedType;
    public $categories;
    public $selectedCategories;
    public $offerTitle = 'Krijo Ofertë';
    public $vendor_id;
    public $products;
    public $prod;
    public $selectedProduct;
    public $selectedProductVendor;
    public $selectedProductId;
    public $name;
    public $action;
    public $discount;
    public $startDate;
    public $endDate;

    protected $queryString = [
        'prod' => ['except' => '']
    ];

    public function mount()
    {
        $categories = Category::where('parent', '=', 0)->get();
        $this->categories = $categories;
        if($this->prod_id){
            $productId = $this->prod_id;
            $this->selectedProduct[$productId] = Product::where('id', '=', $productId)->first();
            $this->selectedType = 3;
            $this->showForm = true;
        }

        $this->startDate = Carbon::now();
        $this->endDate = Carbon::tomorrow();
        
        if($this->exis){
            $type = $this->current->type;
            $this->showForm = true;
            $this->selectedType = $type;
            $this->name = $this->current->name;
            $this->startDate = $this->current->start_date;
            $this->endDate = $this->current->expire_date;
            if($type == 1){
                $this->vendor_id = $this->current->vendor_id;
                $this->action = $this->current->action;
                $this->discount = $this->current->discount;
            } else if ($type == 2){
                $this->selectedCategories = $this->current->details()->first()->prod_id;
                $this->action = $this->current->action;
                $this->discount = $this->current->discount;
            } else if ($type == 3){
                // ray($this->current->details);
                foreach($this->current->details as $product){
                    if($this->selectedProduct && isset($this->selectedProduct[$product->prod_id])){
                    } else {
                        $this->selectedProduct[$product->prod_id] = Product::where('id', '=', $product->prod_id)->first();
                    }
                    if($product->variant_id){
                        $this->selectedProduct[$product->prod_id]['v'.$product->variant_id] = ['nprice'=>$product->discount] ;
                    } else {
                        $this->selectedProduct[$product->prod_id]['nprice'] = $product->discount;
                    }
                    $this->selectedProductId[$product->prod_id] = $product->prod_id;
                }
                // $this->selectedType = $this->current->type;
                // foreach($this->categories as $)
            } else if ($type == 4){
                if(auth()->user()->role == 2 && $this->editRole){
                    $this->searchAll = true;
                    $allProducts = $this->current->details;
                } else {
                    $this->searchAll = false;
                    $allProducts = $this->current->details->where('vendor_id', '=', current_vendor()->id);
                }
                foreach($allProducts as $product){
                    $currProduct = Product::where('id', '=', $product->prod_id)->first();
                    if($this->selectedProduct && isset($this->selectedProduct[$currProduct->owner->id][$product->prod_id])){
                    } else {
                        $this->selectedProduct[$currProduct->owner->id][$product->prod_id] = $currProduct;
                    }
                    if($product->variant_id){
                        $this->selectedProduct[$currProduct->owner->id][$product->prod_id]['v'.$product->variant_id] = ['nprice'=>$product->discount] ;
                    } else {
                        $this->selectedProduct[$currProduct->owner->id][$product->prod_id]['nprice'] = $product->discount;
                    }
                    // $this->selectedProduct[$currProduct->owner->id][$product->prod_id] = $currProduct;
                    // $this->selectedProduct[$currProduct->owner->id][$product->prod_id]['nprice'] = $product->discount;
                    $this->selectedProductVendor[$currProduct->owner->id] = $currProduct->owner->name;
                    $this->selectedProductId[$currProduct->owner->id][$product->prod_id] = $product->prod_id;
                }
            }
        }
    }

    public function render()
    {
        $offerType = $this->selectedType;
        if($offerType >= 3){
            if($offerType == 4){
                if(strlen($this->prod) >= 3){
                    $selectedVendor = $this->vendor_id;
                    if($this->searchAll){
                        $this->products = Product::where('name', 'like', '%'.$this->prod.'%')->take(7)->get();
                    } else {
                        $this->products = Product::where('name', 'like', '%'.$this->prod.'%')->where('vendor_id', '=', $selectedVendor)->take(7)->get();
                    }
                } else {
                    $this->products = [];
                }
            }  else {
                if(strlen($this->prod) >= 3){
                    $selectedVendor = $this->vendor_id;
                    if($selectedVendor){
                        $this->products = Product::where('name', 'like', '%'.$this->prod.'%')->where('vendor_id', '=', $selectedVendor)->take(7)->get();
                    } else {
                        $this->products = Product::where('name', 'like', '%'.$this->prod.'%')->take(7)->get();
                    }
                } else {
                    $this->products = [];
                }
            }
        }
        $this->emit('updateOffer', "1");
        return view('livewire.offer.create-offer');
    }
    public function updatedSelectedCategories()
    {
        // ray($this->selectedCategories);
    }

    public function selectType($type)
    {
        if(is_numeric($type)){
            $this->selectedType = $type;
            if($type == 1){
                $this->offerTitle = 'Krijo Ofertë për të gjitha produktet';
            } else if($type == 2){
                $this->offerTitle = 'Krijo Ofertë mbi kategorinë';
            } else if($type == 3){
                $this->offerTitle = 'Krijo Ofertë për disa produktet';
            } else if($type == 4){
                $this->offerTitle = 'Krijo Ofertë Speciale';
            }
            $this->showForm = true;
        }
    }

    public function addProduct($prodid)
    {
        if(is_numeric($prodid)){
            if($this->selectedType == 4){
                $currProduct = $this->products->where('id', '=', $prodid)->first();
                $this->selectedProduct[$currProduct->owner->id][$prodid] = $currProduct;
                $this->selectedProductId[$currProduct->owner->id][$prodid] = $prodid;
                $this->selectedProductVendor[$currProduct->owner->id] = $currProduct->owner->name;
                $this->prod = '';
            } else {
                $this->selectedProduct[$prodid] = $this->products->where('id', '=', $prodid)->first();
                $this->selectedProductId[$prodid] = $prodid;
                $this->prod = '';
            }
        }
    }

    public function addCategory($catId)
    {
        if(is_numeric($catId)){
            $currCat = $categories = Category::where('id', '=', $catId)->get();
            $this->selectedCategories[$catId] = $currCat;
            // $this->selectedProduct[$prodid] = $this->products->where('id', '=', $prodid)->first();
            // $this->selectedProductId[$prodid] = $prodid;
            // $this->prod = '';
        }
    }

    public function rmProduct($prodid, $vendid=0)
    {
        if(is_numeric($prodid)){
            if($this->selectedType == 4){
                if(auth()->user()->role == 2 && $this->editRole){
                } else {
                    $vendid = current_vendor()->id;
                }
                unset($this->selectedProduct[$vendid][$prodid]);
                unset($this->selectedProductId[$vendid][$prodid]);
            } else {
                unset($this->selectedProduct[$prodid]);
                unset($this->selectedProductId[$prodid]);
            }
        }
    }
}
