<x-admin-layout>
    <div class="row row-cards-one">
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
        <div class="col-md-6 col-xl-3">
            <div class="card c-info-box-area">
                <div class="c-info-box box4">
                    <p>{{ $userNewMonth }}</p>
                </div>
                <div class="c-info-box-content">
                    <h6 class="title">Përdorues të rinj</h6>
                    <p class="text">Këtë Muaj</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards-one">
        <div class="col-md-12 col-lg-6 col-xl-4">
            <div class="mycard bg1">
                <div class="left">
                    <h5 class="title">Porosi në Pritje</h5>
                    <span class="number">{{ $pOrders }}</span>
                    <a href="{{ route('admin.orders.pending') }}" class="link">Të Gjitha</a>
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
                    <a href="{{ route('admin.orders.completed') }}" class="link">Të Gjitha</a>
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
                    <a href="{{ route('admin.orders.canceled') }}" class="link">Të Gjitha</a>
                </div>
                <div class="right d-flex align-self-center">
                    <div class="icon">
                        <i class="icofont-check-circled"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6 col-xl-4">
            <div class="mycard bg4">
                <div class="left">
                    <h5 class="title">Nr Dyqaneve</h5>
                    <span class="number">{{ $vendorC }}</span>
                    <a href="{{ route('admin.vendors.index') }}" class="link">Të Gjitha</a>
                </div>
                <div class="right d-flex align-self-center">
                    <div class="icon">
                        <i class="icofont-cart-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6 col-xl-4">
            <div class="mycard bg2">
                <div class="left">
                    <h5 class="title">Nr Produkteve</h5>
                    <span class="number">{{ $productsC }}</span>
                    <a href="{{ route('admin.products.show') }}" class="link">Të Gjitha</a>
                </div>
                <div class="right d-flex align-self-center">
                    <div class="icon">
                        <i class="icofont-users-alt-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6 col-xl-4">
            <div class="mycard bg3">
                <div class="left">
                    <h5 class="title">Nr Përdoruesve</h5>
                    <span class="number">{{ $userC }}</span>
                    <a href="{{ route('admin.users.index') }}" class="link">Të Gjitha</a>
                </div>
                <div class="right d-flex align-self-center">
                    <div class="icon">
                        <i class="icofont-newspaper"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>