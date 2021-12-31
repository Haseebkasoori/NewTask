<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FriendRequstController;

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

    //MIDDLEWARE Checking Reqeust, valid or not
//CREATE FRIEND REQEUST
Route::post('SendFriendRequest', [FriendRequstController::class, 'SendFriendRequest'])->middleware(['JwtAuth','FriendReqeustSendOrNot']);

Route::put('FriendRequestUpdate', [FriendRequstController::class, 'UpdateFriendRequest'])->middleware(['JwtAuth','FriendRequetPendingOrNot']);
