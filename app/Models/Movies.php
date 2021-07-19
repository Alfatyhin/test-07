<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movies extends Model
{
    use HasFactory;

    protected $fillable = [
        'imdb_title_id',
        'title',
        'year',
        'duration',
        'description',
        'avg_vote',
        'votes',
        'reviews_from_users',
        'reviews_from_critics',
        'is_top',
        'language',
        'genre',
        'country',
        'is_usa',
        'is_europe',
        'director',
        'writer',
        'actors',
    ];
}
