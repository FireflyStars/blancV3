<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderListController;
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


Route::post('/authenticate',[LoginController::class, 'authenticate'])->name('authenticate');
Route::get('/logout',[LoginController::class, 'logout'])->name('logout');
Route::post('/auth/login',function () {
    return view('welcome');
})->name('login');
Route::post('/getorderlist',[OrderListController::class, 'getorderlist'])->middleware('auth')->name('orderlist');
Route::get('{any}', function () {
    return view('welcome');
})->where('any','.*');