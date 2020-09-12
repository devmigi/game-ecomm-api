<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Version 1 (V1) - API Routes
|--------------------------------------------------------------------------
|namespace: Api\V1
|middleware: api
|prefix: api/v1/
*/

Route::prefix('v1')->namespace('V1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Customer Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        // get current user profile
        Route::get('/profile', 'UserController@profile');

        // log out user
        Route::get('/logout', 'UserController@logout');


        // get user reviews
        Route::get('/reviews', 'ReviewController@userReviews');

        // add new review
        Route::post('/reviews/add', 'ReviewController@add');

        // update a review
        Route::post('/reviews/{review}', 'ReviewController@update');


        // get user wishlists
        Route::get('/wishlists', 'WishlistController@all');

        // add to user wishlists
        Route::post('/wishlists/add', 'WishlistController@add');

        // remove from user wishlists
        Route::post('/wishlists/remove', 'WishlistController@remove');


        // get user addresses
        Route::get('/addresses', 'AddressController@all');

        // add user address
        Route::post('/addresses/add', 'AddressController@add');

        // remove user address
        Route::post('/addresses/remove', 'AddressController@remove');

    });



    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */

    // login user
    Route::post('/login', 'UserController@login');

    // login user
    Route::post('/login/{provider}', 'UserController@loginWithSociaAccount');

    // signup user
    Route::post('/register', 'UserController@register');


    // APP: homepage
    Route::get('/pages/{page}', 'PageController@load');

    // get all categories
    Route::get('/categories', 'CategoryController@index');

    // get category products
    Route::get('/categories/{id}/products', 'CategoryController@products');

    // get all products
    Route::get('/products', 'ProductController@index');

    // get product details
    Route::get('/products/{id}', 'ProductController@show');

    // get product reviews
    Route::get('/products/{productId}/reviews', 'ReviewController@productReviews');



    // search products
    Route::get('/search/{key}', 'SearchController@index');


    // search products
    Route::get('/pincodes/{pincode}', 'AddressController@pincodeCities');


    // proceed to paytm payment
    Route::get('/payment/initiate/{orderId}', 'PaymentController@initiate');

    // handle payment gateway callback
    Route::post('/payment/callback', 'PaymentController@callback');

});


