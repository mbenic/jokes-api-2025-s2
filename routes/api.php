<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**API ROutes defined here will be used for the application.
 *  Version ROute filename:        api.php
 * /

/**
 * Fallback route for any routes that do not match the defined API routes.
 * This will return a 404 response.
 */
Route::fallback(static function(){
   return Response::json([
       ['error'=>"OOPS!"]
   ],404);
});


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
