<div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.addEventListener("livewire:load", function(event) {
                let cartLocalStorage = localStorage.getItem('cart');
                let wishlistLocalStorage = localStorage.getItem('wishlist');
                window.localStorage.setItem('cart', '{}');
                window.localStorage.setItem('wishlist', '{}');
                if(!cartLocalStorage){
                    cartLocalStorage = {};
                }
                if(!wishlistLocalStorage){
                    wishlistLocalStorage = {};
                }
                @this.emit('updateLocal', cartLocalStorage, wishlistLocalStorage);
            });
        });
    </script>
</div>
