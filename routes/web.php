<?php

use App\Models\Country;
use App\Models\Coupon;
use App\Models\ScratchGame;
use App\Models\ScratchGameUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


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
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('test', function(){
    $countries = Country::get();
    foreach($countries as $country){
            Coupon::where('country_id', $country->id)
                    ->where('expiration_date', '<', Carbon::today($country->timezone))
                    ->update([
                        'status' => 'expired',
                    ]);
    }
});
