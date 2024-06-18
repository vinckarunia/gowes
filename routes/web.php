<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BicycleController;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/bicycle', function () {
    return view('pages.plp');
})->name('plp');

Route::get('/bicycle/{i}', function () {
    return view('pages.pdp');
})->name('pdp');