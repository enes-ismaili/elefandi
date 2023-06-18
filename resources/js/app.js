require('./bootstrap');

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

let stories = document.querySelector("#stories");
if (stories) {
    stories.addEventListener("touchstart", startTouch, false);
    stories.addEventListener("touchmove", moveTouch, false);
    let initialX = null;
    let initialY = null;

    function startTouch(e) {
        initialX = e.touches[0].clientX;
        initialY = e.touches[0].clientY;
    };

    function moveTouch(e) {
        if (initialX === null) {
            return;
        }

        if (initialY === null) {
            return;
        }

        var currentX = e.touches[0].clientX;
        var currentY = e.touches[0].clientY;

        var diffX = initialX - currentX;
        var diffY = initialY - currentY;

        if (Math.abs(diffX) > Math.abs(diffY)) {
            // sliding horizontally
            if (diffX > 0) {
                // swiped left
                console.log("swiped left");
            } else {
                // swiped right
                console.log("swiped right");
            }
        } else {
            // sliding vertically
            if (diffY > 0) {
                // swiped up
                console.log("swiped up");
            } else {
                // swiped down
                console.log("swiped down");
            }
        }
        initialX = null;
        initialY = null;
        e.preventDefault();
    };
}
let tabs = document.querySelectorAll('.tab-list li span')
tabs.forEach(tab => {
    tab.addEventListener('click', (e) => {
        let newTab = e.target.dataset.id;
        let activeTab = document.querySelectorAll('.tab-list li.active');
        activeTab.forEach(atab => {
            atab.classList.remove('active');
        })
        let activeTabBlock = document.querySelectorAll('.tab-list .tabs .active');
        activeTabBlock.forEach(atabBlock => {
            atabBlock.classList.remove('active');
        })
        e.target.parentElement.classList.add('active');
        document.querySelector('.tab-list #' + newTab).classList.add('active');

    })
})
let goToRating = document.querySelector('.goToRating');
if (goToRating) {
    goToRating.addEventListener('click', (e) => {
        let activeTab = document.querySelectorAll('.tab-list li.active');
        activeTab.forEach(atab => {
            atab.classList.remove('active');
        })
        let activeTabBlock = document.querySelectorAll('.tab-list .tabs .active');
        activeTabBlock.forEach(atabBlock => {
            atabBlock.classList.remove('active');
        })
        let ratingTab = document.querySelector('.tab-list .tab-header li:nth-child(4)');
        ratingTab.classList.add('active');
        let ratingTabB = document.querySelector('.tab-list .tabs #tab-4')
        ratingTabB.classList.add('active');
        ratingTabB.scrollIntoView({ behavior: 'smooth', block: 'center' });
    })
}


let openCat = document.querySelector('.categories__button');
let mainBody = document.getElementsByTagName('body')[0];
if (openCat) {
    openCat.addEventListener('click', e => {
        mainBody.classList.add('opencat');
        mainBody.classList.add('openbg');
    })
}

let closeBg = document.querySelector('.bg_open');
if (closeBg) {
    closeBg.addEventListener('click', e => {
        if (mainBody.classList.contains('opencat')) {
            mainBody.classList.remove('opencat');
        }
        if (mainBody.classList.contains('opencountry')) {
            mainBody.classList.remove('opencountry');
        }
        if (mainBody.classList.contains('openprofile')) {
            mainBody.classList.remove('openprofile');
        }
        if (mainBody.classList.contains('openmenu')) {
            mainBody.classList.remove('openmenu');
        }
        if (mainBody.classList.contains('opensearch')) {
            mainBody.classList.remove('opensearch');
        }
        mainBody.classList.remove('openbg');
    });
}
let closeCat = document.querySelector('.categories-open');
if (closeCat) {
    closeCat.addEventListener('click', e => {
        if (mainBody.classList.contains('opencat')) {
            mainBody.classList.remove('opencat');
            mainBody.classList.remove('openbg');
        }
    });
}
let currentCountry = document.querySelector('.current-country');
if (currentCountry) {
    currentCountry.addEventListener('click', e => {
        mainBody.classList.add('opencountry');
        mainBody.classList.add('openbg');
    });
}
let userProfile = document.querySelector('.user-profile.m');
if (userProfile) {
    userProfile.addEventListener('click', e => {
        mainBody.classList.add('openprofile');
        mainBody.classList.add('openbg');
    });
}

let showCategories = document.querySelectorAll('.filter-categories .right-icon');
showCategories.forEach(showCategory => {
    showCategory.addEventListener('click', e => {
        let currCat = e.target.parentElement.parentElement;
        if (currCat.classList.contains('show')) {
            currCat.classList.remove('show')
        } else {
            currCat.classList.add('show')
        }
    });
})

let addCarts = document.querySelectorAll('.add-cart.jadd');
let addWishlists = document.querySelectorAll('.add-wishlist.jadd');
let currentCart = window.localStorage.getItem('cart');
let currentWishlist = window.localStorage.getItem('wishlist');
let newCart = {}
if (currentCart) {
    newCart = JSON.parse(currentCart);
}
let newWishlist = {}
if (currentWishlist) {
    newWishlist = JSON.parse(currentWishlist);
}
addCarts.forEach(addCart => {
    let prodid = addCart.dataset.id;
    let variant = addCart.dataset.variant;
    if (newCart['p' + prodid + 'v0']) {
        addCart.innerHTML = '<i class="fas fa-cart-plus"></i>Është në Shportë';
        addCart.classList.add('remove');
    } else {
        addCart.innerHTML = '<i class="fas fa-cart-plus"></i>Shto në Shportë';
    }
    addCart.addEventListener('click', (e) => {
        prodid = e.target.dataset.id;
        variant = e.target.dataset.variant;
        if (e.target.classList.contains('remove')) {
            rmCartLocal(prodid, variant);
            e.target.innerHTML = '<i class="fas fa-cart-plus"></i>Shto në Shportë';
            e.target.classList.remove('remove');
        } else {
            addCartLocal(prodid, variant);
            e.target.innerHTML = '<i class="fas fa-cart-plus"></i>Është në Shportë';
            e.target.classList.add('remove');
        }
    });
})
addWishlists.forEach(addWishlist => {
    let prodid = addWishlist.dataset.id;
    if (newWishlist['p' + prodid]) {
        addWishlist.classList.add('remove');
    }
    addWishlist.addEventListener('click', (e) => {
        prodid = e.target.dataset.id;
        if (e.target.classList.contains('remove')) {
            rmWishLocal(prodid);
            e.target.classList.remove('remove');
        } else {
            addWishLocal(prodid);
            e.target.classList.add('remove');
        }
    });
});

function addCartLocal(prodid, variant) {
    let currentCart = window.localStorage.getItem('cart');
    let newCart = {};
    if (currentCart) {
        newCart = JSON.parse(currentCart);
    }
    let newElem = { id: prodid, variant_id: variant, qty: 1 };
    newCart['p' + prodid + 'v' + variant] = newElem;
    window.localStorage.setItem('cart', JSON.stringify(newCart));
}

function rmCartLocal(prodid, variant) {
    let currentCart = window.localStorage.getItem('cart');
    let newCart = {};
    if (currentCart) {
        newCart = JSON.parse(currentCart);
    }
    delete newCart['p' + prodid + 'v' + variant];
    window.localStorage.setItem('cart', JSON.stringify(newCart));
}

function addWishLocal(prodid) {
    let currentWishlist = window.localStorage.getItem('wishlist');
    let newWishlist = {};
    if (currentWishlist) {
        newWishlist = JSON.parse(currentWishlist);
    }
    let newElem = { id: prodid };
    newWishlist['p' + prodid] = newElem;
    window.localStorage.setItem('wishlist', JSON.stringify(newWishlist));
    window.livewire.emitTo('header.wishlist', 'getWishlistsUpdate', JSON.stringify(newWishlist));
}

function rmWishLocal(prodid) {
    let currentWishlist = window.localStorage.getItem('wishlist');
    let newWishlist = {};
    if (currentWishlist) {
        newWishlist = JSON.parse(currentWishlist);
    }
    delete newWishlist['p' + prodid];
    window.localStorage.setItem('wishlist', JSON.stringify(newWishlist));
    window.livewire.emitTo('header.wishlist', 'getWishlistsUpdate', JSON.stringify(newWishlist));
}

// import Echo from 'laravel-echo';

// window.Echo = new Echo({
//     broadcaster: 'socket.io',
//     host: window.location.hostname + ":" + window.laravel_echo_port
// });

let rightIcons = document.querySelectorAll('.main-header .categories__link a .right-icon');
rightIcons.forEach(rightIcon => {
    rightIcon.addEventListener('click', (e) => {
        if (window.innerWidth <= 765) {
            e.preventDefault();
            let mainEl = e.target.parentElement.parentElement;
            if (mainEl.classList.contains('active')) {
                mainEl.classList.remove('active')
            } else {
                mainEl.classList.add('active')
            }
        }
    })
})

let mobileMenu = document.querySelector('.main-header .mobile-menu');
if (mobileMenu) {
    mobileMenu.addEventListener('click', (e) => {
        if (window.innerWidth <= 765) {
            mainBody.classList.add('openmenu');
            mainBody.classList.add('openbg');
        }
    })
}
let mobileSearch = document.querySelector('.main-header .search-menu .search-icon');
if (mobileSearch) {
    mobileSearch.addEventListener('click', (e) => {
        if (window.innerWidth <= 999) {
            mainBody.classList.add('opensearch');
            mainBody.classList.add('openbg');
        }
    })
}
let openFilter = document.querySelector('.filter-menu');
if (openFilter) {
    openFilter.addEventListener('click', (e) => {
        if (window.innerWidth <= 999) {
            let taxonomyFilter = document.querySelector('.product-listing .taxonomy-filter');
            if (taxonomyFilter.classList.contains('active')) {
                taxonomyFilter.classList.remove('active');
            } else {
                taxonomyFilter.classList.add('active')
            }
        }
    })
}
let footerAccordions = document.querySelectorAll('.footer-accordion .title');
footerAccordions.forEach(footerAccordion => {
    footerAccordion.addEventListener('click', (e) => {
        let footerAccordionP = e.target.parentElement;
        if (footerAccordionP.classList.contains('show')) {
            footerAccordionP.classList.remove('show');
        } else {
            footerAccordionP.classList.add('show')
        }
    });
})

let closeCategories = document.querySelector('.categories-title .close-popup');
if (closeCategories) {
    closeCategories.addEventListener('click', (e) => {
        mainBody.classList.remove('opencat');
        mainBody.classList.remove('openbg');
    })
}

let FgoHome = document.querySelector('.fixed-footer .go-home');
if (FgoHome) {
    FgoHome.addEventListener('click', (e) => {
        if (mainBody.classList.contains('openbg')) {
            e.preventDefault();
        }
        checkOpenedTabs();
    })
}
let FopenCategories = document.querySelector('.fixed-footer .open-category');
if (FopenCategories) {
    FopenCategories.addEventListener('click', (e) => {
        if (mainBody.classList.contains('opencat')) {
            mainBody.classList.remove('opencat');
            mainBody.classList.remove('openbg');
        } else {
            checkOpenedTabs();
            mainBody.classList.add('opencat');
            mainBody.classList.add('openbg');
        }
    })
}
let FopenSearch = document.querySelector('.fixed-footer .open-search');
if (FopenSearch) {
    FopenSearch.addEventListener('click', (e) => {
        if (mainBody.classList.contains('opensearch')) {
            mainBody.classList.remove('opensearch');
            mainBody.classList.remove('openbg');
        } else {
            checkOpenedTabs();
            mainBody.classList.add('opensearch');
            mainBody.classList.add('openbg');
        }
    })
}
let FopenLogin = document.querySelector('.fixed-footer .open-login');
if (FopenLogin) {
    FopenLogin.addEventListener('click', (e) => {
        if (mainBody.classList.contains('openlogin')) {
            mainBody.classList.remove('openlogin');
            mainBody.classList.remove('openbg');
        } else {
            checkOpenedTabs();
            mainBody.classList.add('openlogin');
            mainBody.classList.add('openbg');
            window.livewire.emitTo('header.login-user', 'open-login');
        }
    })
}
let FopenProfile = document.querySelector('.fixed-footer .open-profile');
if (FopenProfile) {
    FopenProfile.addEventListener('click', (e) => {
        if (mainBody.classList.contains('openprofiles')) {
            mainBody.classList.remove('openprofiles');
            mainBody.classList.remove('openbg');
        } else {
            checkOpenedTabs();
            mainBody.classList.add('openprofiles');
            mainBody.classList.add('openbg');
        }
    })
}
let FopenMenu = document.querySelector('.fixed-footer .open-menu');
if (FopenMenu) {
    FopenMenu.addEventListener('click', (e) => {
        if (mainBody.classList.contains('openmenu')) {
            mainBody.classList.remove('openmenu');
            mainBody.classList.remove('openbg');
        } else {
            checkOpenedTabs();
            mainBody.classList.add('openmenu');
            mainBody.classList.add('openbg');
        }
    })
}
let closeProfile = document.querySelector('.profile-name .close-popup');
if (closeProfile) {
    closeProfile.addEventListener('click', (e) => {
        checkOpenedTabs();
    })
}
let closeMenuM = document.querySelector('.right-header .menu-title .close-popup');
if (closeMenuM) {
    closeMenuM.addEventListener('click', (e) => {
        checkOpenedTabs();
    })
}

function checkOpenedTabs() {
    if (mainBody.classList.contains('opencat')) {
        mainBody.classList.remove('opencat');
        mainBody.classList.remove('openbg');
    } else if (mainBody.classList.contains('opensearch')) {
        mainBody.classList.remove('opensearch');
        mainBody.classList.remove('openbg');
    } else if (mainBody.classList.contains('openlogin')) {
        mainBody.classList.remove('openlogin');
        mainBody.classList.remove('openbg');
    } else if (mainBody.classList.contains('openprofiles')) {
        mainBody.classList.remove('openprofiles');
        mainBody.classList.remove('openbg');
    } else if (mainBody.classList.contains('openmenu')) {
        mainBody.classList.remove('openmenu');
        mainBody.classList.remove('openbg');
    }
}