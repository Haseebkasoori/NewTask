<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CommentController;

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



    //MIDDLEWARE
    Route::middleware([JwtAuth::class])->group(function(){
    //COMMENT SECTION

    //Creat Comment
    Route::post('CreateComment', [CommentController::class, 'CreateComment']);
    //Delete Comment
    Route::delete('DeleteComment/{id}', [CommentController::class, 'DeleteComment'])->middleware('CommentOwnerOrNot::class');
    //Update Comment
    Route::put('updateComment', [CommentController::class, 'updateComment'])->middleware('CommentOwnerOrNot::class');
});
