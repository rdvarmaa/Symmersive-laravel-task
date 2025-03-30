<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('user/authenticate', [UserController::class, 'authenticate']);
Route::middleware('auth:sanctum')->group(function () {
   Route::get('recipes/search', [RecipeController::class, 'searchRecipes']);
    Route::get('recipes/difficulty/{level}', [RecipeController::class, 'filterByDifficulty']);
    Route::apiResource('recipes', RecipeController::class);
});
