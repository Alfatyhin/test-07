<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldMovies extends Model
{
    use HasFactory;

    protected $fillable = ['move_id', 'year'];
}
