<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::middleware(['middleware', 'auth'])->group(function () {
//     Route::resource('events', EventController::class);
// });
Route::middleware(['auth'])->group(function () {
    // Route::resource('events', EventController::class);
    Route::resource('events', EventController::class);
    Route::get('refetchEvents', [\App\Http\Controllers\EventController::class, 'refetchEvents'])->name('refetch-events');


});

