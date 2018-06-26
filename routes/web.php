<?php

/*postgres://kslbwmltwtecdh:2e3bcb2ead3e011b1ce8abef35c344bbd9f32f0ba8394ecaafc637e8e651c824@ec2-54-235-75-214.compute-1.amazonaws.com:5432/dbhq70epkn68cm
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// user registration
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.post');
Route::get('logout', 'Auth\LoginController@logout')->name('logout.get');

Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup.get');
Route::post('signup', 'Auth\RegisterController@register')->name('signup.post');

Route::get('/', function () {return view('welcome');});
Route::get('/', 'MicropostsController@index');
Route::resource('microposts', 'MicropostsController');


Route::group(['middleware' => 'auth'], function () {
Route::resource('users', 'UsersController', ['only' => ['index', 'show']]);

Route::group(['prefix' => 'users/{id}'], function () {
Route::post('follow', 'UserFollowController@store')->name('user.follow');
Route::delete('unfollow', 'UserFollowController@destroy')->name('user.unfollow');
Route::get('followings', 'UsersController@followings')->name('users.followings');
Route::get('followers', 'UsersController@followers')->name('users.followers');
    });
    
Route::group(['prefix' => 'users/{id}'], function () {
Route::post('pushfavorite', 'FavoriteController@store')->name('micropost.pushfavorite');
Route::delete('drawfavorite', 'FavoriteController@destroy')->name('micropost.drawfavorite');
Route::get('favoritepost', 'UsersController@favoritepost')->name('micropost.favoritepost');
});

Route::resource('microposts', 'MicropostsController', ['only' => ['store', 'destroy']]);

    });