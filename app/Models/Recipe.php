<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'ingredients',
        'prep_time',
        'cook_time',
        'difficulty',
        'description',
    ];

    protected $dates = ['deleted_at'];
}
