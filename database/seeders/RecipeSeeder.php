<?php

namespace Database\Seeders;

use App\Models\Recipe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Seeding');
        $json = file_get_contents(database_path('seed_data/recipe.json')); // get contents from sample data file
        Log::info($json);
        $recipes = json_decode($json);
        Log::info($recipes);
        foreach ($recipes as $recipe) {
            Log::info($recipe->ingredients);
            Recipe::create([
                'name' => $recipe->name,
                'ingredients' => $recipe->ingredients,
                'prep_time' => $recipe->prep_time,
                'cook_time' => $recipe->cook_time,
                'difficulty' => $recipe->difficulty,
                'description' => $recipe->description,
            ]);
        }
    }
}
