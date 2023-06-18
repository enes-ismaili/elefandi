<?php

namespace App\Http\Livewire\Products;

use App\Models\Country;
use Livewire\Component;
use App\Models\WishList;
use App\Models\ShoppingCart;
use App\Models\TransportInfo;

class ProductPrice extends Component
{
    protected $listeners = [
        'updateCart',
        'updatedCart',
        'removeCarts'
    ];
    public $product;
    public $minStock = 1;
    public $selectStock = 1;
    public $maxStock = 1;
    public $shippingCountry;
    public $productShippings;
    public $productPrice;
    public $productColors;
    public $selectedColors;
    public $productAttributes;
    public $selectedAttributes;
    public $allVariants;
    public $currentVariants;
    protected $selectedVariant;
    public $productVariant;
    protected $selectedAttributesSlug;
    public $selectedAttributesSlugs;
    protected $selectedAttributesOption;
    public $noVariant = false;
    public $isLogged = false;
    public $inCart = false;
    public $inWish = false;
	public $buttonsError = '';
    public $personalizeProduct = '';

    public function mount($product)
    {
        if($product){
            if(current_user()){
                $this->isLogged = true;
            }
            $shippingCountry = Country::where('shipping', '1')->get();
            $this->shippingCountry = $shippingCountry;
            $productShippings = '';
            $ii = 0;
            foreach($shippingCountry as $country){
                $ii++;
                $getShipping = $product->shippings->where('country_id', '=', $country->id)->first();
                if(!$product->shipping && $getShipping){
                    $transportInfo = TransportInfo::where('id', '=', $getShipping->shipping_time)->first();
                    $productShippings .= '"c'.$country->id.'":{"id":"'.$getShipping->id.'","shipping":"'.$getShipping->shipping.'","free":"'.$getShipping->free.'","cost":"'.$getShipping->cost.'","shipping_time":"'.$getShipping->shipping_time.'", "timeName":"'.$transportInfo->name.'"}';
                } else {
                    $getShippingVendor = $product->owner->shippings()->where('country_id', '=', $country->id)->first();
                    if($getShippingVendor){
                        $transportInfo = TransportInfo::where('id', '=', $getShippingVendor->transtime)->first();
                        $productShippings .= '"c'.$country->id.'":{"id":"'.$getShippingVendor->id.'","shipping":"'.$getShippingVendor->limit.'","free":"0","cost":"'.$getShippingVendor->cost.'","shipping_time":"'.$getShippingVendor->transtime.'", "timeName":"'.(($transportInfo) ? $transportInfo->name : 'Ska Transport' ).'"}';
                    }
                }
                if($ii < 3){
                    $productShippings .= ',';
                }
            }
            $this->productShippings = $productShippings;
            if(($product->colors && $product->colors != '[]') || ($product->attributes && $product->attributes != '[]')){
                $this->allVariants = $product->variants;
                $this->selectedVariant = $this->allVariants[0];
                $this->productVariant = $this->selectedVariant->id;
                $this->selectedAttributesSlug = $this->selectedVariant->slug;
                $this->selectedAttributesSlugs = $this->selectedVariant->slug;
                $explodeVariant = explode('-', $this->selectedVariant->slug);
                if(is_numeric($explodeVariant[0])){
                    $this->selectedColors = $explodeVariant[0];
                    if($product->attributes){
                        $this->productAttributes = json_decode($product->attributes);
                        $i=1;
                        foreach($this->productAttributes as $attribute){
                            $this->selectedAttributes[$attribute->id] = $explodeVariant[$i];
                            $i++;
                        }
                    }
                } else {
                    if($product->attributes){
                        $this->productAttributes = json_decode($product->attributes);
                        $i=0;
                        foreach($this->productAttributes as $attribute){
                            $this->selectedAttributes[$attribute->id] = $explodeVariant[$i];
                            $i++;
                        }
                    }
                }
            }
            $currentVariant = $this->product->variants()->where('slug', '=', $this->selectedAttributesSlugs)->first();
            if($currentVariant){
                $this->currentVariants = $currentVariant->id;
            } else {
                $this->currentVariants = 0;
            }
            if(current_user()){
                if($currentVariant){
                    $this->inCart = $this->product->cart()->where('variant_id', '=', $currentVariant->id)->where('user_id', '=', current_user()->id)->first();
                    $this->inWish = $this->product->wishlist()->where('variant_id', '=', $currentVariant->id)->where('user_id', '=', current_user()->id)->first();
                } else {
                    $this->inCart = $this->product->cart()->where('variant_id', '=', 0)->where('user_id', '=', current_user()->id)->first();
                    $this->inWish = $this->product->wishlist()->where('variant_id', '=', 0)->where('user_id', '=', current_user()->id)->first();
                }
            }

            if($this->selectedVariant && ($this->selectedVariant->slug == $this->selectedAttributesSlug)){
                $this->productVariant = $this->selectedVariant ? $this->selectedVariant->id : 0;
                $this->productPrice = $this->selectedVariant ? (($this->selectedVariant->price) ? $this->selectedVariant->price : $product->price) : $product->price;
                $this->maxStock = $this->selectedVariant ? (($this->selectedVariant->stock) ? $this->selectedVariant->stock : $product->stock) : $product->stock;
            } else {
                $this->productPrice = $product->price;
                $this->maxStock = $product->stock;
            }
			if($this->maxStock < $this->selectStock){
				$this->selectStock = ($this->maxStock < 1)?0:$this->maxStock;
			}
        }
    }

    public function render()
    {
        return view('livewire.products.product-price');
    }

    public function updateCart()
    {
        // ray('updatse');
        // $this->emit('updateCarts', "Updated Salary.");
    }

    public function updatedCart()
    {
        $this->emit('updateCarts', "1");
    }

    public function removeCarts()
    {
        $currentVariant = $this->product->variants()->where('slug', '=', $this->selectedAttributesSlugs)->first();
        if($currentVariant){
            $variantId = $currentVariant->id;
        } else {
            $variantId = 0;
        }
        if(current_user()) {
            $isCart = ShoppingCart::where('product_id', '=', $this->product->id)->where('variant_id', '=', $variantId)->where('user_id', '=', current_user()->id)->first();
            if(!$isCart){
                $this->inCart = false;
            }
            $this->emit('updateCarts', "1");
        }
    }

    public function changevariant($attribute, $variant)
    {
        if($attribute == 'color'){
            $this->selectedColors = $variant;
            // $this->selectedAttributesSlug = $this->selectedAttributesOption['color'][$variant]['id'];
            // if($this->selectedAttributesOption['attribute']){
            //     foreach($this->selectedAttributesOption['attribute'] as $attribute){
            //         if($this->selectedAttributesSlug){
            //             $this->selectedAttributesSlug .= '-'.$attribute->options[0];
            //         } else {
            //             $this->selectedAttributesSlug .= $attribute->options[0];
            //         }
            //     }
            // }
        } else {
            $this->selectedAttributes[$attribute] = $variant;
        }
        if($this->product->colors && $this->product->colors != '[]'){
            $selectedSlug = $this->selectedColors;
        }
        if($this->selectedAttributes){
            foreach($this->selectedAttributes as $attribute){
                if($selectedSlug){
                    $selectedSlug .= '-'.$attribute;
                } else {
                    $selectedSlug .= $attribute;
                }
            }
        }
        $this->selectedAttributesSlugs = $selectedSlug;
        $currentVariant = $this->product->variants()->where('slug', '=', $selectedSlug)->first();
        if($currentVariant){
            $this->productVariant = $currentVariant->id;
            $this->noVariant = false;
            $this->currentVariants = $currentVariant->id;
        } else {
            $this->productVariant = 0;
            $this->noVariant = true;
            $this->currentVariants = 0;
        }
        if($currentVariant){
            $this->productPrice = $currentVariant ? (($currentVariant->price) ? $currentVariant->price : $this->product->price) : $this->product->price;
            $this->maxStock = $currentVariant ? (($currentVariant->stock && $currentVariant->stock > 0) ? $currentVariant->stock : $this->product->stock) : $this->product->stock;
        } else {
            $this->productPrice = $this->product->price;
            $this->maxStock = $this->product->stock;
        }
        if($this->selectStock > $this->maxStock){
            $this->selectStock = $this->maxStock;
        }

        if(current_user()){
            if($currentVariant){
                $this->inCart = $this->product->cart()->where('variant_id', '=', $currentVariant->id)->where('user_id', '=', current_user()->id)->first();
                $this->inWish = $this->product->wishlist()->where('variant_id', '=', $currentVariant->id)->where('user_id', '=', current_user()->id)->first();
            } else {
                $this->inCart = $this->product->cart()->where('variant_id', '=', 0)->where('user_id', '=', current_user()->id)->first();
                $this->inWish = $this->product->wishlist()->where('variant_id', '=', 0)->where('user_id', '=', current_user()->id)->first();
            }
        } else {
            if($currentVariant){
                $this->currentVariants = $currentVariant->id;
            } else {
                $this->currentVariants = 0;
            }
        }
        $this->emit('changeVariant', "1");
    }

    public function addToCart()
    {
		if($this->selectStock && $this->selectStock > 0){
            $currentVariant = $this->product->variants()->where('slug', '=', $this->selectedAttributesSlugs)->first();
            if($currentVariant){
                $variantId = $currentVariant->id;
            } else {
                $variantId = 0;
            }
            if(current_user()) {
                ShoppingCart::updateOrCreate(
                    ['product_id' => $this->product->id, 'variant_id' => $variantId, 'user_id'=> current_user()->id],
                    ['qty' => $this->selectStock, 'personalize'=> $this->personalizeProduct]
                );
                $this->inCart = true;
                $this->emitTo('header.mini-cart', '$refresh');
                $this->emit('updateCarts', "1");
            }
        } else {
            if($this->maxStock < 1){
                $this->buttonsError = 'Ky produkt nuk mund të shtohet në shport pasi nuk ka stok';
            } else {
                $this->buttonsError = 'Ky produkt nuk mund të shtohet në shport pasi nuk keni zgjedhur Sasinë';
            }
		}
		 $this->emit("updateWish", "1");
    }
    public function addToWishlist($action)
    {
        $currentVariant = $this->product->variants()->where('slug', '=', $this->selectedAttributesSlugs)->first();
        if($currentVariant){
            $variantId = $currentVariant->id;
        } else {
            $variantId = 0;
        }
        if($action == 1){
            if(current_user()) {
                WishList::updateOrCreate(
                    ['product_id' => $this->product->id, 'variant_id' => $variantId, 'user_id'=> current_user()->id],
                );
                $this->inWish = true;
            }
        } else {
            $thisWishlistP = $this->product->wishlist()->where('variant_id', '=', $variantId)->where('user_id', '=', current_user()->id)->first();
            if($thisWishlistP){
                $thisWishlistP->delete();
            }
            $this->inWish = false;
        }
        if($this->isLogged){
            $this->emitTo('header.wishlist', 'getWishLUpdate');
        }
        $this->emit("updateWish", "1");
    }

    public function openCheckout()
    {
		if($this->selectStock && $this->selectStock > 0){
			$currentVariant = $this->product->variants()->where('slug', '=', $this->selectedAttributesSlugs)->first();
			if($currentVariant){
				$variantId = $currentVariant->id;
				$variantName = $currentVariant->name;
			} else {
				$variantId = 0;
				$variantName = '';
			}
			if($this->product->offers($variantId) && $this->product->offers($variantId)->discount != 0){
				$offer = $this->product->offers($variantId);
					if($offer->type < 3){
						$offerPrice = round($this->productPrice - (($this->productPrice * $offer->discount)/100), 2);
					} else {
						$offerPrice = $offer->discount;
					}
			} else {
				$offerPrice = $this->productPrice;
			}
			$this->emitTo('products.buy-directly', 'openCheckout', $this->selectStock, $variantId, $variantName, $offerPrice);
		} else {
            if($this->maxStock < 1){
                $this->buttonsError = 'Ky produkt nuk mund të blihet me një klik pasi nuk ka stok';
            } else {
                $this->buttonsError = 'Ky produkt nuk mund të blihet me një klik pasi nuk keni zgjedhur Sasinë';
            }
		}
		 $this->emit("updateWish", "1");
    }
}
