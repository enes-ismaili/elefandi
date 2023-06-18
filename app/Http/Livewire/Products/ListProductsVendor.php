<?php

namespace App\Http\Livewire\Products;

use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class ListProductsVendor extends Component
{
    use WithPagination;
    protected $paginationTheme = 'paginate';

    public $orderFilter = 1; 
    public $listView = 1;
    protected $vendor;
    public $vendorid;

    public function render()
    {
        $products = [];
        if($this->vendorid){
            $this->vendor = Vendor::find($this->vendorid);
            if($this->orderFilter == 1){
                $products = $this->vendor->products()->where('status', '=', 1)->orderByDesc('updated_at')->paginate(16);
            } elseif($this->orderFilter == 2){
                $products = $this->vendor->products()->where('status', '=', 1)->orderByDesc('updated_at')->paginate(16);
            } elseif($this->orderFilter == 3){
                $products = $this->vendor->products()->where('status', '=', 1)->orderByDesc('price')->paginate(16);
            } elseif($this->orderFilter == 4){
                $products = $this->vendor->products()->where('status', '=', 1)->orderBy('price')->paginate(16);
            }
        }
        return view('livewire.products.list-products-vendor', [
            'products' => $products,
        ]);
    }

    public function changeView($view=1)
    {
        if($view == 2){
            $this->listView = 2;
        } else {
            $this->listView = 1;
        }
    }
}
