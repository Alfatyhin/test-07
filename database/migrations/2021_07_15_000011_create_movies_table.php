<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('imdb_title_id')->unique();
            $table->string('title');
            $table->smallInteger('year');
            $table->json('genre')->nullable();
            $table->smallInteger('duration');
            $table->bigInteger('country')->nullable();
            $table->json('language')->nullable();
            $table->json('director')->nullable();
            $table->json('writer')->nullable();
            $table->json('actors')->nullable();
            $table->text('description');
            $table->float('avg_vote', 3, 1);
            $table->integer('votes');
            $table->string('reviews_from_users')->nullable();
            $table->string('reviews_from_critics')->nullable();
            $table->boolean('is_usa')->default(false);
            $table->boolean('is_europe')->default(false);
            $table->boolean('is_top')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
}
