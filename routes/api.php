<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('recipes/search', [RecipeController::class, 'searchRecipes']);
Route::get('recipes/difficulty/{level}', [RecipeController::class, 'filterByDifficulty']);
Route::apiResource('recipes', RecipeController::class); 