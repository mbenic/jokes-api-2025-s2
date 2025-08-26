<?php

//https://github.com/AdyGCode/jokes-api-2025-s2

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;

use App\http\Controllers\Admin\AdminJokeController;
use App\http\Controllers\Admin\AdminCategoryController;

Route::get('/', [StaticPageController::class, 'home'])
    ->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('dashboard');
});

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])
            ->name('index');

        Route::get('users', [AdminController::class, 'users'])
            ->name('users.index');

   
        
         /* Categories Admin Routes ------------------------------------------------------ */
        Route::get('categories/trash', [AdminCategoryController::class, 'trash'])
            ->name('categories.trash');

        Route::delete('categories/trash/empty', [AdminCategoryController::class, 'removeAll'])
            ->name('categories.trash.remove.all');

        Route::post('categories/trash/recover', [AdminCategoryController::class, 'recoverAll'])
            ->name('categories.trash.recover.all');

        Route::delete('categories/trash/{id}/remove', [AdminCategoryController::class, 'removeOne'])
            ->name('categories.trash.remove.one');

        Route::post('categories/trash/{id}/recover', [AdminCategoryController::class, 'recoverOne'])
            ->name('categories.trash.recover.one');

        /** Stop people trying to "GET" admin/categories/trash/1234/delete or similar */
        Route::get('categories/trash/{id}/{method}', [AdminCategoryController::class, 'trash']);

    
   
     Route::post('categories/{category}/delete', [AdminCategoryController::class, 'delete'])
            ->name('categories.delete');
    
     Route::get('categories/{category}/delete', function () {
            return redirect()->route('admin.categories.index');
        });

    // Resource route for categories
    Route::resource("categories", AdminCategoryController::Class);


    // JOKES ADMIN ROUTES ------------------------------------------------------
    Route::get('jokes/trash', [AdminJokeController::class, 'trash'])
        ->name('jokes.trash');  

    Route::delete('jokes/trash/empty', [AdminJokeController::class, 'removeAll'])
            ->name('jokes.trash.remove.all');
   
    // Route::delete('jokes/trash/empty', [AdminJokeController::class, 'empty'])
    //     ->name('jokes.trash.empty');
    
    Route::post('jokes/trash/recover', [AdminJokeController::class, 'recoverAll'])
        ->name('jokes.trash.recover.all');
    Route::delete('jokes/trash/{id}/remove', [AdminJokeController::class, 'removeOne'])
        ->name('jokes.trash.remove.one');
    Route::post('jokes/trash/{id}/recover', [AdminJokeController::class, 'restore'])
        ->name('jokes.trash.restore.one');      
    /** Stop people trying to "GET" admin/jokes/trash/1234/delete or similar */
    Route::get('jokes/trash/{id}/{method}', [AdminJokeController::class, 'trash']);     


    
    Route::post('jokes/{joke}/delete', [AdminJokeController::class, 'delete'])
            ->name('jokes.delete');
    
     Route::get('jokes/{joke}/delete', function () {
            return redirect()->route('admin.jokes.index');
        });

    // Resource route for jokes
    Route::resource("jokes", AdminJokeController::Class);

    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
