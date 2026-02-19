<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InfografisController;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('infografis', InfografisController::class);
});

