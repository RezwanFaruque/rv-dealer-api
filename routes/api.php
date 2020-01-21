<?php

use App\Rv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::group(['prefix' => 'api/v1', 'middleware' => 'auth:api'], function () {

    Route::get('/cms', 'InventoryController@cms');
    Route::get('/inventory/brands', 'InventoryController@brands');
    Route::get('/inventory/models', 'InventoryController@models');
    Route::get('/inventory/types', 'InventoryController@types');
    Route::get('/inventory/brands/{record}/models','InventoryController@brandModels');
    Route::get('/inventory/types/{record}/models','InventoryController@typeModels');
    Route::get('/inventory/classifications/{record}/models','InventoryController@classificationsModels');

    Route::get('/statistics', 'StatisticController@index');
    Route::get('/statistics/{slug}', 'StatisticController@show');


});

Route::group(['prefix' => 'api/v1'], function () {

    Route::post('login','AuthController@login');

    Route::get('social','AuthController@redirectToProvider');

    Route::get('callback/{provider}','AuthController@handleProviderCallback');

    // social login routes
    Route::get('social-login','AuthController@login');


    Route::group(['middleware' => 'auth:customer-api'],function (){

        Route::post('logout','AuthController@logout');

    });


    // register user Route
    Route::post('/user-profile/register','UserProfileController@registerUser')->name('user-profile.register');
    // user profile
    Route::group(['prefix' => '/user-profile', 'middleware' => 'auth:customer-api'], function (){

        Route::get('/niriho', function(){

            return Auth::id();

        });

        Route::get('/', function(){

            return ['data'=>'User Profile'];

        });

        Route::get('/events','UserProfileController@getEvents')->name('user-profile.getevents');

        Route::post('/favorite-units','UserProfileController@createFavoriteUnits')->name('user-profile.create-favorite-units');

        Route::get('/favorite-units','UserProfileController@getFavoriteUnites')->name('user-profile.getfavorite-unites');
        
        Route::delete('/favorite-units','UserProfileController@deleteFavoriteUnit')->name('user-profile.deletefavorite-units');

        Route::put('/notfication-status','UserProfileController@changeUserNotificationStatus')->name('user-profile.change-notification-status');

        Route::get('/brought-units/{user_id}','UserProfileController@getRvByBroughtUnit')->name('user-profile.getRvByBroughtUnits');

        Route::get('/{user_id}','UserProfileController@getDashboardBasicInfo')->name('user-profile.basic-dashboard');

        Route::put('/change-password','UserProfileController@changePassword')->name('user-profile.change-password');

        

        Route::put('/user-info','UserProfileController@updateUserInfo')->name('user-profile.update-user-info');
    });

});

JsonApi::register('v1')->middleware('auth:api')->routes(function ($api) {
    $api->resource('rvs')->only('index', 'read', 'related')->relationships(function ($relations) {
        $relations->hasOne('brand')->only('index', 'read', 'related');
        $relations->hasOne('type')->only('index', 'read');
        $relations->hasOne('model')->only('index', 'read');
        $relations->hasMany('options')->only('index', 'read', 'related');
        $relations->hasMany('images')->only('related');
        $relations->hasMany('attributes')->only('index', 'read', 'related');
        $relations->hasMany('classifications');
        $relations->hasMany('documents')->only('index', 'read', 'related');
    });

    $api->resource('options')->only('index', 'read', 'related')->relationships(function ($relations) {
        $relations->hasMany('rvs')->only('index', 'read', 'related');
    });

    $api->resource('brands')->only('index', 'read', 'related')->relationships(function ($relations) {
        $relations->hasMany('rvs')->only('index', 'read', 'related');
        $relations->hasMany('models')->only('index', 'read', 'related');
    });

    $api->resource('types')->only('index', 'read', 'related')->relationships(function ($relations) {
        $relations->hasMany('rvs')->only('index', 'read', 'related');
        $relations->hasMany('models')->only('index', 'read', 'related');
    });

    $api->resource('models')->only('index', 'read', 'related')->relationships(function ($relations) {
        $relations->hasMany('rvs')->only('index', 'read', 'related');
        $relations->hasOne('brand')->only('index', 'read', 'related');
        $relations->hasMany('types')->only('index', 'read', 'related');
    });

    $api->resource('classifications')->only('index', 'read', 'related')->relationships(function ($relations) {
        $relations->hasMany('rvs')->only('index', 'read', 'related');
    });

    $api->resource('attributes')->only('index', 'read', 'related')->relationships(function ($relations) {
        $relations->hasMany('rvs')->only('index', 'read', 'related');
    });
});
