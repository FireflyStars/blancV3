<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderListController;
use App\Http\Controllers\SearchController;
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
Route::post('/register',[LoginController::class, 'register'])->name('register');
Route::get('/logout',[LoginController::class, 'logout'])->name('logout');
Route::post('/auth/login',function () {
    return view('welcome');
})->name('login');
Route::post('/getorderlist',[OrderListController::class, 'getorderlist'])->middleware('auth')->name('orderlist');
Route::post('/cancelorders',[OrderListController::class, 'cancelorders'])->middleware('auth')->name('cancelorders');
Route::post('/markaslate',[OrderListController::class, 'markaslate'])->middleware('auth')->name('markaslate');
Route::post('/getorderdetail',[OrderListController::class,'getorderdetail'])->middleware('auth')->name('getorderdetail');
Route::post('/suggestdate',[OrderListController::class,'suggestdate'])->middleware('auth')->name('suggestdate');
Route::post('/newdeliverydate',[OrderListController::class,'newdeliverydate'])->middleware('auth')->name('newdeliverydate');
Route::get('{any}', function () {
    return view('welcome');
})->where('any','.*');
Route::post('/SearchCustomerByOrder', [SearchController::class, 'SearchCustomerByOrder'])->name('SearchCustomerByOrder');
Route::post('/SearchCustomerByName', [SearchController::class, 'SearchCustomerByName'])->name('SearchCustomerByName');
Route::post('/SearchCustomerByEmail', [SearchController::class, 'SearchCustomerByEmail'])->name('SearchCustomerByEmail');