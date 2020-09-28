<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Mollie\Laravel\Facades\Mollie;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payment/oauth/return', function (\Illuminate\Http\Request  $request) {
    dd($request);
});

Route::get('mollie-login', function () {
    return Socialite::with('mollie')
        ->scopes(['profiles.read']) // Additional permission: profiles.read
        ->redirect();
});

Route::get('login_callback', function () {
    $user = Socialite::with('mollie')->user();

    Mollie::api()->setAccessToken($user->token);

    return Mollie::api()->profiles()->page(); // Retrieve payment profiles available on the obtained Mollie account
});
