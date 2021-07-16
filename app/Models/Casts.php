<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casts extends Model
{
    use HasFactory;

    protected $fillable = [
        'imdb_name_id',
        'name',
        'height',
        'bio',
        'date_of_birth',
        'place_of_birth',
        'children',
        'is_usa',
        'is_europe'
    ];
}
