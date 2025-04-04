<?php

// tests/Feature/RecipeFilterByDifficultyTest.php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeFilterByDifficultyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user for protected routes
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_filter_recipes_by_difficulty()
    {
        Recipe::factory()->create(['difficulty' => 'easy']);
        Recipe::factory()->create(['difficulty' => 'medium']);
        Recipe::factory()->create(['difficulty' => 'hard']);

        $response = $this->getJson('/api/recipes/difficulty/easy');

        $response->assertStatus(200)
            ->assertJsonFragment(['difficulty' => 'easy']);
    }

    public function test_returns_404_if_no_recipes_found()
    {
        $response = $this->getJson('/api/recipes/difficulty/hard');

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'No recipes found for this difficulty.']);
    }

    public function test_returns_400_for_invalid_difficulty()
    {
        $response = $this->getJson('/api/recipes/difficulty/extreme');

        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'Invalid difficulty level. Use easy, medium, or hard.']);
    }
}
