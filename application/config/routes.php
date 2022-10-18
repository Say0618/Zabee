<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'home';
$route['sitemap\.xml'] = "sitemap/index";
$route['debug'] = "debug/index";
//$route['index'] = 'front/home';
$route['404_override'] = 'home/notexist';
$route['login'] = 'home/login';
$route['home/login'] = 'home/login';
$route['accounts/signin'] = 'home/login';
$route['logout'] = 'home/logout';
$route['change_password'] = "home/change_password";
$route['order_complete'] = "home/order_complete";
$route['product/review'] = 'product/review';
$route['product/saveMessage'] = 'product/saveMessage';
$route['message'] = 'message';

$route['seller/product/add'] = 'seller/product/addProduct/';

$route['checkout/proceed'] = "checkout/index";
$route['checkout/payment'] = "checkout/payment";
$route['checkout/payment/(:any)'] = "checkout/payment/$1";
$route['checkout/proceed_payment'] = "checkout/proceed_payment";

$route['cart'] = 'cart';
$route['buynow/(:any)'] = 'cart/buynow/$1';
$route['buynow/(:any)/(:any)'] = 'cart/buynow/$1/$2';
$route['checkout'] = 'checkout';

$route['seller'] = "seller/login";
$route['seller/(:any)'] = "seller/$1";
$route['seller/sales/expired'] = "seller/sales/index/expired";
$route['seller/profile/update'] = "seller/profile/index";
$route['seller/(:any)/(:any)'] = "seller/$1/$2";
$route['seller/(:any)/(:any)/(:any)'] = "seller/$1/$2/$3";
$route['seller/categories/(:num)'] = "seller/categories/index/$1";

/* UPDATE STORE STATUS */

// $route['seller/store/update_store_status'] = "seller/store/update_store_status";

/* Discount Vouchers */
$route['seller/discount/voucher/delete/(:num)'] = "seller/discount/delete_voucher/$1";
$route['seller/discount/voucher/update/(:num)'] = "seller/discount/voucher_edit/$1";

//$route['warehouse'] = "warehouse/login";
$route['warehouse'] = "warehouse/dashboard";
$route['warehouse/product_list'] = "warehouse/dashboard/product_list";
$route['warehouse/create'] = "warehouse/dashboard/create";
$route['warehouse/create/(:num)'] = "warehouse/dashboard/create/$1";
$route['warehouse/update/(:num)'] = "warehouse/dashboard/create/$1";
$route['warehouse/inventory_view'] = "warehouse/dashboard/inventory_view";
/*$route['warehouse/(:any)'] = "warehouse/$1";
$route['warehouse/profile/update'] = "warehouse/profile/index";
$route['warehouse/(:any)/(:any)'] = "warehouse/$1/$2";
$route['warehouse/(:any)/(:any)/(:any)'] = "warehouse/$1/$2/$3";*/

$route['custom/(:any)'] = "custom/$1";
$route['custom/(:any)/(:any)'] = "custom/$1/$2";
$route['custom/(:any)/(:any)/(:any)'] = "custom/$1/$2/$3";
$route['custom/(:any)/(:any)/(:any)/(:any)'] = "custom/$1/$2/$3/$4";
$route['custom/(:any)/(:any)/(:any)/(:any)/(:any)'] = "custom/$1/$2/$3/$4/$5";
$route['custom/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'] = "custom/$1/$2/$3/$4/$5/$6";

$route['product/page/(:any)'] = 'product/viewLatestProducts/$1';
//$route['product/(:any)'] = 'product/productOfferListing/$1';
//Comment this two route for test new query.
//$route['product/detail/(:any)'] = 'product/viewProduct/$1';
//$route['product/detail/(:any)/(:any)'] = 'product/viewProduct/$1/$2';
$route['product/offerlisting/(:any)'] = 'product/productOfferListing/$1';
$route['product/getProductData'] = "product/getProductData";
$route['product/saveProductHistory'] = "product/saveProductHistory";
$route['product/viewLatestProducts'] = 'product/viewLatestProducts';
$route['product/ajax_Latest'] = 'product/ajax_Latest';
$route['product/ajax_Latest/(:any)'] = 'product/ajax_Latest/$1';

$route['product/viewFeaturedProducts'] = 'product/viewFeaturedProducts';
$route['product/ajax_Featured'] = 'product/ajax_Featured';
$route['product/ajax_Featured/(:any)'] = 'product/ajax_Featured/$1';

//$route['product/SearchResults'] = 'product/SearchResults';
$route['product/ajax_SearchResults'] = 'product/ajax_SearchResults';
$route['product/ajax_SearchResults/(:any)'] = 'product/ajax_SearchResults/$1';

$route['product/viewDiscountProducts'] = 'product/viewDiscountProducts';
$route['product/ajax_Discount'] = 'product/ajax_Discount';
$route['product/ajax_Discount/(:any)'] = 'product/ajax_Discount/$1';
$route['product/delete_from_list'] = "product/delete_from_list";

$route['categories/(:num)'] = 'categories/viewCategoryProducts/$1';
$route['subcategories/(:num)/(:num)'] = 'subcategories/viewSubcategoryProducts/$1/$2';

$route['account'] = "buyer";
$route['billing'] = "buyer/billing_system";
$route['account/saved_cards'] = "buyer/buyer_cards";
$route['account/delete_card/(:num)'] = "buyer/delete_card/$1";

$route['shipping'] = "buyer/address_book";
$route['shipping/add'] = "buyer/address_book_add";
$route['shipping/add/(:num)'] = "buyer/address_book_add/$1";
$route['shipping/add/(:num)/(:num)'] = "buyer/address_book_add/$1/$2";
$route['shipping/edit/(:num)'] = "buyer/address_book_update/$1";
$route['orders'] = "buyer/buyer_order";
$route['orders/(:num)'] = "buyer/buyer_order/$1";
$route['wish_list'] = "product/saved_for_later";
$route['contact_us'] = "home/contact_us";
$route['invite'] = "referral/Referrals/invite";
$route['translate_uri_dashes'] = FALSE;
$route['seller/Userdetails/show/(:any)'] = 'seller/Userdetails/show/$1';
$route['seller/Userdetails/get_product_details/(:any)'] = 'seller/Userdetails/get_product_details/$1';
$route['seller/Userdetails/get_buyer_orders/(:any)'] = 'seller/Userdetails/get_buyer_orders/$1';
$route['seller/Userdetails/buyer_order_history/(:any)'] = 'seller/Userdetails/buyer_order_history/$1';
$route['seller_notifications'] = 'seller/dashboard/notification_status';

/*Front*/
$route['join_us'] = 'home/join_us';
$route['reset/(:any)'] = 'home/reset/$1';
$route['forgotpassword'] = 'home/forgotpassword';
$route['checkout/signin'] = 'home/checkout_not_loggedIn';
$route['seller_info/(:any)'] = 'home/seller_info/$1';
$route['store/(:any)'] = 'home/store/$1';
$route['checkout/guest'] = 'home/checkout_as_guest';
$route['privacypolicy'] = 'home/privacypolicy';
$route['termsandcondition'] = 'home/termsandcondition';
$route['minicart'] = 'home/minicart';
$route['saleView/(:num)'] = 'seller/sales/saleView/$1';
$route['grocery-sales-partner'] = 'home/grocerySalesPartner';
$route['affiliate'] = "referral/affiliate/affiliate_user";
$route['user_affiliation'] = 'referral/affiliate/user_affiliation';
$route['delete_wishlist_category'] = "product/delete_wishlist_category";
$route['product/product_acc'] = "product/product_acc";

$route['order_mobile'] = "home/orderListMobile";

/*Api*/
$route['api/getProductDetails'] = "api/getProductDetails";
$route['api/getProductData'] = "api/getProductData";
$route['api/searchResults'] = "api/searchResults";
$route['api/seller_info'] = "api/seller_info";
$route['api/getCategory'] = "api/getCategory";
$route['api/offerlisting'] = "api/offerlisting";
$route['api/addtocart'] = "api/addtocart";
$route['api/getDefault'] = "api/getDefault";
$route['api/getTaxRate']  ="api/getTaxRate";
$route['api/proceed_payment'] = "api/proceed_payment";
$route['api/login'] = "api/login";
$route['api/signup'] = "api/signup";
$route['api/cartExists'] = "api/cartExists";
$route['api/changePassword'] = "api/changePassword";

$route['save_subscription'] = 'home/save_subscription';
$route['dealsSubscription/no'] = 'home/disable_subscription';

$route['product'] = 'product/searchResults';
$route['product/listview_pagination'] = 'product/listview_pagination';
$route['product/updateView'] = "product/updateView";
$route['product/save_for_later_2'] = 'product/save_for_later_2';
$route['product/saved_for_later/(:any)'] = "product/saved_for_later/$1";
$route['(:any)'] = 'product/searchResults/$1';
$route['product/qna'] = "product/qna";
$route['product/(:any)'] = 'product/productDetails/$1';
$route['product/(:any)/(:num)'] = 'product/productDetails/$1/$2';
$route['callbacks/get_state'] = "welcome/getState";
$route['callbacks/sign_in_with_apple'] = "welcome/sign_in_with_apple";


/*Music Pillars*/
$route['mp/(:num)/(:num)/(:any)'] = "musicpillars/index/$1/$2/$3";
