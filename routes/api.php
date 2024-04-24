<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProgramController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::post('gpt', 'gpt')->name('gpt');
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('{user}', 'show')->name('show');
        Route::patch('{user}', 'update')->name('update');
        Route::delete('{user}', 'destroy')->name('destroy');
    });
});

Route::controller(ChallengeController::class)->group(function () {
    Route::prefix('challenges')->name('challenges.')->group(function () {
        Route::post('gpt', 'gpt')->name('gpt');
        Route::get('/', 'index')->name('index');
        Route::post('{user}', 'store')->name('store');
        Route::get('{challenge}', 'show')->name('show');
        Route::patch('{challenge}', 'update')->name('update');
        Route::delete('{challenge}', 'destroy')->name('destroy');
    });
});

Route::controller(CompanyController::class)->group(function () {
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::post('gpt', 'gpt')->name('gpt');
        Route::get('/', 'index')->name('index');
        Route::post('{user}', 'store')->name('store');
        Route::get('{company}', 'show')->name('show');
        Route::patch('{company}', 'update')->name('update');
        Route::delete('{company}', 'destroy')->name('destroy');
    });
});

Route::controller(ProgramController::class)->group(function () {
    Route::prefix('programs')->name('programs.')->group(function () {
        Route::post('gpt', 'gpt')->name('gpt');
        Route::get('/', 'index')->name('index');
        Route::post('{user}', 'store')->name('store');
        Route::get('{program}', 'show')->name('show');
        Route::patch('{program}', 'update')->name('update');
        Route::delete('{program}', 'destroy')->name('destroy');
    });
});