<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Koordinat;
use App\Http\Controllers\Benchmark;
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
Route::get('/benchmark',[Benchmark::class,'index']);
Route::post('/add_bm',[Benchmark::class,'store']);
Route::get('/koordinat',[Koordinat::class,'index']);
Route::post('/add_koord',[Koordinat::class,'store']);
Route::get('/', function () {
    return view('welcome');
});
