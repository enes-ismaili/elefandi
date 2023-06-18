<x-app-layout>
@push('styles')
.error-page img {
    margin-bottom: 100px;
    width: 100%;
}
.error-page {
    background-color: #efeef0;
    padding: 25px 0;
    margin: -30px 0 -50px;
    z-index: -1;
    border-bottom: 2px solid #fabf3e;
}
.error-page h3 {
    margin-bottom: 20px;
    font-size: 36px;
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
@endpush

<div class="error-page">
    <div class="container">
        <img src="http://nouthemes.net/html/martfury/img/404.jpg" alt="">
                <h3>ohh! page not found</h3>
                <p>It seems we can't find what you're looking for. Perhaps searching can help or go back to<a href="index.html"> Homepage</a></p>
    </div>
</div>

</x-app-layout>