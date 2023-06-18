<x-app-layout>
    @push('styles')
        
    @endpush
    <style>
        .error-page {
            background-color: #efeef0;
            padding: 25px 0;
            margin: -30px 0 -50px;
            z-index: -1;
            border-bottom: 2px solid #fabf3e;
        }
        .error-page img {
            margin-bottom: 100px;
            width: 100%;
        }
        .error-page h3 {
            margin-bottom: 20px;
            font-size: 30px;
            color: #000;
            font-weight: 600;
            text-align: center;
        }
        .error-page p {
            margin-bottom: 20px;
            font-size: 18px;
            text-align: center;
            line-height: 1.6em;
            color: #666;
        }
    </style>
    <div class="error-page">
        <div class="container">
            <img src="{{ asset('/images/404.jpg') }}" alt="">
                <h3>Faqja e kërkuar nuk është gjetur</h3>
                <p>Një gabim ka ndodhur ose faqja që keni kërkuar nuk ekziston. <br> Ju mund të provoni ta kërkoni ose të ktheni në <b><a href="{{ route('home') }}"> Faqen Kryesore</a><b></p>
        </div>
    </div>

</x-app-layout>