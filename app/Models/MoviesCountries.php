<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoviesCountries extends Model
{
    use HasFactory;

    protected $fillable = ['move_id', 'country_id'];
}
