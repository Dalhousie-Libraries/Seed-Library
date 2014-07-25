<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/



/** ------------------------------------------
 *  Route model binding for session control
 *  ------------------------------------------*/
//Route::get('login', 'SessionController@create');
Route::get('login/{iframe?}', 'SessionController@create');
Route::post('login/{iframe?}', 'SessionController@store');
Route::any('logout', 'SessionController@destroy');

/** ------------------------------------------
 *  Route model binding for administrator
 *  ------------------------------------------*/
Route::group(array('prefix' => 'admin', 'before' => 'admin'), function()
{
    # Users (borrowers, donors, suppliers, returners)
    Route::get('users/{item}/edit', 'AdminUserController@getEdit');
    Route::post('users/{item}/edit', 'AdminUserController@postEdit');
    Route::get('users/{item}/delete', 'AdminUserController@delete');
    Route::any('users/delete', 'AdminUserController@deleteAll');
    Route::controller('users', 'AdminUserController');

    # Items
    Route::get('items/{item}/edit', 'AdminItemController@getEdit');
    Route::post('items/{item}/edit', 'AdminItemController@postEdit');
    Route::get('items/{item}/delete', 'AdminItemController@delete');
    Route::any('items/delete', 'AdminItemController@deleteAll');
    Route::controller('items', 'AdminItemController');

    # Donations
    Route::get('donations/{item}/edit', 'AdminDonationController@getEdit');
    Route::post('donations/{item}/edit', 'AdminDonationController@postEdit');
    Route::get('donations/{item}/check_in', 'AdminDonationController@getCheckIn');
    Route::post('donations/{item}/check_in', 'AdminDonationController@postCheckIn');
    Route::get('donations/data/donated', 'AdminDonationController@getDonatedPackets');
    Route::get('donations/data/requested', 'AdminDonationController@getRequestedPackets');
    Route::controller('donations', 'AdminDonationController');
    
    # Returns
    Route::get('returns/{item}/edit', 'AdminReturnController@getEdit');
    Route::post('returns/{item}/edit', 'AdminReturnController@postEdit');
    Route::get('returns/{item}/check_in', 'AdminReturnController@getCheckIn');
    Route::post('returns/{item}/check_in', 'AdminReturnController@postCheckIn');
    Route::get('returns/userPackets/{id}', 'AdminReturnController@getUserPackets');
    Route::get('returns/data/returned', 'AdminReturnController@getReturnedPackets');
    Route::get('returns/data/requested', 'AdminReturnController@getRequestedPackets');
    Route::controller('returns', 'AdminReturnController');
    
    # Packets
    Route::get('packets/{item}/edit/{type}', 'AdminPacketController@getEdit');
    Route::post('packets/{item}/edit/{type}', 'AdminPacketController@postEdit');
    Route::get('packets/listByName/{query?}', 'AdminPacketController@getListByName');
    Route::get('packets/data/lent', 'AdminPacketController@getLentPackets');
    Route::get('packets/data/requested', 'AdminPacketController@getRequestedPackets');
    Route::get('packets/lend', 'AdminPacketController@getSeedsList');
    Route::get('packets/{item}/lend', 'AdminPacketController@getLending');
    Route::post('packets/{item}/lend', 'AdminPacketController@postLending');
    Route::get('packets/{item}/history', 'AdminPacketController@showPacketHistory');
    Route::get('packets/{item}/delete', 'AdminPacketController@delete');
    Route::any('packets/delete', 'AdminPacketController@deleteAll');
    Route::controller('packets', 'AdminPacketController');
    
    # Images
    Route::get('images/{item}/delete', 'AdminImageController@delete');
    Route::controller('images', 'AdminImageController');

    # Index
    Route::get('/', function(){
        return View::make('admin/home');
    });
});

/** ------------------------------------------
 *  Route model binding for cms
 *  ------------------------------------------*/
Route::group(array('prefix' => 'cms', 'before' => 'admin'), function()
{
    # Postings
    Route::get('postings/{id}/edit', 'PostingController@getEdit');
    Route::post('postings/{id}/edit', 'PostingController@postEdit');
    Route::get('postings/{id}/delete', 'PostingController@delete');
    Route::get('postings/{id}/publish', 'PostingController@changePublishStatus');
    Route::controller('postings', 'PostingController');
    
    # Categories
    Route::get('categories/list', 'CategoryController@listAll');
    Route::controller('categories', 'CategoryController');

    # Index
    /*Route::get('/', function(){
        return View::make('cms/home');
    });*/
});

/** ------------------------------------------
 *  Frontend Routes
 *  ------------------------------------------
 */
# Users
Route::group(array('prefix' => 'user'), function()
{
    // Logged in users only
    Route::group(array('before' => 'auth'), function() {
        Route::get('profile', 'UserController@getIndex');
        Route::post('profile', 'UserController@postEdit');    
        Route::get('requests', 'UserController@getRequests');
    });
});

# Items
Route::group(array('prefix' => 'item'), function() 
{
    Route::get('show/{id}', 'ItemController@show');
    Route::get('basket', 'ItemController@basket');
    Route::get('search', 'ItemController@search');
    
    // Logged in users only
    Route::group(array('before' => 'auth'), function() {
        Route::get('checkout', 'ItemController@confirmCheckout');
        Route::post('checkout', 'ItemController@doCheckout');
        Route::get('postCheckout', 'ItemController@postCheckout');
    });    
    
    // JSON URLs
    Route::any('list', 'ItemController@getAll');
    Route::any('listAvail', 'ItemController@getAllAvail');
    Route::any('find/{id}', 'ItemController@getItem');
    Route::any('findByName/{name}', 'ItemController@getItemByName');
});

# Packets
Route::group(array('prefix' => 'packet'), function()
{
    Route::get('item_list/{id}', 'PacketController@getItemPackets');
    Route::get('history/{id}', 'PacketController@getPacketHistory');
    
    // Logged in users only
    Route::group(array('before' => 'auth'), function() {
        Route::any('requests/{action}/{id}', 'PacketController@getRequests');
        Route::get('{id}/delete', 'PacketController@delete');
        Route::any('delete', 'PacketController@deleteAll');
    });
});

# Donations
Route::group(array('prefix' => 'donations'), function() {
    
    // Logged in users only
    Route::group(array('before' => 'auth'), function() {
        Route::get('donate', 'DonationController@getCreate');
        Route::post('donate', 'DonationController@postCreate');
        Route::get('{id}/edit', 'DonationController@getEdit');
        Route::post('{id}/edit', 'DonationController@postEdit');
        Route::get('{id}/delete', 'DonationController@delete');
        Route::any('delete', 'DonationController@deleteAll');
    });
});

# Returns
Route::group(array('prefix' => 'returns'), function() {
    
    // Logged in users only
    Route::group(array('before' => 'auth'), function() {
        Route::get('return', 'ReturnController@getCreate');
        Route::post('return', 'ReturnController@postCreate');
        Route::get('{id}/edit', 'ReturnController@getEdit');
        Route::post('{id}/edit', 'ReturnController@postEdit');
        Route::get('{id}/delete', 'ReturnController@delete');
        Route::any('delete', 'ReturnController@deleteAll');
    });
});

# General access
Route::get('/', 'SiteController@index');
Route::get('home', 'SiteController@index');
Route::get('search/{keyword}', 'SiteController@search');
Route::get('signup', 'UserController@getCreate');
Route::post('signup', 'UserController@postCreate');
Route::get('getpass', 'UserController@getPassword');
Route::post('getpass', 'UserController@postPassword');
Route::get('activate/{token}', 'UserController@activateAccount');

# Articles
Route::group(array('prefix' => 'article'), function() {
    Route::get('{id}/show', 'SiteController@show');
    Route::get('{slug}', 'SiteController@show');
});
