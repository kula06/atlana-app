<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\GithubController;

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

Route::get('/search', [GithubController::class, 'searchUsers']);
Route::get('/user/{username}', [GithubController::class, 'userInfo']);
Route::get('/user/{username}/repositories', [GithubController::class, 'userRepositories']);
Route::get('/users', [GithubController::class, 'getUsers']);
Route::get('/most_popular_users', [GithubController::class, 'mostPopularUsers']);
