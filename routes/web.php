<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

Route::get('/auth/{provider}/redirect', function ($provider) {
    return Socialite::driver($provider)->redirect();
})->name('social.login');

Route::get('/auth/{provider}/callback', function ($provider) {
    $providerUser = Socialite::driver($provider)->stateless()->user();

    $user = User::firstOrCreate(['email' => $providerUser->getEmail()], [
        "name"          =>  $providerUser->getName() ?? $providerUser->getNickname,
        "provider_id"   =>  $providerUser->getId(),
        "provider"      =>  $provider
    ]);

    Auth::login($user);

    return redirect()->route('home');
})->name('social.callback');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
