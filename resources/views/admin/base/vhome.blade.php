<x-admin-layout>
    @if(vendor_status())
    @php
        $vendorMembership = current_vendor()->amembership->first();
    @endphp
    <div class="row row-cards-one">
        @if(current_vendor()->unpaidInvoices->count())
            <div class="col-12">
                <div class="card">
                    <div class="card-body bg-danger">
                        <h6 class="title text-white">Ju keni fatura të pa paguara prej <b>{{current_vendor()->unpaidInvoices->sum('amount')}}€</b> tek Elefandi</h6>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-6 col-xl-3">
            <div class="card c-info-box-area">
                <div class="c-info-box box1">
                    <p>{{ $ordersToday }}</p>
                </div>
                <div class="c-info-box-content">
                    <h6 class="title">Shitje</h6>
                    <p class="text">Sot</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card c-info-box-area">
                <div class="c-info-box box2">
                    <p>{{ $ordersMonth }}</p>
                </div>
                <div class="c-info-box-content">
                    <h6 class="title">Shitje</h6>
                    <p class="text">Këtë muaj</p>
                </div>
            </div>
        </div>
        @if($vendorMembership && $vendorMembership->type == 1)
        <div class="col-md-6 col-xl-3">
            <div class="card c-info-box-area">
                <div class="c-info-box box3">
                    <p>{{ number_format($ordersTodaySum,2) }}€</p>
                </div>
                <div class="c-info-box-content">
                    <h6 class="title">Porosi</h6>
                    <p class="text">Sot</p>
                </div>
            </div>
        </div>
        @endif
        <div class="col-md-6 col-xl-3">
            <div class="card c-info-box-area">
                <div class="c-info-box box4">
                    <p>{{ number_format($ordersMonthSum,2) }}€</p>
                </div>
                <div class="c-info-box-content">
                    <h6 class="title">Porosi</h6>
                    <p class="text">Këtë muaj</p>
                </div>
            </div>
        </div>
        @if($vendorMembership && $vendorMembership->type == 2)
        <div class="col-md-6 col-xl-3">
            <div class="card c-info-box-area">
                <div class="c-info-box box3">
                    @if($vendorMembership->amount)
                        <p>{{ number_format((($ordersMonthSum * $vendorMembership->amount)/100),2) }}€</p>
                    @else
                        <p>{{ number_format((($ordersMonthSum * $vendorMembership->amount)/100),2) }}€</p>
                    @endif
                </div>
                <div class="c-info-box-content">
                    <h6 class="title">Komisione</h6>
                    <p class="text">Këtë muaj</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="row row-cards-one">
        <div class="col-md-12 col-lg-6 col-xl-4">
            <div class="mycard bg1">
                <div class="left">
                    <h5 class="title">Porosi në Pritje</h5>
                    <span class="number">{{ $pOrders }}</span>
                    <a href="{{ route('vendor.orders.pending') }}" class="link">Të Gjitha</a>
                </div>
                <div class="right d-flex align-self-center">
                    <div class="icon">
                        <i class="icofont-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6 col-xl-4">
            <div class="mycard bg6">
                <div class="left">
                    <h5 class="title">Porosi të Dërguara!</h5>
                    <span class="number">{{ $cOrders }}</span>
                    <a href="{{ route('vendor.orders.completed') }}" class="link">Të Gjitha</a>
                </div>
                <div class="right d-flex align-self-center">
                    <div class="icon">
                        <i class="icofont-truck-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6 col-xl-4">
            <div class="mycard bg5">
                <div class="left">
                    <h5 class="title">Porosi të Anulluara</h5>
                    <span class="number">{{ $caOrders }}</span>
                    <a href="{{ route('vendor.orders.canceled') }}" class="link">Të Gjitha</a>
                </div>
                <div class="right d-flex align-self-center">
                    <div class="icon">
                        <i class="icofont-check-circled"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="card">
            <div class="card-header"><h5>Statusi i dyqanit tuaj është Pezulluar</h5></div>
            <div class="card-body">
                <p>Antarsia e dyqanit tuaj nuk është paguar, ju lutem paguani faturat për të riaktivizuar dyqanin.</p>
                <p>Dyqani dhe produktet tuaja përkohësisht jane te fshehura nga Elefandi dhe do të ri shfaqen në momentin që do të konfirmohet pagesa nga administratorët</p>
                @if(current_vendor()->membership->count())
                    <div class="divider mt-4 mb-4"></div>
                    <div class="old-payment">
                        <h5>Pagesat e vjetra</h5>
                            @foreach(current_vendor()->membership as $membership)
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <div class="left">Nga Data: {{ \Carbon\Carbon::parse($membership->start_date)->format('d.m.Y H:i') }}</div>
                                        <div class="right">Deri më: {{ \Carbon\Carbon::parse($membership->end_date)->format('d.m.Y H:i') }}</div>
                                    </div>
                                    <div class="card-body">{{ $membership->description }}</div>
                                </div>
                            @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-admin-layout>