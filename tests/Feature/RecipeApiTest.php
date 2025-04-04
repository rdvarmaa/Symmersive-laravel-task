<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user for protected routes
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_create_recipe()
    {
        $data = [
            'name' => 'Test Recipe',
            'ingredients' => 'salt, sugar',
            'prep_time' => 5,
            'cook_time' => 10,
            'difficulty' => 'easy',
            'description' => 'Test description',
        ];

        $response = $this->postJson('/api/recipes', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Recipe']);

        $this->assertDatabaseHas('recipes', ['name' => 'Test Recipe']);
    }

    public function test_can_read_recipe()
    {
        $recipe = Recipe::factory()->create();

        $response = $this->getJson('/api/recipes/'.$recipe->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $recipe->name]);
    }

    public function test_can_update_recipe()
    {
        $recipe = Recipe::factory()->create();

        $response = $this->putJson('/api/recipes/'.$recipe->id, [
            'name' => 'Updated Recipe',
            'prep_time' => $recipe->prep_time,
            'cook_time' => $recipe->cook_time,
            'ingredients' => $recipe->ingredients,
            'difficulty' => $recipe->difficulty,
            'description' => $recipe->description,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Recipe updated successfully']);

        $this->assertDatabaseHas('recipes', ['name' => 'Updated Recipe']);
    }

    public function test_can_delete_recipe()
    {
        $recipe = Recipe::factory()->create();

        $response = $this->deleteJson('/api/recipes/'.$recipe->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
    }
}
