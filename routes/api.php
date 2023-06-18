<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/v1/m_config', [ApiController::class, 'config']);
Route::get('/v1/home_slider', [ApiController::class, 'homeSlider']);
Route::get('/v1/all_categories', [ApiController::class, 'allCategories']);
Route::get('/v1/all_categories_slider/{id}', [ApiController::class, 'allCategoriesSliders']);
Route::get('/v1/home_categories', [ApiController::class, 'homeCategories']);
Route::get('/v1/home_categories_product', [ApiController::class, 'homeCategoriesProduct']);
Route::get('/v1/product/{id}', [ApiController::class, 'singleProduct']);
Route::get('/v1/product_vendor/{id}', [ApiController::class, 'singleProductsVendor']);
Route::get('/v1/product_other/{id}', [ApiController::class, 'singleProductSimilar']);
Route::get('/v1/all_products/{page}/{cat}', [ApiController::class, 'allProducts']);
Route::get('/v1/searchs/{cat}/{page}', [ApiController::class, 'searchProducts']);
Route::get('/v1/get_countries', [ApiController::class, 'getCountriesList']);
Route::get('/v1/get_shcountries', [ApiController::class, 'getshCountriesList']);
Route::get('/v1/get_cities/{id}', [ApiController::class, 'getCitiesList']);
Route::get('/v1/get_stories', [ApiController::class, 'getStories']);
Route::get('/v1/get_category/{id}', [ApiController::class, 'getCategoryInfo']);
Route::post('/v1/scancode', [ApiController::class, 'scanQrCode']);
Route::get('/v1/get_offer/{id}', [ApiController::class, 'getOffer']);
Route::get('/v1/getStoryLink/{id}', [ApiController::class, 'getStoryLink']);

Route::post('/v1/get_cart', [CartController::class, 'getCarts']);
Route::post('/v1/get_checkout', [CartController::class, 'getCheckout']);
Route::post('/v1/get_direct_checkout', [CartController::class, 'getDirectCheckout']);
Route::post('/v1/checkout_order', [CartController::class, 'checkoutOrder'])->middleware('check_auth');
Route::post('/v1/checkout_direct_order', [CartController::class, 'checkoutDirectOrder'])->middleware('check_auth');

Route::post('/v1/count_chats', [ChatController::class, 'countChats'])->middleware('check_auth');
Route::post('/v1/get_chats', [ChatController::class, 'getChats'])->middleware('check_auth');
Route::post('/v1/get_vchats', [ChatController::class, 'getVChats'])->middleware('check_auth');
Route::post('/v1/get_chat_messages', [ChatController::class, 'getChatMessages'])->middleware('check_auth');
Route::post('/v1/get_single_chat_messages', [ChatController::class, 'getSingleChatMessages'])->middleware('check_auth');
Route::post('/v1/get_single_vchat_messages', [ChatController::class, 'getSingleVChatMessages'])->middleware('check_auth');
Route::post('/v1/send_message', [ChatController::class, 'sendMessage'])->middleware('check_auth');
Route::post('/v1/send_single_message', [ChatController::class, 'sendSingleMessage'])->middleware('check_auth');
Route::post('/v1/send_vsingle_message', [ChatController::class, 'sendVSingleMessage'])->middleware('check_auth');
Route::post('/v1/mchange_status', [ChatController::class, 'mchangeStatus'])->middleware('check_auth');

Route::get('/v1/get_help_page', [ApiController::class, 'helpPage']);
Route::get('/v1/get_help_vendor', [ApiController::class, 'helpVendor']);
Route::post('/v1/contact_us', [FormController::class, 'contactUs'])->middleware('custom_auth');
Route::post('/v1/contact_us', [FormController::class, 'contactUs'])->middleware('custom_auth');
Route::post('/v1/report_product', [FormController::class, 'reportProduct'])->middleware('check_auth');
// User
Route::post('/v1/user_profile', [FormController::class, 'userProfile'])->middleware('custom_auth');
Route::post('/v1/sync_user', [UserController::class, 'syncUser'])->middleware('check_auth');
Route::post('/v1/sync_data', [UserController::class, 'syncData'])->middleware('custom_auth');
Route::post('/v1/sync_cart', [UserController::class, 'syncCart'])->middleware('custom_auth');
Route::post('/v1/sync_wishlist', [UserController::class, 'syncWishlist'])->middleware('custom_auth');
Route::post('/v1/login_user', [ApiController::class, 'loginUser']);
Route::get('/v1/token/validate', [ApiController::class, 'tokenValidate'])->middleware('custom_auth');
Route::post('/v1/token/validate', [ApiController::class, 'tokenValidate'])->middleware('custom_auth');
Route::post('/v1/logoutUser', [ApiController::class, 'logoutUser'])->middleware('custom_auth');
Route::post('/v1/wishlists', [ApiController::class, 'getWishList']);

Route::post('/v1/change_password', [UserController::class, 'changePassword'])->middleware('custom_auth');
Route::post('/v1/forget_password', [UserController::class, 'forgetPassword'])->middleware('check_auth');
Route::post('/v1/register_user', [UserController::class, 'registerUser'])->middleware('check_auth');
Route::get('/v1/get_user_info', [UserController::class, 'getUserInfo'])->middleware('custom_auth');
Route::post('/v1/get_user_info', [UserController::class, 'setUserInfo'])->middleware('custom_auth');
Route::get('/v1/get_orders', [UserController::class, 'getOrders'])->middleware('custom_auth');
Route::get('/v1/get_order/{id}', [UserController::class, 'getOrderDetail'])->middleware('custom_auth');
Route::get('/v1/get_tickets', [UserController::class, 'getTickets'])->middleware('custom_auth');
Route::get('/v1/get_ticket_order/{id}', [UserController::class, 'getOrderTicketDetail'])->middleware('custom_auth');
Route::get('/v1/get_ticket/{id}', [UserController::class, 'getTicketDetail'])->middleware('custom_auth');
Route::post('/v1/new_ticket/{id}', [UserController::class, 'newTicket'])->middleware('custom_auth');
Route::post('/v1/edit_ticket/{id}', [UserController::class, 'addTicketDetail'])->middleware('custom_auth');
Route::get('/v1/get_addresses', [UserController::class, 'getAddresses'])->middleware('custom_auth');
Route::post('/v1/get_uaddress', [UserController::class, 'getUAddresses'])->middleware('custom_auth');
Route::get('/v1/get_address/{id}', [UserController::class, 'getAddressDetail'])->middleware('custom_auth');
Route::post('/v1/primary_address/{id}', [UserController::class, 'primaryAddress'])->middleware('custom_auth');
Route::post('/v1/add_address', [UserController::class, 'addAddress'])->middleware('custom_auth');
Route::post('/v1/edit_address/{id}', [UserController::class, 'editAddress'])->middleware('custom_auth');
Route::post('/v1/delete_address/{id}', [UserController::class, 'deleteAddress'])->middleware('custom_auth');

// Vendor
Route::get('/v1/get_vendor', [ApiController::class, 'getVendor'])->middleware('custom_auth');
Route::get('/v1/get_fvendor', [ApiController::class, 'getfVendor'])->middleware('custom_auth');
Route::get('/v1/get_products', [ApiController::class, 'getProducts'])->middleware('custom_auth');
Route::get('/v1/get_vendor_info/{id}', [ApiController::class, 'getVendorInfo']);
Route::get('/v1/get_vendor_products/{oid}/{id}', [ApiController::class, 'getVendorProducts']);
Route::get('/v1/get_vendor_category/{id}/{vid}', [ApiController::class, 'getVendorCategoryInfo']);

Route::get('/v1/get_vorders', [VendorController::class, 'getVOrders'])->middleware('custom_auth');
Route::get('/v1/get_vorder/{id}', [VendorController::class, 'getVOrderDetail'])->middleware('custom_auth');
Route::post('/v1/change_vorder/{id}', [VendorController::class, 'changeVOrder'])->middleware('custom_auth');
Route::post('/v1/track_vorder/{id}', [VendorController::class, 'trackVOrder'])->middleware('custom_auth');
Route::get('/v1/get_vtickets', [VendorController::class, 'getTickets'])->middleware('custom_auth');
Route::get('/v1/get_vticket/{id}', [VendorController::class, 'getTicketDetail'])->middleware('custom_auth');
Route::post('/v1/edit_vticket/{id}', [VendorController::class, 'addTicketDetail'])->middleware('custom_auth');
Route::post('/v1/refund_vticket/{id}', [VendorController::class, 'refundTicketDetail'])->middleware('custom_auth');
Route::get('/v1/get_vendor_settings', [VendorController::class, 'getVendorSettings'])->middleware('custom_auth');
Route::post('/v1/get_vendor_settings', [VendorController::class, 'saveVendorSettings'])->middleware('custom_auth');
Route::get('/v1/get_product_single/{id}', [VendorController::class, 'getProduct'])->middleware('custom_auth');
Route::post('/v1/get_product_single/{id}', [VendorController::class, 'saveProduct'])->middleware('custom_auth');

Route::get('/v1/get_vstories', [VendorController::class, 'getVStories'])->middleware('custom_auth');
Route::get('/v1/get_vstory/{id}', [VendorController::class, 'getVStoryDetail'])->middleware('custom_auth');
Route::post('/v1/add_vstory/{id}', [VendorController::class, 'addVStory'])->middleware('custom_auth');
Route::get('/v1/get_vstory_item/{id}', [VendorController::class, 'getVItemDetail'])->middleware('custom_auth');
Route::post('/v1/get_vstory_item/{id}', [VendorController::class, 'postVItemDetail'])->middleware('custom_auth');
Route::post('/v1/add_vstory_item/{id}', [VendorController::class, 'postVAddItemDetail'])->middleware('custom_auth');
Route::post('/v1/delete_vstory_item/{id}', [VendorController::class, 'deleteVItemDetail'])->middleware('custom_auth');

Route::get('/v1/get_voffers', [VendorController::class, 'getVOffers'])->middleware('custom_auth');
Route::get('/v1/get_vsoffers/{id}', [VendorController::class, 'getVSingleOffers'])->middleware('custom_auth');
Route::post('/v1/get_vsoffers/{id}', [VendorController::class, 'postVSingleOffers'])->middleware('custom_auth');
Route::get('/v1/add_vsoffers/{id}', [VendorController::class, 'addVSingleOffers'])->middleware('custom_auth');
Route::post('/v1/add_vsoffers/{id}', [VendorController::class, 'storeVSingleOffers'])->middleware('custom_auth');
Route::post('/v1/delete_vsoffers/{id}', [VendorController::class, 'deleteVSingleOffers'])->middleware('custom_auth');

Route::get('/v1/get_vcoupons', [VendorController::class, 'getVCoupons'])->middleware('custom_auth');
Route::get('/v1/get_vcoupon/{id}', [VendorController::class, 'getVScoupon'])->middleware('custom_auth');
Route::post('/v1/get_vcoupon/{id}', [VendorController::class, 'postVScoupon'])->middleware('custom_auth');
Route::get('/v1/add_vcoupon/{id}', [VendorController::class, 'addVScoupon'])->middleware('custom_auth');
Route::post('/v1/add_vcoupon', [VendorController::class, 'storeVScoupon'])->middleware('custom_auth');
Route::post('/v1/delete_vcoupon/{id}', [VendorController::class, 'deleteVScoupon'])->middleware('custom_auth');