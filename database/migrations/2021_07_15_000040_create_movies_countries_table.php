<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies_countries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('move_id');
            $table->unsignedBigInteger('country_id');
            $table->timestamps();

            $table->foreign('move_id')
                ->references('id')
                ->on('movies')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies_countries');
    }
}
