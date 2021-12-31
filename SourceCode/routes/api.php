<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\ResetPasswordController;
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
Route::post('register', [RegisterController::class, 'Register']);
Route::get('emailConfirmation/{email}/{email_varified_token}', [RegisterController::class, 'VerifyEmail']);

Route::middleware([VerifiedEmail::class])->group(function(){
	Route::post('login', [LoginController::class, 'Login']);
	Route::post('ResetPassword', [ResetPasswordController::class, 'ResetPassword']);
});

Route::middleware([JwtAuth::class])->group(function(){
	Route::post('logout', [LoginController::class, 'Logout']);
	Route::post('CreatePost', [PostController::class, 'CreatePost']);
});
