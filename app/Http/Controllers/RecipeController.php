<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class RecipeController extends Controller
{
    // gets all recipes
    public function index()
    {
        return response()->json(Recipe::all(), 200);
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
                'description' => 'required|string'
            ]);
            Log::info('Validated Data:', $validated); //
            $recipe = Recipe::create($validated);

            Log::info($recipe);
            return response()->json($recipe, 201);
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
        if (!$recipe) {
            return response()->json(['message' => 'Recipe 4 not found'], 404);
        }
        return response()->json($recipe, 200);
    }

    public function update(Request $request, $id)
    {
        Log::info('Incoming Request Data:');
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return response()->json(['message' => 'Recipe 3 not found'], 404);
        }
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'ingredients' => 'sometimes|string',
                'prep_time' => 'sometimes|integer|min:1',
                'cook_time' => 'sometimes|integer|min:1',
                'difficulty' => ['sometimes', Rule::in(['easy', 'medium', 'hard'])],
                'description' => 'sometimes|string'
            ]);

            $recipe->update($validated);
            return response()->json($recipe, 200);
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
        if (!$recipe) {
            return response()->json(['message' => 'Recipe 1 not found'], 404);
        }
        $recipe->delete();
        return response()->json(['message' => 'Recipe deleted'], 200);
    }

    public function filterByDifficulty($level)
    {
        if (!in_array($level, ['easy', 'medium', 'hard'])) {
            return response()->json(['message' => 'Invalid difficulty level'], 400);
        }

        return response()->json(Recipe::where('difficulty', $level)->get(), 200);
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
                        $q->orWhere('ingredients', 'LIKE', '%' . trim($ingredient) . '%');
                    }
                });
            }

            // Filter by cooking time range
            if ($request->has('min_time') && $request->has('max_time')) {
                $query->whereRaw('(prep_time + cook_time) BETWEEN ? AND ?', [$request->min_time, $request->max_time]);
            }

            $recipes = $query->get();


            Log::info('Search Results:', $recipes->toArray());
            return response()->json($recipes);
        } catch (\Illuminate\Validation\ValidationException $ValidationException) {
            Log::info($ValidationException->errors());
            return response()->json(['errors' => $ValidationException->errors()], 422);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception->getMessage()], 422);
        }
    }
}
