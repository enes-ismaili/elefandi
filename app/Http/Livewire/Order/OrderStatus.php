<?php

namespace App\Http\Livewire\Order;

use Livewire\Component;
use App\Models\OrderVendor;

class OrderStatus extends Component
{
    public $orderStatus;
    public $oid;
    protected $order;

    public function mount()
    {
        $oId = $this->oid;
        $this->order = OrderVendor::where('id', '=', $oId)->first();
    }

    public function render()
    {
        return view('livewire.order.order-status');
    }

    public function updatedOrderStatus()
    {
        $oId = $this->oid;
        $order = OrderVendor::find($oId);
        $order->status = $this->orderStatus;
        $order->save();
        if($order->order->ordervendor()->where('status', '=', 0)->count() == 0){
            if($this->orderStatus == 1){
                if($order->order->ordervendor()->where('status', '=', 2)->count()){
                    $order->order->status = 3;
                    $order->order->save();
                } else {
                    $order->order->status = 1;
                    $order->order->save();
                }
            } else {
                if($order->order->ordervendor()->where('status', '=', 1)->count()){
                    $order->order->status = 3;
                    $order->order->save();
                } else {
                    $order->order->status = 2;
                    $order->order->save();
                }
            }
        } else {
            $order->order->status = 0;
            $order->order->save();
        }
        $orderStatuss = 'Duke Procesuar';
        if($this->orderStatus == 1){
            $orderStatuss = 'Dërguar';
        } elseif($this->orderStatus == 2){
            $orderStatuss = 'Anulluar';
        }
        $this->emit('statusChange', $orderStatuss);
    }
}
