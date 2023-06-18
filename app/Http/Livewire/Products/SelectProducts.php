<?php

namespace App\Http\Livewire\Products;

use App\Models\Vendor;
use App\Models\Product;
use Livewire\Component;

class SelectProducts extends Component
{

    public $vendor_id;
    public $product_id;
    public $vendors;
    public $products;
    public $vend;
    public $prod;
    public $selectedVendor;
    public $selectedProduct;
    public $selectedVendorId;
    public $selectedProductId;
    public $showVendor = true;

    protected $queryString = [
        'vend' => ['except' => ''],
        'prod' => ['except' => '']
    ];

    public function mount()
    {
        $this->vendors = [];
        $this->products = [];
        $this->vend = '';
        $this->prod = '';
        $this->selectedVendor = [];
        $this->selectedProduct = [];
        $this->selectedVendorId = [];
        $this->selectedProductId = [];
        if($this->vendor_id){
            $vendorsId = $this->vendor_id;
            $this->selectedVendorId = $vendorsId;
            $selVendors = Vendor::whereIn('id', $vendorsId)->get();
            foreach($selVendors as $selVendor){
                $this->selectedVendor[$selVendor->id] = $selVendor;
            }
        }
        if($this->product_id){
            $productId = $this->product_id;
            $this->selectedProductId = $productId;
            $selProducts = Product::whereIn('id', $productId)->get();
            foreach($selProducts as $selProduct){
                $this->selectedProduct[$selProduct->id] = $selProduct;
            }
            // $this->selectedProduct = Product::whereIn('id', $productId)->get();
        }
    }

    public function render()
    {
        if(strlen($this->vend) >= 3){
            $this->vendors = Vendor::where('name', 'like', '%'.$this->vend.'%')->take(7)->get();
        } else {
            $this->vendors = [];
        }
        if(strlen($this->prod) >= 3){
            $selectedVendor = $this->selectedVendorId;
            if($selectedVendor){
                $this->products = Product::where('name', 'like', '%'.$this->prod.'%')->whereIn('vendor_id', $selectedVendor)->take(7)->get();
            } else {
                $this->products = Product::where('name', 'like', '%'.$this->prod.'%')->take(7)->get();
            }
        } else {
            $this->products = [];
        }
        return view('livewire.products.select-products');
    }

    public function addVendor($vendorid)
    {
        if(is_numeric($vendorid)){
            $this->selectedVendor[$vendorid] = $this->vendors->where('id', '=', $vendorid)->first();
            $this->selectedVendorId[$vendorid] = $vendorid;
            $this->vend = '';
        }
    }

    public function rmVendor($vendorid)
    {
        if(is_numeric($vendorid)){
            unset($this->selectedVendor[$vendorid]);
            unset($this->selectedVendorId[$vendorid]);
        }
    }

    public function addProduct($prodid)
    {
        if(is_numeric($prodid)){
            $this->selectedProduct[$prodid] = $this->products->where('id', '=', $prodid)->first();
            $this->selectedProductId[$prodid] = $prodid;
            $this->prod = '';
        }
    }

    public function rmProduct($prodid)
    {
        if(is_numeric($prodid)){
            unset($this->selectedProduct[$prodid]);
            unset($this->selectedProductId[$prodid]);
        }
    }
}
