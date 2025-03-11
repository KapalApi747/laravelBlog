<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.home');
});

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::group(['prefix' => 'backend', 'middleware' => ['auth', 'verified', 'admin']], function () {
    Route::resource('/users', UserController::class);
    Route::patch('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

    Route::resource('/categories', CategoryController::class);
    Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/categories/{id}/forceDelete', [CategoryController::class, 'forceDelete'])->name('categories.forceDelete');
});

Route::group(['prefix' => 'backend', 'middleware' => ['auth', 'verified']], function () {
    Route::resource('/posts', PostController::class)->scoped(['post' => 'slug']);
});

Route::get('/backend', function () {
    return view('backend.backend');
})->middleware(['auth', 'verified'])->name('backendindex');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
