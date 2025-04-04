<?php

namespace App\Http\Controllers;

use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RecipeController extends Controller
{
    // gets all recipes
    public function index()
    {
        return RecipeResource::collection(Recipe::all());
    }

    public function store(Request $request)
    {
        Log::info('Incoming Request Data:', $request->all());
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'ingredients' => 'required|string',
                'prep_time' => 'required|integer|min:1',
                'cook_time' => 'required|integer|min:1',
                'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
                'description' => 'required|string',
            ]);
            Log::info('Validated Data:', $validated); //
            $recipe = Recipe::create($validated);

            Log::info($recipe);

            return response()->json([
                'message' => 'Recipe created successfully',
                'recipe' => new RecipeResource($recipe),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $ValidationException) {
            Log::info($ValidationException->errors());

            return response()->json(['errors' => $ValidationException->errors()], 422);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $recipe = Recipe::find($id);
        if (! $recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        return new RecipeResource($recipe);
    }

    public function update(Request $request, $id)
    {
        Log::info('Incoming Request Data:');
        $recipe = Recipe::find($id);
        if (! $recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'ingredients' => 'sometimes|string',
                'prep_time' => 'sometimes|integer|min:1',
                'cook_time' => 'sometimes|integer|min:1',
                'difficulty' => ['sometimes', Rule::in(['easy', 'medium', 'hard'])],
                'description' => 'sometimes|string',
            ]);

            $recipe->update($validated);

            return response()->json([
                'message' => 'Recipe updated successfully',
                'recipe' => new RecipeResource($recipe),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $ValidationException) {
            Log::info($ValidationException->errors());

            return response()->json(['errors' => $ValidationException->errors()], 422);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        Log::info('Incoming Request Data:');
        $recipe = Recipe::find($id);
        if (! $recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }
        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted'], 200);
    }

    public function filterByDifficulty($level)
    {

        if (! $level || ! in_array($level, ['easy', 'medium', 'hard'])) {
            return response()->json([
                'message' => 'Invalid difficulty level. Use easy, medium, or hard.',
            ], 400);
        }

        $recipes = Recipe::where('difficulty', $level)->get();

        if ($recipes->isEmpty()) {
            return response()->json([
                'message' => 'No recipes found for this difficulty.',
            ], 404);
        }

        return RecipeResource::collection($recipes);
    }

    public function searchRecipes(Request $request)
    {
        Log::info('Incoming Request Data:', $request->all());
        try {

            $validated = $request->validate([
                'ingredients' => 'sometimes|string',
                'min_time' => 'sometimes|integer|min:1',
                'max_time' => 'sometimes|integer|min:1',
            ]);
            Log::info('Validated Data:', $validated);
            $query = Recipe::query();
            // Filter by ingredients (comma-separated string)
            if ($request->has('ingredients')) {
                $ingredients = explode(',', $request->ingredients);
                $query->where(function ($q) use ($ingredients) {
                    foreach ($ingredients as $ingredient) {
                        $q->orWhere('ingredients', 'LIKE', '%'.trim($ingredient).'%');
                    }
                });
            }

            // Filter by cooking time range
            if ($request->has('min_time') && $request->has('max_time')) {
                $query->whereRaw('(prep_time + cook_time) BETWEEN ? AND ?', [$request->min_time, $request->max_time]);
            }

            $recipes = $query->get();

            Log::info('Search Results:', $recipes->toArray());
            if ($recipes->isEmpty()) {
                return response()->json([
                    'message' => 'No recipes found for this difficulty.',
                ], 404);
            }

            return RecipeResource::collection($recipes);
        } catch (\Illuminate\Validation\ValidationException $ValidationException) {
            Log::info($ValidationException->errors());

            return response()->json(['errors' => $ValidationException->errors()], 422);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception->getMessage()], 422);
        }
    }
}
