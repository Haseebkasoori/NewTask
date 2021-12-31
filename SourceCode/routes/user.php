<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EmailVarified;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\userController;

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

    //REGISTER
    Route::post('register', [UserController::class, 'register']);

    //MIDDLEWARE
    Route::middleware([VerifiedEmail::class])->group(function(){
        //LOGIN
        Route::post('login', [UserController::class, 'login']);
        //FORGOT PASSWORD
        Route::get('/forgotPassword', [UserController::class, 'ResetPassword']);
    });

    //EMAIL VERIFY
    Route::get('emailConfirmation/{email}/{token}', [UserController::class, 'verifyingEmail']);

    //MIDDLEWARE Checking Reqeust, valid or not
Route::middleware([JwtAuth::class])->group(function(){

    //Update
    Route::put('updateUser', [UserController::class, 'UpdateUser']);
    //Update Password
    Route::put('updateUserPassword', [UserController::class, 'updateUserPassword']);
    //TwoFactorAuth
    Route::post('TwoFactorAuth', [UserController::class, 'TwoFactorAuth'])->middleware([TwoFactorAuth::class]);
    //KYC
    Route::post('UserKYC', [UserController::class, 'UserKYC']);
    //Delete
    Route::delete('deleteUser', [UserController::class, 'DeleteUser']);
 //LOGOUT
    Route::get('logout', [UserController::class, 'logout']);
});
