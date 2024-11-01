<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;



Route::get('/', [MainController::class, 'index']);
Route::post('/category/store', [MainController::class, 'store_category'])->name('category.store');
