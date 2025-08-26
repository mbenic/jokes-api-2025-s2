<?php

use App\Http\Controllers\Api\v1\AuthController as AuthControllerV1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\v2\CategoryController;
use App\Http\Controllers\Api\v2\JokeController;

/**
 * API Version 2 Routes
 */

/**
 * User API Routes
 * - Register, Login (no authentication)
 * - Profile, Logout, User details (authentication required)
 */

Route::prefix('auth')
    ->group(function () {
        Route::post('register', [AuthControllerV1::class, 'register']);
        Route::post('login', [AuthControllerV1::class, 'login']);

        Route::get('profile', [AuthControllerV1::class, 'profile'])
            ->middleware(['auth:sanctum',]);
        Route::post('logout', [AuthControllerV1::class, 'logout'])
            ->middleware(['auth:sanctum',]);

    }); 

   
    
  //   Trash routes - must be before the categories routes
    // to avoid conflicts with the apiResource route
    // which is defined below.
Route::middleware('auth:sanctum')
    ->prefix('categories/trash')
    ->name('categories.trash.')
    ->group(function () {
        Route::get('/', [CategoryController::class, 'trash'])
            ->name('index');
        Route::delete('/empty', [CategoryController::class, 'removeAll'])
            ->name('remove.all');
        Route::post('/recover', [CategoryController::class, 'recoverAll'])
            ->name('recover.all');
        Route::delete('/{id}/remove', [CategoryController::class, 'removeOne'])
            ->name('remove.one');
        Route::post('/{id}/recover', [CategoryController::class, 'recoverOne'])
            ->name('recover.one');
    });
        

  //categories
 //Route::group(['prefix' => 'v2'], function () {  
    // Route::apiResource('categories', CategoryController::class)  
    //     ->except(['update','delete','store']);  
  
    // Route::apiResource('categories', CategoryController::class)  
    //     ->only(['update','delete','store']) 
    //     ->middleware('auth:sanctum');  


        Route::apiResource('categories', CategoryController::class)
    ->except(['store','update','destroy']); // public routes

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class)
        ->only(['store','update','destroy']); // protected routes
});


/** Joke Trash Routes */

Route::middleware('auth:sanctum')
    ->prefix('jokes/trash')
    ->name('jokes.trash.')
    ->group(function () {
        Route::get('/', [JokeController::class, 'trash'])
            ->name('index');
        Route::delete('/empty', [JokeController::class, 'removeAll'])
            ->name('remove.all');
        Route::post('/recover-all', [JokeController::class, 'recoverAll'])
            ->name('recover.all');
        Route::delete('/{id}/remove', [JokeController::class, 'removeOne'])
            ->name('remove.one');
        Route::post('/{id}/recover', [JokeController::class, 'recoverOne'])
            ->name('recover.one');
    });


/**
 * Joke API Routes
 * - List, Show (no authentication)
 * - Create, Update, Delete (authentication required)
 */

Route::apiResource('jokes', JokeController::class)
    ->except(['store','update','destroy']); // public routes

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('jokes', JokeController::class)
        ->only(['store','update','destroy']); // protected routes
});


