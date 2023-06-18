<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
<style>
.contact-us .base-info .row a {
    display: inline-block;
}
.contact-us p.page-information {
    display: block;
    background: #32bb75;
    color: #fff;
    font-size: 15px;
    font-weight: 500;
    padding: 5px;
    border-radius: 5px;
}
</style>
    @endpush
    @section('pageTitle', 'Kontaktoni me ne')
    <div class="container">
        <div class="contact-us">
            @if($submitedC)
                <p class="page-information tcenter">Mesazhi u dërgua me sukses</p>
            @endif
            <div class="base-info">
                <h1 class="tcenter"><b>Kontaktoni me ne</b></h1>
                {!! $contactPage->description !!}
                {{-- <div class="row">
                    <div class="col-4 col-sm-12 tcenter">
                        <h2>Gjenerale</h2>
                        <p><a href="mailto:info@elefandi.com">info@elefandi.com</a></p>
                        <p><a href="tel:+38344120220">+383 (0) 44 120 220</a></p>
                    </div>
                    <div class="col-4 col-sm-12 tcenter">
                        <h2>Adresa</h2>
                        <p>Rr Skenderbeu 14, Prishtine</p>
                    </div>
                    <div class="col-4 col-sm-12 tcenter">
                        <h2>Shitja</h2>
                        <p>Dërgoni email në:</p>
                        <p><a href="mailto:shitja@elefandi.com">shitja@elefandi.com</a></p>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-4 col-sm-12 tcenter">
                        <h2>Shërbimi për konsumator</h2>
                        <p><a href="mailto:info@elefandi.com">support@elefandi.com</a></p>
                        <p><a href="tel:+38344300320">+383 (0) 44 300 320</a></p>
                    </div>
                    <div class="col-4 col-sm-12 tcenter">
                        <h2>Media</h2>
                        <p><a href="mailto:info@elefandi.com">media@elefandi.com</a></p>
                        <p><a href="tel:+38344300320">+383 (0) 44 300 320</a></p>
                    </div>
                    <div class="col-4 col-sm-12 tcenter">
                        <h2>Vendor Support</h2>
                        <p><a href="mailto:info@elefandi.com">info@elefandi.com</a></p>
                        <p><a href="tel:+38344120220">+383 (0) 44 120 220</a></p>
                    </div>
                </div> --}}
            </div>
            <form class="card contact-form mt-50" action="{{ route('submit.contact') }}" method="POST">
                @csrf
                <div class="card-header">
                    <h5>Na Kontaktoni</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 col-sm-12">
                            <div class="form-group">
                                <label for="name">Emri *</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Emri" value="{{old('name')}}" required>
                                @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-6 col-sm-12">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
                                @error('email') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="subject">Subjekti *</label>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder="Subjekti" value="{{old('subject')}}" required>
                                @error('subject') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="message">Mesazhi *</label>
                                <textarea name="message" id="message"  class="form-control" required>{{ old('message') }}</textarea>
                                @error('message') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn text-white btn-success fullwidth">Dërgo</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>