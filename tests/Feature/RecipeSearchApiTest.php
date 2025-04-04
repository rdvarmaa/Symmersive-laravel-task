<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeSearchApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user for protected routes
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_search_recipes_with_ingredients_min_and_max_time()
    {
        Recipe::factory()->create([
            'name' => 'Sambar',
            'ingredients' => 'curry leaves, mustard seeds, lentils',
            'prep_time' => 5,
            'cook_time' => 10,
        ]);

        Recipe::factory()->create([
            'name' => 'Upma',
            'ingredients' => 'semolina, mustard seeds, curry leaves',
            'prep_time' => 2,
            'cook_time' => 5,
        ]);

        Recipe::factory()->create([
            'name' => 'Biryani',
            'ingredients' => 'rice, chicken, spices',
            'prep_time' => 30,
            'cook_time' => 45,
        ]);

        $response = $this->getJson('/api/recipes/search?ingredients=curry leaves,mustard seeds&min_time=5&max_time=20');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Sambar']);
        $response->assertJsonFragment(['name' => 'Upma']);
        $response->assertJsonMissing(['name' => 'Biryani']);
    }

    public function test_search_with_only_ingredients()
    {
        Recipe::factory()->create([
            'name' => 'Tomato Rice',
            'ingredients' => 'tomato, rice, curry leaves',
            'prep_time' => 5,
            'cook_time' => 15,
        ]);

        $response = $this->getJson('/api/recipes/search?ingredients=rice');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Tomato Rice']);
    }

    public function test_search_with_only_time_filters()
    {
        Recipe::factory()->create([
            'name' => 'Quick Fry',
            'ingredients' => 'onion, spices',
            'prep_time' => 2,
            'cook_time' => 3,
        ]);

        Recipe::factory()->create([
            'name' => 'Slow Cooked Curry',
            'ingredients' => 'lamb, spices',
            'prep_time' => 15,
            'cook_time' => 60,
        ]);

        $response = $this->getJson('/api/recipes/search?min_time=4&max_time=10');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Quick Fry']);
        $response->assertJsonMissing(['name' => 'Slow Cooked Curry']);
    }

    public function test_search_returns_404_if_no_results()
    {
        Recipe::factory()->create([
            'name' => 'Pongal',
            'ingredients' => 'rice, dal, ghee',
            'prep_time' => 10,
            'cook_time' => 15,
        ]);

        $response = $this->getJson('/api/recipes/search?ingredients=pizza');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'No recipes found for this search.']);
    }

    public function test_search_fails_validation_with_invalid_time_input()
    {
        $response = $this->getJson('/api/recipes/search?min_time=abc');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['min_time']);
    }
}
