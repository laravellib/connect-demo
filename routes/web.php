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

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

/**
 * MOLLIE...
 */
Route::middleware('auth')->group(function () {
    Route::get('mollie-login', function () {
        return Socialite::with('mollie')
            ->scopes(['profiles.read']) // Additional permission: profiles.read
            ->redirect();
    });

    Route::middleware('auth')->get('/payment/oauth/return', function (\Illuminate\Http\Request $request) {
        $mollieUser = Socialite::with('mollie')->user();

        $localUser = $request->user();
        $localUser->currentTeam->forceFill([
            'access_token' => $mollieUser->token,
            'access_token_expires_at' => now()->addSeconds($mollieUser->expiresIn),
            'refresh_token' => $mollieUser->refreshToken,
        ])->save();

        Mollie::api()->setAccessToken($mollieUser->token);

        return redirect(route('dashboard'));
    });
});
