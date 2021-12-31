<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
    //Create Post
    Route::post('createPost', [PostController::class, 'CreatePost'])->middleware('JwtAuth');

    //MIDDLEWARE
    //Get specific Post
    Route::get('GetPost', [PostController::class, 'GetPost'])->middleware(['JwtAuth','FriendsOrNot']);

    Route::middleware(['JwtAuth','PostOwnerOrNot'])->group(function(){
        //Delete Post
        Route::delete('delete', [PostController::class, 'DeletePost']);
        //Update Post
        Route::put('update', [PostController::class, 'UpdatePost']);
    });
