<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdsController;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SocialiteLoginController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('phpinfo', function(){
    phpinfo();
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/', [HomeController::class,'home'])->name('home');
Route::get('/home', [HomeController::class,'homeRedirect'])->name('home.redirect');

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin', [AdminController::class,'home'])->name('admin.home');

    Route::get('/admin/users', [AdminController::class,'users'])->name('admin.users.index')->middleware('check_role');
    Route::get('/admin/users/{id}', [AdminController::class,'singleUser'])->name('admin.users.single')->middleware('check_role');
    Route::get('/admin/users/{id}/delete', [AdminController::class,'singleUserDelete'])->name('admin.users.delete')->middleware('check_role');

    Route::get('/admin/vendors', [AdminController::class,'vendors'])->name('admin.vendors.index');
    Route::get('/admin/vendor/edit/{id}', [AdminController::class,'editvendor'])->name('admin.vendors.edit');
    Route::post('/admin/vendor/store/{id}', [AdminController::class,'storevendor'])->name('admin.vendors.store');
    Route::get('/admin/vendors/namechange', [AdminController::class,'vendorsname'])->name('admin.vendors.namechange');
    Route::get('/admin/vendor/namechange/edit/{id}', [AdminController::class,'editvendorname'])->name('admin.vendor.namechange.edit');
    Route::post('/admin/vendor/namechange/store/{id}', [AdminController::class,'storevendorname'])->name('admin.vendor.namechange.store');
    Route::get('/admin/vendor/delete/{id}', [AdminController::class,'deleteVendor'])->name('admin.vendor.delete');

    Route::get('/admin/vendor/loginvendor/{id}', [AdminController::class,'loginvendor'])->name('admin.vendors.login');
    Route::get('/admin/vendor/cloginvendor}', [AdminController::class,'cloginvendor'])->name('admin.vendors.return');
    
    Route::get('/admin/vendors/requests', [AdminController::class,'vendorsRequest'])->name('admin.vendors.requests');
    Route::get('/admin/vendor/requests/edit/{id}', [AdminController::class,'vendorsRequestedit'])->name('admin.vendors.requests.edit');
    Route::post('/admin/vendor/requests/edit/{id}', [AdminController::class,'vendorsRequestupdate'])->name('admin.vendors.requests.update');
    Route::get('/admin/vendor/requests/delete/{id}', [AdminController::class,'vendorsRequestdelete'])->name('admin.vendors.requests.delete');

    Route::get('/admin/vendors/{id}/membership', [AdminController::class,'membership'])->name('admin.vendors.membership.index');
    Route::get('/admin/vendors/{id}/membership/add', [AdminController::class,'membershipadd'])->name('admin.vendors.membership.add');
    Route::post('/admin/vendors/{id}/membership/add', [AdminController::class,'membershipstore'])->name('admin.vendors.membership.store');
    Route::get('/admin/vendors/{id}/membership/{mid}/edit', [AdminController::class,'membershipedit'])->name('admin.vendors.membership.edit');
    Route::post('/admin/vendors/{id}/membership/{mid}/edit', [AdminController::class,'membershipupdate'])->name('admin.vendors.membership.update');
    Route::get('/admin/vendors/{id}/membership/{mid}/delete', [AdminController::class,'membershipdelete'])->name('admin.vendors.membership.delete');

    Route::get('/admin/invoices/', [AdminController::class,'membershipinvoice'])->name('admin.vendors.membership.invoice');
    Route::get('/admin/invoices/edit/{id}', [AdminController::class,'membershipinvoiceedit'])->name('admin.vendors.membership.invoice.edit');
    Route::post('/admin/invoices/edit/{id}', [AdminController::class,'membershipinvoiceupdate'])->name('admin.vendors.membership.invoice.update');

    Route::get('/admin/products', [ProductController::class,'products'])->name('admin.products.show');
    Route::get('/admin/products/edit/{id}', [ProductController::class,'editproducts'])->name('admin.products.edit');
    Route::post('/admin/products/update/{id}', [ProductController::class,'storeproducts'])->name('admin.products.update');
    Route::get('/admin/products/delete/{id}', [ProductController::class,'deleteproducts'])->name('admin.products.delete');
    Route::get('/admin/products/sales/{id}', [ProductController::class,'salesproducts'])->name('admin.products.sales');
    Route::post('/admin/products/sales/{id}', [ProductController::class,'salesproductsUpdate'])->name('admin.products.supdate');

    Route::get('/admin/products/{id}/comments/', [ProductController::class,'commentsproducts'])->name('admin.products.comments');
    Route::get('/admin/products/{id}/comments/{cid}/delete', [ProductController::class,'commentsproductsDelete'])->name('admin.products.comments.delete');

    Route::get('/admin/products/categories', [TaxonomyController::class,'categories'])->name('admin.products.categories.index');
    Route::post('/admin/products/categories/save', [TaxonomyController::class,'savecategories'])->name('admin.products.categories.savelist');
    Route::get('/admin/products/addcategory', [TaxonomyController::class,'addcategory'])->name('admin.products.categories.add');
    Route::post('/admin/products/addcategory', [TaxonomyController::class,'storecategory'])->name('admin.products.categories.store');
    Route::get('/admin/products/editcategory/{id}', [TaxonomyController::class,'editcategory'])->name('admin.products.categories.edit');
    Route::post('/admin/products/editcategory/{id}', [TaxonomyController::class,'updatecategory'])->name('admin.products.categories.update');
    Route::get('/admin/products/deletecategory/{id}', [TaxonomyController::class,'deletecategory'])->name('admin.products.categories.delete');

    Route::get('/admin/products/tags', [TaxonomyController::class,'tags'])->name('admin.products.tags.index');
    Route::get('/admin/products/addtags', [TaxonomyController::class,'addtag'])->name('admin.products.tags.add');
    Route::post('/admin/products/addtags', [TaxonomyController::class,'storetag'])->name('admin.products.tags.store');
    Route::get('/admin/products/edittags/{id}', [TaxonomyController::class,'edittag'])->name('admin.products.tags.edit');
    Route::post('/admin/products/edittags/{id}', [TaxonomyController::class,'updatetag'])->name('admin.products.tags.update');
    Route::get('/admin/products/deletetags/{id}', [TaxonomyController::class,'deletetag'])->name('admin.products.tags.delete');

    Route::get('/admin/products/brands', [TaxonomyController::class,'brands'])->name('admin.products.brands.index');
    Route::get('/admin/products/addbrands', [TaxonomyController::class,'addbrand'])->name('admin.products.brands.add');
    Route::post('/admin/products/addbrands', [TaxonomyController::class,'storebrand'])->name('admin.products.brands.store');
    Route::get('/admin/products/editbrands/{id}', [TaxonomyController::class,'editbrand'])->name('admin.products.brands.edit');
    Route::post('/admin/products/editbrands/{id}', [TaxonomyController::class,'updatebrand'])->name('admin.products.brands.update');
    Route::get('/admin/products/deletebrands/{id}', [TaxonomyController::class,'deletebrand'])->name('admin.products.brands.delete');

    Route::get('/admin/products/variants', [TaxonomyController::class,'variants'])->name('admin.products.variants.index');
    Route::get('/admin/products/addvariants', [TaxonomyController::class,'addvariant'])->name('admin.products.variants.add');
    Route::post('/admin/products/addvariants', [TaxonomyController::class,'storevariant'])->name('admin.products.variants.store');
    Route::get('/admin/products/editvariants/{id}', [TaxonomyController::class,'editvariant'])->name('admin.products.variants.edit');
    Route::post('/admin/products/editvariants/{id}', [TaxonomyController::class,'updatevariant'])->name('admin.products.variants.update');
    Route::get('/admin/products/deletevariants/{id}', [TaxonomyController::class,'deletevariant'])->name('admin.products.variants.delete');

    Route::get('/admin/products/colors', [TaxonomyController::class,'colors'])->name('admin.products.colors.index');
    Route::get('/admin/products/addcolors', [TaxonomyController::class,'addcolor'])->name('admin.products.colors.add');
    Route::post('/admin/products/addcolors', [TaxonomyController::class,'storecolor'])->name('admin.products.colors.store');
    Route::get('/admin/products/editcolors/{id}', [TaxonomyController::class,'editcolor'])->name('admin.products.colors.edit');
    Route::post('/admin/products/editcolors/{id}', [TaxonomyController::class,'updatecolor'])->name('admin.products.colors.update');
    Route::get('/admin/products/deletecolors/{id}', [TaxonomyController::class,'deletecolor'])->name('admin.products.colors.delete');

    Route::get('/admin/products/reports', [ProductController::class,'reports'])->name('admin.products.reports.index');
    Route::get('/admin/products/report/{id}', [ProductController::class,'reportsView'])->name('admin.products.reports.view');
    Route::get('/admin/products/reports/delete/{id}', [ProductController::class,'reportsDelete'])->name('admin.products.reports.delete');

    Route::get('/admin/orders', [OrderController::class,'orders'])->name('admin.orders.index');
    Route::get('/admin/orders/pending', [OrderController::class,'porders'])->name('admin.orders.pending');
    Route::get('/admin/orders/completed', [OrderController::class,'corders'])->name('admin.orders.completed');
    Route::get('/admin/orders/canceled', [OrderController::class,'dorders'])->name('admin.orders.canceled');
    Route::get('/admin/orders/{id}', [OrderController::class,'singleorder'])->name('admin.orders.single');
    Route::get('/admin/orders/{id}/track', [OrderController::class,'singleordertrack'])->name('admin.orders.track');
    Route::post('/admin/orders/{id}/addtrack', [OrderController::class,'addsingleordertrack'])->name('admin.orders.track.add');

    Route::get('/admin/coupons', [CouponController::class,'coupons'])->name('admin.coupons.index');
    Route::get('/admin/coupons/edit/{id}', [CouponController::class,'editcoupons'])->name('admin.coupons.edit');
    Route::post('/admin/coupons/store/{id}', [CouponController::class,'storecoupons'])->name('admin.coupons.store');
    Route::get('/admin/coupons/delete/{id}', [CouponController::class,'deleteCoupon'])->name('admin.coupons.delete');

    Route::get('/admin/offers', [OfferController::class,'offers'])->name('admin.offers.index');
    Route::get('/admin/offers/new', [OfferController::class,'newoffers'])->name('admin.offers.new');
    Route::post('/admin/offers/save', [OfferController::class,'saveoffers'])->name('admin.offers.save');
    Route::get('/admin/offers/edit/{id}', [OfferController::class,'editoffers'])->name('admin.offers.edit');
    Route::post('/admin/offers/store/{id}', [OfferController::class,'storeoffers'])->name('admin.offers.store');
    Route::get('/admin/offers/delete/{id}', [OfferController::class,'deleteoffers'])->name('admin.offers.delete');

    Route::get('/admin/settings/main', [SettingController::class,'settingsMain'])->name('admin.settings.main');
    Route::post('/admin/settings/main', [SettingController::class,'savesettingsMain'])->name('admin.settings.save');

    Route::get('/admin/settings/countries', [SettingController::class,'countries'])->name('admin.settings.countries.index');
    Route::get('/admin/settings/countries/{id}/edit', [SettingController::class,'editcountries'])->name('admin.settings.countries.edit');
    Route::post('/admin/settings/countries/{id}/edit', [SettingController::class,'updatecountries'])->name('admin.settings.countries.update');
    Route::get('/admin/settings/countries/{id}/add', [SettingController::class,'addcities'])->name('admin.settings.countries.cities.add');
    Route::post('/admin/settings/countries/{id}/add', [SettingController::class,'storecities'])->name('admin.settings.countries.cities.store');
    Route::get('/admin/settings/countries/{id}/edit/{cid}', [SettingController::class,'editcities'])->name('admin.settings.countries.cities.edit');
    Route::post('/admin/settings/countries/{id}/edit/{cid}', [SettingController::class,'updatecities'])->name('admin.settings.countries.cities.update');
    Route::get('/admin/settings/countries/{id}/delete/{cid}', [SettingController::class,'deletecities'])->name('admin.settings.countries.cities.delete');

    Route::get('/admin/settings/home/slider', [SettingController::class,'settingsSlider'])->name('admin.homesettings.slider');
    Route::post('/admin/settings/home/slider', [SettingController::class,'settingsSliderUpdate'])->name('admin.homesettings.slider.update');
    Route::get('/admin/settings/home/slidermobile', [SettingController::class,'settingsSliderMobile'])->name('admin.homesettings.slidermobile');
    Route::post('/admin/settings/home/slidermobile', [SettingController::class,'settingsSliderMobileUpdate'])->name('admin.homesettings.slidermobile.update');
    Route::get('/admin/settings/home/slidermobile/add', [SettingController::class,'settingsSliderMobileAdd'])->name('admin.homesettings.slidermobile.add');
    Route::post('/admin/settings/home/slidermobile/add', [SettingController::class,'settingsSliderMobileAddStore'])->name('admin.homesettings.slidermobile.add.store');
    Route::get('/admin/settings/home/slidermobile/edit/{id}', [SettingController::class,'settingsSliderMobileEdit'])->name('admin.homesettings.slidermobile.edit');
    Route::post('/admin/settings/home/slidermobile/edit/{id}', [SettingController::class,'settingsSliderMobileEditUpdate'])->name('admin.homesettings.slidermobile.edit.update');
    Route::get('/admin/settings/home/slidermobile/delete/{id}', [SettingController::class,'settingsSliderMobileDelete'])->name('admin.homesettings.slidermobile.delete');

    Route::get('/admin/settings/home/features', [SettingController::class,'settingsFeatures'])->name('admin.homesettings.features');
    Route::get('/admin/settings/home/addfeatures', [SettingController::class,'addFeatures'])->name('admin.homesettings.addfeatures');
    Route::post('/admin/settings/home/addfeatures', [SettingController::class,'storeFeatures'])->name('admin.homesettings.storeFeatures');
    Route::get('/admin/settings/home/editfeatures/{id}', [SettingController::class,'editFeatures'])->name('admin.homesettings.editfeatures');
    Route::post('/admin/settings/home/editfeatures/{id}', [SettingController::class,'saveFeatures'])->name('admin.homesettings.saveFeatures');
    Route::get('/admin/settings/home/deletefeatures/{id}', [SettingController::class,'deleteFeatures'])->name('admin.homesettings.deletefeatures');

    Route::get('/admin/settings/home/featuredproduct', [SettingController::class,'featuredProduct'])->name('admin.homesettings.featuredProduct');
    Route::get('/admin/settings/home/addfeaturedproduct', [SettingController::class,'addFeaturedProduct'])->name('admin.homesettings.addFeaturedProduct');
    Route::post('/admin/settings/home/addfeaturedproduct', [SettingController::class,'storeFeaturedProduct'])->name('admin.homesettings.storeFeaturedproduct');
    Route::get('/admin/settings/home/editfeaturedproduct/{id}', [SettingController::class,'editFeaturedProduct'])->name('admin.homesettings.editFeaturedProduct');
    Route::post('/admin/settings/home/editfeaturedproduct/{id}', [SettingController::class,'saveFeaturedProduct'])->name('admin.homesettings.saveFeaturedProduct');
    Route::get('/admin/settings/home/deletefeaturedproduct/{id}', [SettingController::class,'deleteFeaturedProduct'])->name('admin.homesettings.deleteFeaturedProduct');

    Route::get('/admin/settings/home/trending', [SettingController::class,'trendingCategories'])->name('admin.homesettings.trending.index');
    Route::get('/admin/settings/home/edittrending/{id}', [SettingController::class,'editrendingCategories'])->name('admin.homesettings.trending.edit');
    Route::post('/admin/settings/home/edittrending/{id}', [SettingController::class,'storeTrendingCategories'])->name('admin.homesettings.trending.store');

    Route::get('/admin/settings/home/categories', [SettingController::class,'categoriesHome'])->name('admin.homesettings.categories.index');
    Route::get('/admin/settings/home/editcategories/{id}', [SettingController::class,'editCategoriesHome'])->name('admin.homesettings.categories.edit');
    Route::post('/admin/settings/home/editcategories/{id}', [SettingController::class,'storeCategoriesHome'])->name('admin.homesettings.categories.store');
    Route::get('/admin/settings/home/editcategories/{id}/addslider', [SettingController::class,'sliderCategoriesHome'])->name('admin.homesettings.categories.slider');
    Route::post('/admin/settings/home/editcategories/{id}/addslider', [SettingController::class,'sliderAddCategoriesHome'])->name('admin.homesettings.categories.addslider');
    Route::get('/admin/settings/home/editcategories/{id}/editslider/{sid}', [SettingController::class,'editsliderCategoriesHome'])->name('admin.homesettings.categories.editslider');
    Route::post('/admin/settings/home/editcategories/{id}/editslider/{sid}', [SettingController::class,'sliderStoreCategoriesHome'])->name('admin.homesettings.categories.storeslider');
    Route::get('/admin/settings/home/editcategories/{id}/deleteslider/{sid}', [SettingController::class,'sliderDeleteCategoriesHome'])->name('admin.homesettings.categories.deleteslider');

    Route::get('/admin/settings/footer/column/{id}', [SettingController::class,'footercolumn'])->name('admin.settings.footer.index');
    Route::get('/admin/settings/footer/column1', [SettingController::class,'footercolumn1'])->name('admin.settings.footer1.index');
    Route::get('/admin/settings/footer/column2', [SettingController::class,'footercolumn2'])->name('admin.settings.footer2.index');
    Route::get('/admin/settings/footer/column3', [SettingController::class,'footercolumn3'])->name('admin.settings.footer3.index');
    Route::post('/admin/settings/footer/update/{id}', [SettingController::class,'footerorder'])->name('admin.settings.footer.order');
    Route::get('/admin/settings/footer/add/{id}', [SettingController::class,'footeradd'])->name('admin.settings.footer.add');
    Route::post('/admin/settings/footer/add/{id}', [SettingController::class,'footerstore'])->name('admin.settings.footer.store');
    Route::get('/admin/settings/footer/{lid}/edit/{id}', [SettingController::class,'footeredit'])->name('admin.settings.footer.edit');
    Route::post('/admin/settings/footer/{lid}/edit/{id}', [SettingController::class,'footerupdate'])->name('admin.settings.footer.update');
    Route::get('/admin/settings/footer/{lid}/delete/{id}', [SettingController::class,'footerdelete'])->name('admin.settings.footer.delete');

    Route::get('/admin/emails', [EmailController::class,'emails'])->name('admin.emails.index');
    Route::get('/admin/emails/edit/{id}', [EmailController::class,'template'])->name('admin.emails.edit');
    Route::post('/admin/emails/edit/{id}', [EmailController::class,'storeTemplate'])->name('admin.emails.update');
    Route::get('/admin/emails/send', [EmailController::class,'send'])->name('admin.emails.send');
    Route::post('/admin/emails/send', [EmailController::class,'sendPost'])->name('admin.emails.sendpost');
    
    Route::get('/admin/tickets', [AdminController::class,'tickets'])->name('admin.ticket.index');
    Route::get('/admin/tickets/{id}', [AdminController::class,'singletickets'])->name('admin.ticket.single');
    Route::post('/admin/tickets/{id}', [AdminController::class,'addsingletickets'])->name('admin.ticket.store');
    Route::post('/admin/tickets/{id}/close', [AdminController::class,'closesingletickets'])->name('admin.ticket.close');
    Route::post('/admin/tickets/{id}/refund', [AdminController::class,'refundsingletickets'])->name('admin.ticket.refund');

    Route::get('/admin/roles', [RoleController::class,'manageroles'])->name('admin.roles.index');
    Route::get('/admin/roles/add', [RoleController::class,'addroles'])->name('admin.roles.add');
    Route::post('/admin/roles/add', [RoleController::class,'storeroles'])->name('admin.roles.add.submit');
    Route::get('/admin/roles/edit/{id}', [RoleController::class,'editroles'])->name('admin.roles.edit');
    Route::post('/admin/roles/save/{id}', [RoleController::class,'saveroles'])->name('admin.roles.add.save');
    Route::get('/admin/roles/delete/{id}', [RoleController::class,'deleterole'])->name('admin.roles.delete');

    Route::get('/admin/staff', [RoleController::class,'managestaff'])->name('admin.staff.index');
    Route::get('/admin/staff/add', [RoleController::class,'addstaff'])->name('admin.staff.add');
    Route::post('/admin/staff/add', [RoleController::class,'storestaff'])->name('admin.staff.add.submit');
    Route::get('/admin/staff/edit/{id}/{rid}', [RoleController::class,'editstaff'])->name('admin.staff.edit');
    Route::post('/admin/staff/save/{id}/{rid}', [RoleController::class,'savestaff'])->name('admin.staff.save');
    Route::get('/admin/staff/delete/{id}/{rid}', [RoleController::class,'deletestaff'])->name('admin.staff.delete');

    Route::get('/admin/stories', [StoryController::class,'stories'])->name('admin.stories.index');
    Route::get('/admin/stories/add', [StoryController::class,'addstories'])->name('admin.stories.add');
    Route::post('/admin/stories/add', [StoryController::class,'addstorestories'])->name('admin.stories.addstore');
    Route::get('/admin/stories/edit/{id}', [StoryController::class,'editstories'])->name('admin.stories.edit');
    Route::post('/admin/stories/edit/{id}', [StoryController::class,'storestories'])->name('admin.stories.store');
    Route::get('/admin/stories/delete/{id}', [StoryController::class,'deletestories'])->name('admin.stories.delete');
    Route::get('/admin/stories/story/{id}/add/', [StoryController::class,'storiesadd'])->name('admin.stories.story.add');
    Route::post('/admin/stories/story/{id}/add/', [StoryController::class,'storiesstore'])->name('admin.stories.story.store');
    Route::get('/admin/stories/story/{id}/edit/{sid}', [StoryController::class,'storiesedit'])->name('admin.stories.story.edit');
    Route::post('/admin/stories/story/{id}/edit/{sid}', [StoryController::class,'storiesupdate'])->name('admin.stories.story.update');
    Route::get('/admin/stories/story/{id}/delete/{sid}', [StoryController::class,'storiesdelete'])->name('admin.stories.story.delete');

    Route::get('/admin/stories/monthlylimit', [StoryController::class,'limitStories'])->name('admin.stories.limit.edit');
    Route::post('/admin/stories/monthlylimit', [StoryController::class,'limitStoriesUpdate'])->name('admin.stories.limit.update');

    Route::get('/admin/pages', [PageController::class,'index'])->name('admin.pages.index');
    Route::get('/admin/pages/create', [PageController::class,'create'])->name('admin.pages.add');
    Route::post('/admin/pages/create', [PageController::class,'store'])->name('admin.pages.store');
    Route::get('/admin/pages/edit/{id}', [PageController::class,'edit'])->name('admin.pages.edit');
    Route::post('/admin/pages/edit/{id}', [PageController::class,'update'])->name('admin.pages.update');
    Route::get('/admin/pages/delete/{id}', [PageController::class,'delete'])->name('admin.pages.delete');

    Route::get('/admin/notifications', [NotificationController::class,'index'])->name('admin.notifications.index');
    Route::get('/admin/notifications/add', [NotificationController::class,'addNotification'])->name('admin.notifications.add');
    Route::post('/admin/notifications/add', [NotificationController::class,'storeNotification'])->name('admin.notifications.store');
    Route::get('/admin/notifications/view/{id}', [NotificationController::class,'viewNotification'])->name('admin.notifications.view');
    Route::get('/admin/notifications/edit/{id}', [NotificationController::class,'editNotification'])->name('admin.notifications.edit');
    Route::post('/admin/notifications/edit/{id}', [NotificationController::class,'updateNotification'])->name('admin.notifications.update');
    Route::get('/admin/notifications/delete/{id}', [NotificationController::class,'deleteNotification'])->name('admin.notifications.delete');
    Route::get('/admin/notifications/reject/{id}', [NotificationController::class,'rejectNotification'])->name('admin.notifications.reject');

    Route::get('/admin/notifications/monthlylimit', [NotificationController::class,'limitNotification'])->name('admin.notifications.limit.edit');
    Route::post('/admin/notifications/monthlylimit', [NotificationController::class,'limitNotificationUpdate'])->name('admin.notifications.limit.update');

    Route::get('/admin/ads', [AdsController::class,'index'])->name('admin.ads.index');
    Route::get('/admin/ads/edit/{id}', [AdsController::class,'editAds'])->name('admin.ads.edit');
    Route::post('/admin/ads/edit/{id}', [AdsController::class,'updateAds'])->name('admin.ads.update');
    Route::get('/admin/ads/{id}/view', [AdsController::class,'single'])->name('admin.ads.view');
    Route::get('/admin/ads/{id}/add', [AdsController::class,'addSingleAds'])->name('admin.ads.single.add');
    Route::post('/admin/ads/{id}/add', [AdsController::class,'storeSingleAds'])->name('admin.ads.single.store');
    Route::get('/admin/ads/{id}/edit/{sid}', [AdsController::class,'editSingleAds'])->name('admin.ads.single.edit');
    Route::post('/admin/ads/{id}/edit/{sid}', [AdsController::class,'updateSingleAds'])->name('admin.ads.single.update');
    Route::get('/admin/ads/{id}/delete/{sid}', [AdsController::class,'deleteSingleAds'])->name('admin.ads.single.delete');
});

Route::middleware(['auth', 'is_vendor'])->group(function () {
    Route::get('/vendor', [VendorController::class,'index'])->name('vendor.home');

    Route::get('/vendor/edit/profile', [VendorController::class,'editprofile'])->name('vendor.edit.profile');
    Route::post('/vendor/store/profile', [VendorController::class,'store'])->name('vendor.store.profile');
    Route::post('/vendor/store/namechange/{id}', [VendorController::class,'storenamechange'])->name('vendor.store.namechange');

    Route::get('/vendor/pages/edit/', [PageController::class,'vedit'])->name('vendor.pages.edit');
    Route::post('/vendor/pages/edit/', [PageController::class,'vupdate'])->name('vendor.pages.update');

    Route::get('/vendor/membership/', [VendorController::class,'membership'])->name('vendor.membership.index');

    Route::get('/vendor/products', [ProductController::class,'vproducts'])->name('vendor.products.index');
    Route::get('/vendor/products/edit/{id}', [ProductController::class,'veditproducts'])->name('vendor.products.edit');
    Route::post('/vendor/products/store/{id}', [ProductController::class,'vstoreproducts'])->name('vendor.products.store');
    Route::get('/vendor/products/new', [ProductController::class,'vnewproducts'])->name('vendor.products.new');
    Route::post('/vendor/products/save', [ProductController::class,'vsavenewproducts'])->name('vendor.products.save');
    Route::get('/vendor/products/delete/{id}', [ProductController::class,'vdeleteproducts'])->name('vendor.products.delete');

    Route::get('/vendor/orders', [OrderController::class,'vorders'])->name('vendor.orders.index');
    Route::get('/vendor/orders/pending', [OrderController::class,'vporders'])->name('vendor.orders.pending');
    Route::get('/vendor/orders/completed', [OrderController::class,'vcorders'])->name('vendor.orders.completed');
    Route::get('/vendor/orders/canceled', [OrderController::class,'vdorders'])->name('vendor.orders.canceled');
    Route::get('/vendor/orders/{id}', [OrderController::class,'vsingleorder'])->name('vendor.orders.single');
    Route::get('/vendor/orders/{id}/track', [OrderController::class,'vsingleordertrack'])->name('vendor.orders.track');
    Route::post('/vendor/orders/{id}/addtrack', [OrderController::class,'vaddsingleordertrack'])->name('vendor.orders.track.add');

    Route::get('/vendor/coupons', [CouponController::class,'vcoupons'])->name('vendor.coupons.index');
    Route::get('/vendor/coupons/new', [CouponController::class,'vnewcoupons'])->name('vendor.coupons.new');
    Route::post('/vendor/coupons/save', [CouponController::class,'vsavecoupons'])->name('vendor.coupons.save');
    Route::get('/vendor/coupons/edit/{id}', [CouponController::class,'veditcoupons'])->name('vendor.coupons.edit');
    Route::post('/vendor/coupons/store/{id}', [CouponController::class,'vstorecoupons'])->name('vendor.coupons.store');
    Route::get('/vendor/coupons/delete/{id}', [CouponController::class,'vdeleteCoupon'])->name('vendor.coupons.delete');

    Route::get('/vendor/offers', [OfferController::class,'voffers'])->name('vendor.offers.index');
    Route::get('/vendor/offers/new', [OfferController::class,'vnewoffers'])->name('vendor.offers.new');
    Route::get('/vendor/offers/new/{id}', [OfferController::class,'vnewoffers'])->name('vendor.offers.newp');
    Route::post('/vendor/offers/save', [OfferController::class,'vsaveoffers'])->name('vendor.offers.save');
    Route::get('/vendor/offers/edit/{id}', [OfferController::class,'veditoffers'])->name('vendor.offers.edit');
    Route::post('/vendor/offers/store/{id}', [OfferController::class,'vstoreoffers'])->name('vendor.offers.store');
    Route::get('/vendor/offers/delete/{id}', [OfferController::class,'vdeleteoffers'])->name('vendor.offers.delete');

    Route::get('/vendor/tickets', [VendorController::class,'tickets'])->name('vendor.ticket.index');
    Route::get('/vendor/tickets/{id}', [VendorController::class,'singletickets'])->name('vendor.ticket.single');
    Route::post('/vendor/tickets/{id}', [VendorController::class,'addsingletickets'])->name('vendor.ticket.store');

    Route::get('/vendor/staff', [RoleController::class,'vmanagestaff'])->name('vendor.staff.index');
    Route::get('/vendor/staff/add', [RoleController::class,'vaddstaff'])->name('vendor.staff.add');
    Route::post('/vendor/staff/add', [RoleController::class,'vstorestaff'])->name('vendor.staff.add.submit');
    Route::get('/vendor/staff/edit/{id}/{rid}', [RoleController::class,'veditstaff'])->name('vendor.staff.edit');
    Route::post('/vendor/staff/save/{id}/{rid}', [RoleController::class,'vsavestaff'])->name('vendor.staff.save');
    Route::get('/vendor/staff/delete/{id}/{rid}', [RoleController::class,'vdeletestaff'])->name('vendor.staff.delete');
    Route::get('/vendor/staff/drequest/{id}', [RoleController::class,'vdeleterequeststaff'])->name('vendor.staff.request.delete');

    Route::get('/vendor/stories', [StoryController::class,'vstories1'])->name('vendor.stories.index');
    Route::get('/vendor/stories/add', [StoryController::class,'vaddstories1'])->name('vendor.stories1.add');
    Route::post('/vendor/stories/add', [StoryController::class,'vstoriesstore1'])->name('vendor.stories1.store');
    Route::get('/vendor/stories/edit/{id}', [StoryController::class,'vstoriesedit1'])->name('vendor.stories1.edit');
    Route::post('/vendor/stories/edit/{id}', [StoryController::class,'vstoriesupdate1'])->name('vendor.stories1.update');
    Route::get('/vendor/stories/delete/{id}', [StoryController::class,'vstoriesdelete1'])->name('vendor.stories1.delete');
    // Route::get('/vendor/stories', [StoryController::class,'vstories1'])->name('vendor.stories.index');

    // Route::get('/vendor/stories', [StoryController::class,'vstories'])->name('vendor.stories.index');
    // Route::get('/vendor/stories2/add', [StoryController::class,'vaddstories'])->name('vendor.stories.add');
    // Route::post('/vendor/stories2/add', [StoryController::class,'vaddstorestories'])->name('vendor.stories.addstore');
    // Route::get('/vendor/stories2/edit/{id}', [StoryController::class,'veditstories'])->name('vendor.stories.edit');
    // Route::post('/vendor/stories2/edit/{id}', [StoryController::class,'vstorestories'])->name('vendor.stories.store');
    // Route::get('/vendor/stories2/delete/{id}', [StoryController::class,'vdeletestories'])->name('vendor.stories.delete');
    // Route::get('/vendor/stories2/story/{id}/add/', [StoryController::class,'vstoriesadd'])->name('vendor.stories.story.add');
    // Route::post('/vendor/stories2/story/{id}/add/', [StoryController::class,'vstoriesstore'])->name('vendor.stories.story.store');
    // Route::get('/vendor/stories2/story/{id}/edit/{sid}', [StoryController::class,'vstoriesedit'])->name('vendor.stories.story.edit');
    // Route::post('/vendor/stories2/story/{id}/edit/{sid}', [StoryController::class,'vstoriesupdate'])->name('vendor.stories.story.update');
    // Route::get('/vendor/stories2/story/{id}/delete/{sid}', [StoryController::class,'vstoriesdelete'])->name('vendor.stories.story.delete');

    Route::get('/vendor/chat/', [ChatController::class,'vindex'])->name('vendor.chat.index');

    Route::get('/vendor/notifications', [NotificationController::class,'vindex'])->name('vendor.notifications.index');
    Route::get('/vendor/notifications/add', [NotificationController::class,'vaddNotification'])->name('vendor.notifications.add');
    Route::post('/vendor/notifications/add', [NotificationController::class,'vstoreNotification'])->name('vendor.notifications.store');
    Route::get('/vendor/notifications/view/{id}', [NotificationController::class,'viewNotification'])->name('vendor.notifications.view');
    Route::get('/vendor/notifications/edit/{id}', [NotificationController::class,'veditNotification'])->name('vendor.notifications.edit');
    Route::post('/vendor/notifications/edit/{id}', [NotificationController::class,'vupdateNotification'])->name('vendor.notifications.update');
    Route::get('/vendor/notifications/delete/{id}', [NotificationController::class,'vdeleteNotification'])->name('vendor.notifications.delete');

    Route::get('/vendor/ads', [AdsController::class,'vindex'])->name('vendor.ads.index');
    Route::get('/vendor/ads/{id}/view', [AdsController::class,'vsingle'])->name('vendor.ads.view');
    Route::get('/vendor/ads/{id}/add', [AdsController::class,'vaddSingleAds'])->name('vendor.ads.single.add');
    Route::post('/vendor/ads/{id}/add', [AdsController::class,'vstoreSingleAds'])->name('vendor.ads.single.store');
    Route::get('/vendor/ads/{id}/edit/{sid}', [AdsController::class,'veditSingleAds'])->name('vendor.ads.single.edit');
    Route::post('/vendor/ads/{id}/edit/{sid}', [AdsController::class,'vupdateSingleAds'])->name('vendor.ads.single.update');
    Route::get('/vendor/ads/{id}/delete/{sid}', [AdsController::class,'vdeleteSingleAds'])->name('vendor.ads.single.delete');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class,'index'])->name('profile.dashboard');
    
    Route::get('/profile/edit', [ProfileController::class,'edit'])->name('profile.edit');
    Route::post('/profile/store', [ProfileController::class,'store'])->name('profile.store');

    Route::get('/profile/orders', [ProfileController::class,'orders'])->name('profile.orders.index');
    Route::get('/profile/orders/{id}', [ProfileController::class,'singleorder'])->name('profile.orders.single');
    Route::get('/profile/orders/{id}/track', [ProfileController::class,'singleordertrack'])->name('profile.orders.track');
    Route::get('/profile/orders/{id}/support', [ProfileController::class,'singleordersupport'])->name('profile.orders.support');
    Route::post('/profile/orders/{id}/support', [ProfileController::class,'addordersupport'])->name('profile.orders.support.create');
    
    Route::get('/profile/address', [ProfileController::class,'address'])->name('profile.address');

    Route::get('/profile/tickets', [ProfileController::class,'tickets'])->name('profile.ticket.index');
    Route::get('/profile/tickets/{id}', [ProfileController::class,'singletickets'])->name('profile.ticket.single');
    Route::post('/profile/tickets/{id}', [ProfileController::class,'addsingletickets'])->name('profile.ticket.store');

    Route::post('/product/{id}/add-comment', [ProductController::class,'addComment'])->name('product.comment');
});

Route::get('/test-mail', [HomeController::class,'testmail'])->name('test.mail'); //

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('accesslogin/{driver}', [SocialiteLoginController::class, 'redirectToProvider'])->name('social.login');
Route::get('{driver}/callback', [SocialiteLoginController::class, 'handleProviderCallback']);

Route::get('/generateProducts', [HomeController::class,'generateProductImage']);
Route::get('/regjistrim-dyqani', [HomeController::class,'vendor'])->name('home.vendor');
Route::post('/regjistrim-dyqani', [HomeController::class,'vendorRequest'])->name('home.vendor.register');

Route::get('/imageview/{image}', [ProductController::class,'singleimage'])->name('single.image');
Route::get('/adsview/{image}', [ProductController::class,'adsimage'])->name('single.ads');
Route::get('/viewstory/{id}', [StoryController::class,'viewstory'])->name('story.link');
Route::get('/viewads/{id}', [AdsController::class,'viewads'])->name('ads.link');

Route::get('/register', [HomeController::class, 'register'])->name('view.register');
Route::post('/register', [HomeController::class, 'registerStore'])->name('register.store');
Route::get('/kontaktoni', [HomeController::class, 'contactus'])->name('view.contact');
Route::post('/kontaktoni', [HomeController::class, 'contactusPost'])->name('submit.contact');
Route::get('/cart', [CartController::class, 'viewcart'])->name('view.cart');
Route::get('/checkout', [CartController::class, 'checkout'])->name('view.checkout');
Route::post('/checkout', [CartController::class, 'checkoutpost'])->name('view.checkout.submit');
Route::get('/wishlist', [HomeController::class, 'wishlist'])->name('view.wishlist');
Route::post('/upload/image', [ImageController::class, 'image'])->name('upload.image');
Route::post('/upload/image/remove', [ImageController::class, 'remove'])->name('upload.remove');
Route::get('/trackorder', [CartController::class, 'trackorder'])->name('view.track');
Route::post('/trackorders', [CartController::class, 'trackorderpost'])->name('view.track.post');
Route::post('/search', [CategoryController::class,'searchpost'])->name('search.post');
Route::get('/search/{query}', [CategoryController::class,'search'])->name('search.single');
Route::get('/category/{slug}', [CategoryController::class,'category'])->name('category.single');
Route::get('/tag/{slug}', [CategoryController::class,'tag'])->name('tag.single');
Route::get('/brand/{slug}', [CategoryController::class,'brand'])->name('brand.single');
Route::get('/offer/{id}', [CategoryController::class,'offer'])->name('offer.single');
Route::get('/page/{slug}', [PageController::class,'single'])->name('pages.single');
Route::get('/{vslug}', [ProfileController::class,'vendor'])->name('single.vendor');
Route::get('/{vslug}/{id}', [ProductController::class,'index'])->name('single.product'); // ProductPrice