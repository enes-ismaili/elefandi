<x-app-layout>
    @push('scripts')
<style>
.text-info{
    line-height: 1;
    margin: 5px 0 20px;
}
.vendor-register {
    margin-top: -30px;
}
.vendor-register section {
    padding: 50px 0;
}
.header-baner {
    position: relative;
    display: inline-block;
    width: 100%;
}
.header-baner .baner-text {
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    transform: translate(0, -50%);
    color: #fff;
    text-align: center;
    font-size: 24px;
}
.vendor-register .subheader {
    margin-bottom: 30px;
    font-size: 16px;
    font-weight: 600;
    color: #666666;
    text-transform: uppercase;
    text-align: center;
}
.vendor-register .header {
    font-weight: 400;
    color: #000;
    font-size: 30px;
    text-align: center;
    padding-bottom: 90px;
}
.vendor-about .col-4 {
    text-align: center;
}
.vendor-about .icons-box {
    font-size: 80px;
    color: #fcb800;
}
.vendor-about .title {
    font-size: 18px;
    font-weight: 600;
}
.vendor-mileston {
    background-color: #f6f6f6;
}
.milestone-single {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: horizontal;
    -webkit-box-direction: normal;
    -ms-flex-flow: row nowrap;
    flex-flow: row nowrap;
    position: relative;
    padding-bottom: 90px;
}
.milestone-single.reverse {
    -webkit-box-orient: horizontal;
    -webkit-box-direction: reverse;
    -ms-flex-flow: row-reverse nowrap;
    flex-flow: row-reverse nowrap;
}
.milestone-single:before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    -webkit-transform: translateX(-50%);
    -moz-transform: translateX(-50%);
    -ms-transform: translateX(-50%);
    -o-transform: translateX(-50%);
    transform: translateX(-50%);
    height: 100%;
    width: 2px;
    background-color: #fcb800;
}
.milestone-single:last-child:before {
	display: none
}
.milestone-single .left {
    padding-right: 140px;
    width: 100%;
}
.milestone-single.reverse .left {
    padding-left: 140px;
    padding-right: 0;
}
.milestone-single .left h4 {
    margin-bottom: 30px;
    font-size: 22px;
    font-weight: 600;
    color: #000;
    line-height: 1.2em;
}
.milestone-single .left ul {
    padding-left: 20px;
}
.milestone-single .left ul li {
    margin-bottom: 20px;
    font-size: 16px;
    color: #666;
    list-style: disc;
    display: list-item;
}
.milestone-single .right {
    text-align: right;
    width: 100%;
}
.milestone-single.reverse .right {
    text-align: left;
}
.milestone-single .number {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    position: absolute;
    top: 0;
    left: 50%;
    -webkit-transform: translateX(-50%);
    -moz-transform: translateX(-50%);
    -ms-transform: translateX(-50%);
    -o-transform: translateX(-50%);
    transform: translateX(-50%);
    width: 100px;
    height: 100px;
    background-color: #ffffff;
    border: 2px solid #fcb800;
    border-radius: 50%;
}
.milestone-single .right img {
    display: inline-block;
}
section.vendor-fees {
    text-align: center;
}
.vendor-fees .content {
    max-width: 730px;
    margin: 0 auto;
    text-align: center;
}
.fee-number {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    margin-bottom: 60px;
}
.fee-number .box-round {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-flow: column wrap;
    flex-flow: column wrap;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    width: 170px;
    height: 170px;
    border: 2px solid #fcb800;
    margin: 0 40px;
    border-radius: 50%;
}
.fee-number .box-round h3 {
    display: block;
    margin-bottom: 0;
    width: 100%;
    font-size: 42px;
    font-weight: 400;
    line-height: 1;
    color: #000;
}
.fee-desc {
    max-width: 550px;
    margin: 0 auto 30px;
}
.fee-desc ul li {
    display: list-item;
    list-style: disc;
    text-align: left;
    color: #666;
    font-size: 16px;
    margin-bottom: 16px;
    line-height: 1.6em;
}
.fee-highlight {
    margin-bottom: 40px;
    padding: 30px 20px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: horizontal;
    -webkit-box-direction: normal;
    -ms-flex-flow: row nowrap;
    flex-flow: row nowrap;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    background-color: #f6f6f6;
}
.fee-highlight .right {
    text-align: justify;
    padding-left: 60px;
    padding-right: 60px;
}
.contact-us .vendor-contact {
    margin-bottom: 30px;
    font-size: 25px;
}
.vendor-request {
    font-size: 19px;
    background: #eee;
    padding: 5px 10px;
    border-radius: 5px;
}
ul.vendor-fee-desc {
    padding-left: 20px;
    margin-top: 10px;
    font-size: 13px;
}
ul.vendor-fee-desc li {
    display: list-item;
    list-style: disc;
    line-height: 1.5;
}
@media (max-width: 999px) {
    .header-baner .baner-text {
        font-size: 18px;
    }
    .vendor-register .header {
        font-size: 22px;
    }
}
@media (max-width: 768px) {
    .header-baner {
        height: 350px;
    }
    .header-baner .baner-header {
        height: 100%;
        max-width: unset;
        object-fit: contain;
    }
    .header-baner .baner-text {
        font-size: 16px;
    }
    .vendor-register .header {
        font-size: 19px;
    }
    .milestone-single, .milestone-single.reverse {
        flex-flow: column-reverse;
    }
    .milestone-single:before {
        left: 35px;
    }
    .milestone-single .left {
        padding-right: 0;
        padding-left: 70px;
    }
    .milestone-single.reverse .left {
        padding-left: 70px;
    }
    .milestone-single .right {
        padding-right: 0;
        padding-left: 70px;
        text-align: center;
    }
    .milestone-single.reverse .right {
        text-align: center;
    }
    .milestone-single .number {
        width: 70px;
        height: 70px;
        left: 0;
        transform: translateX(0);
    }
    .fee-number .box-round {
        margin: 0 20px;
    }
    .fee-highlight .left {
        width: 250px;
    }
    .fee-highlight .right {
        padding-left: 20px;
        padding-right: 0px;
    }
    .fee-desc ul li {
        list-style: inside disc;
    }
}
@media (max-width: 450px) {
    .header-baner .baner-text {
        font-size: 15px;
    }
    .vendor-register .header {
        font-size: 17px;
    }
    .vendor-about .row .col-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .milestone-single:before {
        left: 20px;
    }
    .milestone-single .left {
        padding-right: 0;
        padding-left: 50px;
    }
    .milestone-single.reverse .left {
        padding-left: 50px;
    }
    .milestone-single .right {
        padding-left: 50px;
    }
    .milestone-single .number {
        width: 50px;
        height: 50px;
    }
    .fee-number .box-round {
        margin: 0 5px;
        width: 120px;
        height: 120px;
    }
    .fee-number .box-round h3 {
        font-size: 34px;
    }
    #register-vendor .row .col-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', 'Regjistrohu si Biznes')
    <div class="vendor-register">
        <div class="header-baner">
            <img src="https://elefandi.com/img/bg/vendor.jpg" alt="" class="baner-header">
            <div class="baner-text">
                <div class="container">
                    <h2>Pasi dyqani juaj fizik është i mbyllur atëherë keni mundësinë të krijoni dyqanin tuaj online.</h2>
                    <div class="row">
                        <div class="button mx-auto mt-20">
                            <a href="#register-vendor" class="btn btn-primary">Filloni shitjen online</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <section class="vendor-about">
                <div class="subheader">PSE TË SHISHNI Në ELEFANDI.COM?</div>
                <div class="header">Elefandi është platforma më e avancuar për shitje online ku ju mund të krijoni profilin e dyqanit tuaj dhe të lidheni direkt me klientët përmes shitjes online.</div>
                <div class="content">
                    <div class="row">
                        <div class="col-4">
                            <div class="icons-box">
                                <i class="icon-money-bill-wave-alt"></i>
                            </div>
                            <div class="content">
                                <h4 class="title">Kosto e ulët</h4>
                                <div class="text-box">
                                    Regjistrimi është falas, krejt cka duhet është të plotësoni të dhënat tuaja dhe të biznesit tuaj, të beni listen e artikujve tuaj dhe të filloni shitjen sa më afër klientëve.
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="icons-box">
                                <i class="icon-cogs"></i>
                            </div>
                            <div class="content">
                                <h4 class="title">Kosto e ulët</h4>
                                <div class="text-box">
                                    Regjistrimi është falas, krejt cka duhet është të plotësoni të dhënat tuaja dhe të biznesit tuaj, të beni listen e artikujve tuaj dhe të filloni shitjen sa më afër klientëve.
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="icons-box">
                                <i class="icon-headset"></i>
                            </div>
                            <div class="content">
                                <h4 class="title">Kosto e ulët</h4>
                                <div class="text-box">
                                    Regjistrimi është falas, krejt cka duhet është të plotësoni të dhënat tuaja dhe të biznesit tuaj, të beni listen e artikujve tuaj dhe të filloni shitjen sa më afër klientëve.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <section class="vendor-mileston">
            <div class="container">
                <div class="subheader">SI FUNKSIONON?</div>
                <div class="header">është shumë e lehtë të shisni në Elefandi, vetëm në 4 hapa të thjeshtë.</div>
                <div class="content">
                    <div class="milestone-single">
                        <div class="left">
                            <h4>Regjistrohu dhe krijo profilin tuaj</h4>
                            <ul>
                                <li>Regjistroni biznesin tuaj, rregulloni profilin tuaj të biznesit dhe filloni listimin e produkteve.</li>
                                <li>Këshilltarët tanë në Elefandi do t'ju ndihmojnë në cdo hap dhe do të ju ndihmojnë plotësisht në krijimin e biznesit tuaj në internet.</li>
                            </ul>
                        </div>
                        <div class="right">
                            <img src="https://elefandi.com/img/vendor/milestone-1.png" alt="">
                        </div>
                        <div class="number">1</div>
                    </div>
                    <div class="milestone-single reverse">
                        <div class="left">
                            <h4>Pranoni dhe paketoni porositë</h4>
                            <ul>
                                <li>Klientet do të shikojnë produktet tuaja, do të kenë mundësi të bisedojnë me ju në live chat dhe të bëjnë porosi të produkteve tuaja.</li>
                                <li>Ju do të pranoni porosinë, të paketoni produktet dhe të dërgoni ato produkte tek klientët tuaj.</li>
                            </ul>
                        </div>
                        <div class="right">
                            <img src="https://elefandi.com/img/vendor/milestone-2.png" alt="">
                        </div>
                        <div class="number">2</div>
                    </div>
                    <div class="milestone-single">
                        <div class="left">
                            <h4>Regjistroni produktet dhe pranoni porositë</h4>
                            <ul>
                                <li>Mënyrë shumë e lehtë për regjistrimin e produkteve, ndarjen në kategori, informacione rreth transportit, etj.</li>
                                <li>Përdorni etiketime të sakta dhe shkruani përshkrimin e qartë rreth produktit që të jetë me i lehtë për klientët.</li>
                            </ul>
                        </div>
                        <div class="right">
                            <img src="https://elefandi.com/img/vendor/milestone-3.png" alt="">
                        </div>
                        <div class="number">3</div>
                    </div>
                    <div class="milestone-single reverse">
                        <div class="left">
                            <h4>Pranoni pagesa dhe rrisni biznesin tuaj</h4>
                            <ul>
                                <li>Ju do të pranoni pagesat nga shitjet online përmes Elefandi dhe do të rrisni biznesin tuaj.</li>
                                <li>Biznesi juaj do të jetë prezent në cdo shtepi të cdo klienti.</li>
                            </ul>
                        </div>
                        <div class="right">
                            <img src="https://elefandi.com/img/vendor/milestone-4.png" alt="">
                        </div>
                        <div class="number">4</div>
                    </div>
                </div>
            </div>
        </section>
        <div class="container">
            <section class="vendor-fees">
                <div class="subheader">OFERTA MË E LEHTË PËR TË FILLUAR</div>
                <div class="header">E lehtë, transparente dhe e sigurt</div>
                <div class="content">
                    <p>Regjistrohuni tani, listoni produktet dhe filloni shitjen online, thjeshtë, lehtë dhe sigurtë.</p>
                    <div class="fee-number">
                        <div class="box-round">
                            <h3>0 €</h3>
                            <div>2 Muaj falas</div>
                        </div>
                        <div class="box-round">
                            <h3>9.50 €</h3>
                            <div>/ Në muaj</div>
                        </div>
                    </div>
                    <div class="fee-desc">
                        <div class="subheader">OFERTA MË E LEHTË PËR TË FILLUAR</div>
                        <ul>
                            <li>2 Muaj FALAS, zbritje nga 19.50€ në 9.50€ për muaj në 12 muajt të ardhshëm.</li>
                            <li>Mundësi reklamimi në storje 24h, oferta për reklamim në slider.</li>
                            <li>Shitje pa limit me mundësi shitje për Kosovë, Shqipëri dhe Maqedoni.</li>
                        </ul>
                    </div>
                    <div class="fee-highlight">
                        <div class="left"><img src="https://elefandi.com/img/icons/vendor-4.png" alt=""></div>
                        <div class="right">Ju nuk do të paguani asgje ekstra për sa shumë do të shisni në Elefandi, ju nuk keni një limit, gjithcka cka 
                            duhet të bëni është të listoni produktet, të pranoni porosi, të dërgoni te klientët dhe të pranoni pagesa.</div>
                    </div>
                    <div class="fee-footer">Me këtë cmim ju përfitoni një dyqan profesional për shitje online.</div>
                </div>
            </section>
        </div>
        <div class="divide"></div>
        <div class="container" id="register-vendor">
            <div class="contact-us tcenter">
                <div class="vendor-contact">Ende keni më shumë pyetje? Mos ngurroni të na kontaktoni.</div>
                <div class="row">
                    <div class="col-12">
                        <a href="#" class="btn btn-warning">Na Kontaktoni</a>
                    </div>
                </div>
            </div>
            <h1 class="tcenter mt-50">ose Regjistro Dyqanin tuaj tani</h1>
            @if(current_user() && current_user()->vendorRequest && current_user()->vendorRequest->where('status', '=', 1)->count())
                <div class="vendor-request tcenter">
                    <p>Ju keni bërë një kërkesë për hapje dyqani me emër <b>{{ current_user()->vendorRequest->name }}</b> e cila është në pritje të konfirimit nga administratorët</p>
                </div>
            @else
            <form action="{{ route('home.vendor.register') }}" method="POST" class="">
                @csrf
                <div class="card mt-30">
                    <div class="card-header">
                        <h5>Të dhënat e dyqanit</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(!current_user())
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="fname">Emri juaj *</label>
                                    <input type="text" name="fname" id="fname" class="form-control" placeholder="Emri Juaj" value="{{old('fname')}}" required>
                                    @error('fname') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="lname">Mbiemri juaj *</label>
                                    <input type="text" name="lname" id="lname" class="form-control" placeholder="Mbiemri Juaj" value="{{old('lname')}}" required>
                                    @error('lname') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="password">Fjalkalimi *</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Fjalkalimi" required>
                                    @error('password') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="password_confirm">Konfirmo Fjalkalimin *</label>
                                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Konfirmo Fjalkalimin"  required>
                                    @error('password_confirm') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            @endif
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Emri i Dyqanit *</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Emri i Dyqanit" value="{{old('name')}}" required>
                                    @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="{{old('email')}}" required>
                                    @error('email') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="phone">Numri i telefonit *</label>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Numri i telefonit" value="{{old('phone')}}" required>
                                    @error('phone') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            @livewire('select-countries', ['selCountry'=>(old('country') ? old('country') : 0), 'selCity'=> (old('city') ? old('city') : '')])
                            @error('country') <span class="text-danger error">{{ $message }}</span>@enderror
                            @error('city') <span class="text-danger error">{{ $message }}</span>@enderror
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="address">Adresa *</label>
                                    <input type="text" name="address" id="address" class="form-control" placeholder="Adresa" value="{{old('address')}}" required>
                                    @error('address') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Përshkrimi i dyqanit</label>
                                    <textarea name="description" id="description"  class="form-control">{{ old('description') }}</textarea>
                                    @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="vendortype"></label>
                                    <select name="vendortype" id="vendortype" class="form-control">
                                        <option value="1" selected>Pagesa mujore</option>
                                        <option value="2">Përqindje</option>
                                    </select>
                                    <ul class="vendor-fee-desc">
                                        <li>Pagesa Mujore janë pagesa fikse që do të pagush cdo muaj për shitje produktesh pa limit në Elefandi</li>
                                        <li>Pagesa me Përqindje ku ju do të paguani një përqindje fikse të shitjeve tuaj mujore në Elefandi</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="message">Informacion drejtuar Administratorit</label>
                                    <textarea name="message" id="message"  class="form-control">{{ old('message') }}</textarea>
                                    @error('message') <span class="text-danger error">{{ $message }}</span>@enderror
                                    <p class="text-info small">Jepni informacion rreth dyqanit tuaj, produktet qe do të shisni, etj</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn text-white btn-success fullwidth" wire:click.prevent="registerStore">Regjistro Dyqanin</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>
</x-app-layout>